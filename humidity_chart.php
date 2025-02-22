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

$humidities = [];
$timestamps = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $humidities[] = $row['humidity'];
        $timestamps[] = $row['timestamp'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wykres Wilgotności</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="humidityChart" width="400" height="200"></canvas>
    <script>
    let humidityData = <?php echo json_encode(array_reverse($humidities)); ?>;
    let labels = <?php echo json_encode(array_reverse($timestamps)); ?>;

    const ctxHumidity = document.getElementById('humidityChart').getContext('2d');
    const humidityChart = new Chart(ctxHumidity, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Wilgotność (%)',
                data: humidityData,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
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
                    min: Math.min(...humidityData) - 5, // dynamiczny zakres dolny
                    max: Math.max(...humidityData) + 5, // dynamiczny zakres górny
                    position: 'left', // Miarka po lewej
                },
                y2: { 
                    position: 'right',// Miarka po prawej stronie
                    grid: {
                        drawOnChartArea: false
                    },
                        min: Math.min(...humidityData) - 5, // dynamiczny zakres dolny
                        max: Math.max(...humidityData) + 5, // dynamiczny zakres górny

                }
            }
        }
    });

    function refreshData() {
        fetch('get_latest_data.php')
            .then(response => response.json())
            .then(data => {
                // Zaktualizuj dane wykresu
                humidityData = data.humidity;
                labels = data.timestamps;
                
                // Zaktualizuj dane wykresu
                humidityChart.data.labels = labels;
                humidityChart.data.datasets[0].data = humidityData;
                
                // Dynamiczny zakres osi Y
                humidityChart.options.scales.y.min = Math.min(...humidityData) - 5;
                humidityChart.options.scales.y.max = Math.max(...humidityData) + 5;

                // Zaktualizuj wykres
                humidityChart.update();
            });
    }

    // Odświeżaj dane co 30 sekund
    setInterval(refreshData, 30000);
</script>


</body>
</html>
