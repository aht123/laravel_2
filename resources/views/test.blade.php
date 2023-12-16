
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Data Example</title>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
<div>
    <label for="startDatetime">Start Datetime:</label>
    <input type="datetime-local" id="startDatetime" name="startDatetime">
    <label for="endDatetime">End Datetime:</label>
    <input type="datetime-local" id="endDatetime" name="endDatetime">
    <label>Meter Tags:</label>
    <div>
        <input type="checkbox" id="meterU2Frequency" name="meters[]" value="U_12_Frequency_HZ">
        <label for="meterU2Frequency">U_12_Frequency_HZ</label>
    </div>
    <div>
        <input type="checkbox" id="meterU2VoltageLine1" name="meters[]" value="U_2_VOLTAGE_LINE_1_V">
        <label for="meterU2VoltageLine1">U_2_VOLTAGE_LINE_1_V</label>
    </div>
    <div>
        <input type="checkbox" id="meterU2VoltageLine2" name="meters[]" value="U_2_VOLTAGE_LINE_2_V">
        <label for="meterU2VoltageLine2">U_2_VOLTAGE_LINE_2_V</label>
    </div>
    <button onclick="fetchData()">Fetch Data</button>
</div>
<div id="result"></div>
<script>
function fetchData() {
    // Get values from input fields
    var startDatetime = $('#startDatetime').val();
    var endDatetime = $('#endDatetime').val();
    var meterTags = $('input[name="meters[]"]:checked').map(function(){
        return $(this).val();
    }).get().join(',');
    // Log the selected meter tags to the console
    console.log('Selected Meter Tags:', meterTags);
    // Make an AJAX request to the Laravel backend
    $.ajax({
        type: 'GET',
        url: '/api/fetch-data_2',
        data: {
            start_datetime: startDatetime,
            end_datetime: endDatetime,
            meter_tags: meterTags
        },
        success: function (data) {
            // Create an HTML table and append it to the #result div
            var tableHtml = '<table border="1">';
            // Add header row
            tableHtml += '<tr>';
            for (var tag in data[0]) {
                tableHtml += '<th>' + tag + '</th>';
            }
            tableHtml += '</tr>';
            // Add data rows
            for (var i = 0; i < data.length; i++) {
                tableHtml += '<tr>';
                for (var tag in data[i]) {
                    tableHtml += '<td>' + data[i][tag] + '</td>';
                }
                tableHtml += '</tr>';
            }
            tableHtml += '</table>';
            // Display the table in the #result div
            $('#result').html(tableHtml);
        },
        error: function (error) {
            // Handle errors if any
            console.error('Error fetching data:', error);
        }
    });
}
</script>
</body>
</html>
