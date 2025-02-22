<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "weather_data";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM measurements ORDER BY timestamp DESC LIMIT 20";
$result = $conn->query($sql);

$temperatures = [];
$timestamps = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $temperatures[] = $row['temperature'];
        $timestamps[] = $row['timestamp'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wykres Temperatury</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="temperatureChart" width="400" height="200"></canvas>
    <script>
    let temperatureData = <?php echo json_encode(array_reverse($temperatures)); ?>;
    let labels = <?php echo json_encode(array_reverse($timestamps)); ?>;

    const ctxTemperature = document.getElementById('temperatureChart').getContext('2d');
    const temperatureChart = new Chart(ctxTemperature, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Temperatura (°C)',
                data: temperatureData,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                x: {
                    reverse: true, // Odwrócenie osi X, najnowsze dane z prawej
                },
                y: {
                    min: Math.min(...temperatureData) - 5, // dynamiczny zakres dolny
                    max: Math.max(...temperatureData) + 5, // dynamiczny zakres górny
                    position: 'left', // Miarka po lewej
                },
                y2: { 
                    position: 'right',// Miarka po prawej stronie
                    grid: {
                        drawOnChartArea: false
                    },
                        min: Math.min(...temperatureData) - 5, // dynamiczny zakres dolny
                        max: Math.max(...temperatureData) + 5, // dynamiczny zakres górny
                }
            }
        }
    });

    function refreshData() {
        fetch('get_latest_data.php')
            .then(response => response.json())
            .then(data => {
                // Zaktualizuj dane wykresu
                temperatureData = data.temperature;
                labels = data.timestamps;
                
                // Zaktualizuj dane wykresu
                temperatureChart.data.labels = labels;
                temperatureChart.data.datasets[0].data = temperatureData;
                
                // Dynamiczny zakres osi Y
                temperatureChart.options.scales.y.min = Math.min(...temperatureData) - 5;
                temperatureChart.options.scales.y.max = Math.max(...temperatureData) + 5;

                // Zaktualizuj wykres
                temperatureChart.update();
            });
    }

    // Odświeżaj dane co 30 sekund
    setInterval(refreshData, 30000);
</script>




</body>
</html>
