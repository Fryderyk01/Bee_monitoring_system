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
$humidities = [];
$timestamps = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $temperatures[] = $row['temperature'];
        $humidities[] = $row['humidity'];
        $timestamps[] = $row['timestamp'];
    }
}

$conn->close();

// Zwróć dane w formacie JSON
echo json_encode([
    'temperature' => $temperatures,
    'humidity' => $humidities,
    'timestamps' => $timestamps
]);
?>
