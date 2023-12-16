<?php
use Illuminate\Support\Facades\Route;
use Jenssegers\Mongodb\Facades\MongoDB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
/*
|-----------------------------------------------------------------------
| Web Routes
|------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('test');
});
Route::get('/test-mongodb-connection', function () {
    try {
        DB::connection()->getDatabaseName();
        return "MongoDB connection successful!";
    } catch (\Exception $e) {
        return "MongoDB connection failed: " . $e->getMessage();
    }
});
use App\Http\Controllers\UserController;
Route::post('/users',[UserController::class, 'store'])->name('users.store');
Route::get('/users', [UserController::class, 'store'])->name('users.store');
use Illuminate\Http\Request;
Route::get('/api/fetch-data', function () {
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
        // Filter only the selected meter data
        $filteredMeters = [];
        foreach ($meters as $meter) {
            $filteredMeter = [];
            foreach ($tags as $tag) {
                // Only include the selected meter data
                if (isset($meter[$tag])) {
                    $filteredMeter[$tag] = $meter[$tag];
                }
            }
            $filteredMeters[] = $filteredMeter;
        }
        // Return the results as JSON
        return response()->json($filteredMeters);
    } else {
        // Handle the case where $meterTags is null (not selected by the user)
        return response()->json(['message' => 'No meter tags selected.']);
    }
});
Route::get('/api/fetch-data_1', function () {
    // Connect to the MongoDB database
    $mongoDB = DB::connection('mongodb');
    // Get the start datetime, end datetime, and selected meter tags from the query parameters in the URL
    $startDatetime = request()->input('start_datetime');
    $endDatetime = request()->input('end_datetime');
    $meterTags = request()->input('meter_tags');
    // Check if $meterTags is not null before proceeding
    if (!is_null($meterTags)) {
        // Specify the collection you want to query (replace 'meters_data_1' with your actual collection name)
        $collection = $mongoDB->collection('naubahar_activetags');
        dd($collection);
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
        $perPage = 100;
         // Adjust the number of documents per page based on your needs
        // Calculate the skip value based on the current page and per page count
        $skip = ($page - 1) * $perPage;
        // Fetch data that matches the date range and the selected meter tags with pagination
        $meters = $collection->where($query)->skip($skip)->limit($perPage)->get();
        // Prepare the output array
        $outputData = [];
        foreach ($meters as $meter) {
            $outputMeter = [
                'Time' => $meter['Time'], // Include the 'Time' field in the output
            ];
            foreach ($tags as $tag) {
                // Only include the selected meter data
                if (isset($meter[$tag])) {
                    $outputMeter[$tag] = $meter[$tag];
                }
            }
            $outputData[] = $outputMeter;
        }
        // Return the results as JSON along with pagination information
        return response()->json([
            'data' => $outputData,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $collection->where($query)->count(),
        ]);
    } else {
        // Handle the case where $meterTags is null (not selected by the user)
        return response()->json(['message' => 'No meter tags selected.']);
    }
});
use App\Http\Controllers\FetchDataController;
Route::get('/api/fetch-data_2', [FetchDataController::class, 'fetchData']);
use App\Http\Controllers\WeeklyDataController;
Route::get('/api/fetch-weekly-data', [WeeklyDataController::class, 'fetchWeeklyData']);
use App\Http\Controllers\MonthlyDataController;
Route::get('/api/fetch-monthly-data', [MonthlyDataController::class, 'fetchMonthlyData']);
use App\Http\Controllers\YearlyDataController;
Route::get('/api/fetch-yearly-data', [YearlyDataController::class, 'fetchYearlyData']);
