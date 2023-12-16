<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Jenssegers\Mongodb\Facades\MongoDB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('
', function () {
    // Connect to the MongoDB database
    $mongoDB = DB::connection('mongodb');
    // Get the start datetime, end datetime, and selected meter tags from the query parameters in the URL
    $startDatetime = request()->input('start_datetime');
    $endDatetime = request()->input('end_datetime');
    $meterTags = request()->input('meter_tags');
    // Check if $meterTags is not null before proceeding
    if (!is_null($meterTags)) {
        // Specify the collection you want to query (replace 'meters_data_1' with your actual collection name)
        $collection = $mongoDB->collection('meters_data_1');
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
        // Fetch data that matches the date range and the selected meter tags without pagination
        $meters = $collection->where($query)->get();
        // Return the results as JSON
        return response()->json($meters);
    } else {
        // Handle the case where $meterTags is null (not selected by the user)
        return response()->json(['message' => 'No meter tags selected.']);
    }
});
