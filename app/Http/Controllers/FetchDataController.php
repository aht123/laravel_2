<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class FetchDataController extends Controller
{
    public function fetchData()
    {              // Connect to the MongoDB database
        $mongoDB = DB::connection('mongodb');

        // Get the start datetime, end datetime, and selected meter tags from the query parameters in the URL
        $startDatetime = request()->input('start_datetime');
        $endDatetime = request()->input('end_datetime');
        $meterTags = request()->input('meter_tags');

        // Check if $meterTags is not null before proceeding
        if (!is_null($meterTags)) {
            // Specify the collection you want to query (replace 'meters_data_1' with your actual collection name)
            $collection = $mongoDB->collection('naubahar_activetags');

            // Create an empty array to hold the $or conditions
            $orConditions = [];

            // Split the meter tags by comma and loop through them
            $tags = explode(',', $meterTags);
            foreach ($tags as $tag) {
                $orConditions[] = [
                    $tag => ['$exists' => true],
                ];
            }

            // Build the query with $or conditions and date range
            $query = [
                '$or' => $orConditions,
                'Time' => [
                    '$gte' => $startDatetime,
                    '$lte' => $endDatetime,
                ],
            ];

            // Add pagination parameters to the request
            $page = request()->input('page', 1);
            $perPage = 4500; // Adjust the number of documents per page based on your needs

            // Calculate the skip value based on the current page and per page count
            $skip = ($page - 1) * $perPage;

            // Fetch data that matches the date range and the selected meter tags with pagination
            $meters = $collection->where($query)->skip($skip)->limit($perPage)->get();

            // Calculate energy consumption from the last and first values
            $lastValue = end($meters) ?: null;
            $firstValue = reset($meters) ?: null;
            $energyConsumption = null;
            if ($lastValue && $firstValue) {
                // Assuming 'Energy' is the field representing energy in your collection
                $energyConsumption = $lastValue['Energy'] - $firstValue['Energy'];
            }

            // Prepare the output array
            $outputData = [];
            foreach ($meters as $meter) {
                $outputMeter = [
                    'Time' => $meter['Time'],
                ];
                foreach ($tags as $tag) {
                    // Only include the selected meter data
                    if (isset($meter[$tag])) {
                        $outputMeter[$tag] = $meter[$tag];
                    }
                }
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
        } 
        else {
            // Handle the case where $meterTags is null (not selected by the user)
            return response()->json(['message' => 'No meter tags selected.']);
        }
    }
}
