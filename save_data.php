<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "weather_data";

// Tworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pobieranie danych z URL
$temperature = $_GET['temperature'];
$humidity = $_GET['humidity'];

// Sprawdzanie czy dane nie są puste
if (isset($temperature) && isset($humidity)) {
    $sql = "INSERT INTO measurements (temperature, humidity, timestamp) VALUES ('$temperature', '$humidity', NOW())";
    if ($conn->query($sql) === TRUE) {
        echo "Dane zapisane pomyślnie!";
    } else {
        echo "Błąd: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Brak danych!";
}

$conn->close();
?>
