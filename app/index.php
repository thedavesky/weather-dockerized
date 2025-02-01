<!DOCTYPE html>
<html>
<head>
    <title>Weather Data Plot</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Weather Data Plot</h1>
    <div>
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" value="2024-01-01" min="2024-01-01" max="2024-05-18">
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" value="2024-01-31" min="2024-01-01" max="2024-05-18">
        <button id="plot_button">Plot Data</button>
    </div>
    <div>
        <canvas id="temperature_chart" width="400" height="200"></canvas>
        <canvas id="humidity_chart" width="400" height="200"></canvas>
        <canvas id="precipitation_chart" width="400" height="200"></canvas>
        <canvas id="wind_speed_chart" width="400" height="200"></canvas>
    </div>

    <script>
        // Get the data from the PHP file
        function getData() {
            var startDate = document.getElementById("start_date").value;
            var endDate = document.getElementById("end_date").value;
            var url = "weather_data.php?start_date=" + startDate + "&end_date=" + endDate;
            fetch(url)
                .then(response => response.json())
                .then(data => plotData(data));
        }

        // Plot the data
        function plotData(data) {
            // Create the temperature chart
            var temperatureChart = document.getElementById("temperature_chart").getContext("2d");
            var temperatureData = data.map(function(row) {
                return row.Temperature_C;
            });
            var temperatureLabels = data.map(function(row) {
                return row.Date_Time;
            });
            new Chart(temperatureChart, {
                type: "line",
                data: {
                    labels: temperatureLabels,
                    datasets: [{
                        label: "Temperature (C)",
                        data: temperatureData,
                        backgroundColor: "rgba(255, 99, 132, 0.2)",
                        borderColor: "rgba(255, 99, 132, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Create the humidity chart
            var humidityChart = document.getElementById("humidity_chart").getContext("2d");
            var humidityData = data.map(function(row) {
                return row.Humidity_pct;
            });
            new Chart(humidityChart, {
                type: "line",
                data: {
                    labels: temperatureLabels,
                    datasets: [{
                        label: "Humidity (%)",
                        data: humidityData,
                        backgroundColor: "rgba(54, 162, 235, 0.2)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Create the precipitation chart
            var precipitationChart = document.getElementById("precipitation_chart").getContext("2d");
            var precipitationData = data.map(function(row) {
                return row.Precipitation_mm;
            });
            new Chart(precipitationChart, {
                type: "line",
                data: {
                    labels: temperatureLabels,
                    datasets: [{
                        label: "Precipitation (mm)",
                        data: precipitationData,
                        backgroundColor: "rgba(255, 206, 86, 0.2)",
                        borderColor: "rgba(255, 206, 86, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Create the wind speed chart
            var windSpeedChart = document.getElementById("wind_speed_chart").getContext("2d");
            var windSpeedData = data.map(function(row) {
                return row.Wind_Speed_kmh;
            });
            new Chart(windSpeedChart, {
                type: "line",
                data: {
                    labels: temperatureLabels,
                    datasets: [{
                        label: "Wind Speed (km/h)",
                        data: windSpeedData,
                        backgroundColor: "rgba(75, 192, 192, 0.2)",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Add event listener to the plot button
        document.getElementById("plot_button").addEventListener("click", getData);
    </script>
</body>
</html>
