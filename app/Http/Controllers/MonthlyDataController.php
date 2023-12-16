<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyDataController extends Controller
{
    public function fetchMonthlyData()
    {
        // Connect to the MongoDB database
        $mongoDB = DB::connection('mongodb');
    
        // Set the selected date to the first day of the current month
        $selectedDate = date('2023-10-01');
        $meterTag = "U_9_VoltageAB_Volt"; // Specific meter tag
    
        // Check if $meterTag is not null before proceeding
        if (!is_null($meterTag)) {
            // Specify the collection you want to query (replace 'meters_data_1' with your actual collection name)
            $collection = $mongoDB->collection('meter_data_2');
    
            // Calculate the start and end dates for the current month
            $startDate = date('Y-m-01', strtotime($selectedDate));
            $endDate = date('Y-m-t', strtotime($selectedDate));
    
          dd($startDate, $endDate);
            // Build the query with the specific meter tag and date range for the current month
            $query = [
                $meterTag => ['$exists' => true],
                'Time' => [
                    '$gte' => $startDate,
                    '$lt' => $endDate,
                ],
            ];
    
            // Add pagination parameters to the request
            $page = request()->input('page', 1);
            $perPage = 4500; // Adjust the number of documents per page based on your needs
    
            // Calculate the skip value based on the current page and per page count
            $skip = ($page - 1) * $perPage;
    
            // Fetch data that matches the date range and the selected meter tag with pagination
            $meters = $collection->where($query)->skip($skip)->limit($perPage)->get();
    
            // Calculate energy consumption from the last and first values
            $lastValue = end($meters) ?: null;
            $firstValue = reset($meters) ?: null;
            $energyConsumption = null;
            if ($lastValue && $firstValue) {
                $energyConsumption = $lastValue['Energy'] - $firstValue['Energy'];
            }
    
            // Prepare the output array
            $outputData = [];
            foreach ($meters as $meter) {
                $outputMeter = [
                    'Time' => $meter['Time'],
                    $meterTag => $meter[$meterTag],
                ];
                $outputData[] = $outputMeter;
            }
    
            // Return the results as JSON along with pagination information and energy consumption
            return response()->json([
                'data' => $outputData,
                'page' => $page,
                'perPage' => $perPage,
                'total' => $collection->where($query)->count(),
                'energyConsumption' => $energyConsumption,
            ]);
        } else {
            // Handle the case where $meterTag is null (not selected by the user)
            return response()->json(['message' => 'No meter tag selected.']);
        }
    }
     //
}
