<!DOCTYPE html>
<html>
<head>
    <title>Wyniki Pomiarów</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }
        #chart-container {
            width: 80%;
            margin: 0 auto;
        }
        iframe {
            border: none;
            width: 100%;
            height: 500px;
            display: none;
        }
        .active {
            display: block;
        }
        #toggleBtn {
            padding: 10px 20px;
            margin: 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Wyniki Pomiarów</h2>
    
    <button id="toggleBtn" onclick="toggleChart()">Pokaż Wilgotność</button>
    
    <div id="chart-container">
        <iframe src="temperature_chart.php" id="temperatureChart" class="active"></iframe>
        <iframe src="humidity_chart.php" id="humidityChart"></iframe>
    </div>

    <script>
        let currentChart = 'temperature';  // Przechowuje aktualnie wyświetlany wykres

        function toggleChart() {
            const tempChart = document.getElementById('temperatureChart');
            const humChart = document.getElementById('humidityChart');
            const toggleBtn = document.getElementById('toggleBtn');

            if (currentChart === 'temperature') {
                // Jeśli aktualny wykres to temperatura, zmień na wilgotność
                tempChart.classList.remove('active');
                humChart.classList.add('active');
                toggleBtn.textContent = 'Pokaż Temperaturę';  // Zmiana tekstu przycisku
                currentChart = 'humidity';
            } else {
                // Jeśli aktualny wykres to wilgotność, zmień na temperaturę
                humChart.classList.remove('active');
                tempChart.classList.add('active');
                toggleBtn.textContent = 'Pokaż Wilgotność';  // Zmiana tekstu przycisku
                currentChart = 'temperature';
            }
        }
    </script>
</body>
</html>
