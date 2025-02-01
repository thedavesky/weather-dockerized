<?php
$host = 'mariadb';
$username = 'weather';
$password = 'password';
$db_name = 'weather';

// Create a PDO connection to the database
$dsn = "mysql:host=$host;dbname=$db_name";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Output the tables as JSON

// Get the start and end dates from the query string
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];

// Get the data from the weather_data table
$stmt = $pdo->prepare("SELECT * FROM weather_data WHERE Date_Time BETWEEN :start_date AND :end_date");
$stmt->execute([
    ':start_date' => $start_date . ' 00:00:00',
    ':end_date' => $end_date . ' 23:59:59',
]);
$data = $stmt->fetchAll();

// Close the connection
$pdo = null;

// Output the data as JSON
echo json_encode($data);
?>
