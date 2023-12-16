<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Data</title>
</head>
<body>
    <h1>Fetch Data by Date and Time (UTC)</h1>
    <label for="start_datetime">Start Datetime (UTC):</label>
    <input type="datetime-local" id="start_datetime" name="start_datetime"><br><br>
    <label for="end_datetime">End Datetime (UTC):</label>
    <input type="datetime-local" id="end_datetime" name="end_datetime"><br><br>
    <button onclick="fetchData()">Fetch Data</button>
    <div id="result"></div>
    <script>
        function fetchData() {
            const startDatetime = document.getElementById("start_datetime").value;
            const endDatetime = document.getElementById("end_datetime").value;
            
            // Convert local time to UTC format
            const startDatetimeUTC = new Date(startDatetime).toISOString();
            const endDatetimeUTC = new Date(endDatetime).toISOString();
            
            // Make a request to your API with the UTC datetime values
            fetch(`http://127.0.0.1:8000/api/fetch-users?start_datetime=${startDatetime}&end_datetime=${endDatetime}`)
                .then(response => response.json())
                .then(data => {
                    // Handle the data returned from the API
                    displayData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        function displayData(data) {
            const resultDiv = document.getElementById("result");
            resultDiv.innerHTML = "<h2>Results:</h2>";
            if (data.length === 0) {
                resultDiv.innerHTML += "No data found for the selected date range.";
            } else {
                // Display the data and calculate energy consumption
                const list = document.createElement("ul");
                let initialMeterValue = null;
                let finalMeterValue = null;

                data.forEach(item => {
                    const listItem = document.createElement("li");
                    listItem.textContent = `Time: ${item.Time}, METER: ${item.U_2_VOLTAGE_LINE_1_V}`;

                    if (initialMeterValue === null) {
                        initialMeterValue = item.U_2_VOLTAGE_LINE_1_V;
                    }
                    finalMeterValue = item.U_2_VOLTAGE_LINE_1_V;

                    list.appendChild(listItem);
                });

                resultDiv.appendChild(list);

                // Calculate energy consumption
                if (initialMeterValue !== null && finalMeterValue !== null) {
                    const consumption = finalMeterValue - initialMeterValue;
                    resultDiv.innerHTML += `<h1>Energy Consumption: ${consumption} kWh</h1>`;
                }
            }
        }
    </script>
</body>
</html>
