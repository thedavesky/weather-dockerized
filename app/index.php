<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Wizualizacja Danych Meteorologicznych</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      padding-top: 20px;
      background-color: #f8f9fa;
    }
    .spinner-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(255, 255, 255, 0.7);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="mb-4 text-center">Wizualizacja Danych Meteorologicznych</h1>
    
    <!-- Panel wyboru zakresu dat -->
    <div class="card mb-4">
      <div class="card-body">
        <form id="dataForm" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label for="start_date" class="form-label">Data początkowa:</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="2024-01-01" min="2019-01-01" max="2024-08-20" required>
          </div>
          <div class="col-md-4">
            <label for="end_date" class="form-label">Data końcowa:</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="2024-01-31" min="2019-01-01" max="2024-08-20" required>
          </div>
          <div class="col-md-4">
            <button type="submit" id="plot_button" class="btn btn-primary w-100">Wczytaj dane i narysuj wykresy</button>
          </div>
        </form>
      </div>
    </div>

  <!-- Miejsce na wykresy -->
  <div class="row">
    <div class="col-md-12 mb-4" >
      <div class="card" style="height: 500px">
        <div class="card-header">Temperatura (°C)</div>
        <div class="card-body">
          <canvas id="temperature_chart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-12 mb-4" >
      <div class="card" style="height: 500px">
        <div class="card-header">Wilgotność (%)</div>
        <div class="card-body">
          <canvas id="humidity_chart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-12 mb-4" >
      <div class="card" style="height: 500px">
        <div class="card-header">Opady (mm)</div>
        <div class="card-body">
          <canvas id="precipitation_chart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-12 mb-4" >
      <div class="card" style="height: 500px">
        <div class="card-header">Prędkość wiatru (km/h)</div>
        <div class="card-body">
          <canvas id="wind_speed_chart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-12 mb-4" >
      <div class="card" style="height: 500px">
        <div class="card-header">Ciśnienie (hPa)</div>
        <div class="card-body">
          <canvas id="pressure_chart"></canvas>
        </div>
      </div>
    </div>
  </div>


  <!-- Spinner podczas ładowania -->
  <div id="spinner" class="spinner-overlay" style="display: none;">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Ładowanie...</span>
    </div>
  </div>

  <!-- Bootstrap JS (wymaga Poppera) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Skrypt do obsługi wykresów i pobierania danych -->
  <script>
    // Globalne zmienne dla wykresów
    let temperatureChart, humidityChart, precipitationChart, windSpeedChart, pressureChart;

    // Funkcja do tworzenia lub aktualizacji wykresu
    function renderChart(chartInstance, ctx, config) {
      if (chartInstance) {
        chartInstance.destroy();
      }
      return new Chart(ctx, config);
    }

    // Funkcja pobierająca dane i rysująca wykresy
    async function fetchAndRenderData(event) {
      event.preventDefault();
      
      // Wyświetl spinner
      document.getElementById('spinner').style.display = 'flex';
      
      const startDate = document.getElementById("start_date").value;
      const endDate = document.getElementById("end_date").value;
      const url = `weather_data.php?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

      try {
        const response = await fetch(url);
        if (!response.ok) {
          throw new Error(`Błąd sieci: ${response.status}`);
        }
        const data = await response.json();

        if (!Array.isArray(data)) {
          throw new Error('Niepoprawny format danych');
        }

        // Przygotuj etykiety (daty) oraz dane dla wykresów
        const labels = data.map(row => row.DATE_VALID_STD);
        const temperatureData = data.map(row => parseFloat(row.AVG_TEMPERATURE_AIR_2M_C));
        const humidityData = data.map(row => parseFloat(row.AVG_HUMIDITY_RELATIVE_2M_PCT));
        const precipitationData = data.map(row => parseFloat(row.TOT_PRECIPITATION_MM));
        const windSpeedData = data.map(row => parseFloat(row.AVG_WIND_SPEED_10M_KPH));
        const pressureData = data.map(row => parseFloat(row.AVG_PRESSURE_2M_MB));

        // Konfiguracja wykresów
        const commonOptions = {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        };

        // Wykres temperatury
        const temperatureConfig = {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Temperatura (°C)',
              data: temperatureData,
              backgroundColor: 'rgba(255, 99, 132, 0.2)',
              borderColor: 'rgba(255, 99, 132, 1)',
              borderWidth: 1,
              fill: true,
              tension: 0.2
            }]
          },
          options: commonOptions
        };
        const tempCtx = document.getElementById('temperature_chart').getContext('2d');
        temperatureChart = renderChart(temperatureChart, tempCtx, temperatureConfig);

        // Wykres wilgotności
        const humidityConfig = {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Wilgotność (%)',
              data: humidityData,
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1,
              fill: true,
              tension: 0.2
            }]
          },
          options: commonOptions
        };
        const humCtx = document.getElementById('humidity_chart').getContext('2d');
        humidityChart = renderChart(humidityChart, humCtx, humidityConfig);

        // Wykres opadów
        const precipitationConfig = {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Opady (mm)',
              data: precipitationData,
              backgroundColor: 'rgba(255, 206, 86, 0.2)',
              borderColor: 'rgba(255, 206, 86, 1)',
              borderWidth: 1,
              fill: true,
              tension: 0.2
            }]
          },
          options: commonOptions
        };
        const precCtx = document.getElementById('precipitation_chart').getContext('2d');
        precipitationChart = renderChart(precipitationChart, precCtx, precipitationConfig);

        // Wykres prędkości wiatru
        const windSpeedConfig = {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Prędkość wiatru (km/h)',
              data: windSpeedData,
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 1,
              fill: true,
              tension: 0.2
            }]
          },
          options: commonOptions
        };
        const windCtx = document.getElementById('wind_speed_chart').getContext('2d');
        windSpeedChart = renderChart(windSpeedChart, windCtx, windSpeedConfig);

                // Wykres ciśnienia
        const pressureConfig = {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Ciśnienie (hPa)',
              data: pressureData,
              backgroundColor: 'rgba(153, 102, 255, 0.2)',
              borderColor: 'rgba(153, 102, 255, 1)',
              borderWidth: 1,
              fill: true,
              tension: 0.2
            }]
          },
          options: commonOptions
        };
        const pressCtx = document.getElementById('pressure_chart').getContext('2d');
        pressureChart = renderChart(pressureChart, pressCtx, pressureConfig);


      } catch (error) {
        console.error('Wystąpił błąd:', error);
        alert("Wystąpił błąd podczas pobierania danych. Sprawdź konsolę przeglądarki dla szczegółów.");
      } finally {
        // Ukryj spinner
        document.getElementById('spinner').style.display = 'none';
      }
    }

    document.getElementById('dataForm').addEventListener('submit', fetchAndRenderData);
  </script>
</body>
</html>