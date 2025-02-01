<?php
// Ustawienie nagłówka Content-Type na JSON
header('Content-Type: application/json; charset=utf-8');

/**
 * Funkcja pomocnicza do zwracania błędu w formacie JSON.
 *
 * @param string $message Komunikat błędu.
 * @param int    $code    Kod HTTP błędu (domyślnie 400).
 */
function returnError(string $message, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// Pobieranie parametrów daty z zapytania GET
$start_date = $_GET['start_date'] ?? null;
$end_date   = $_GET['end_date'] ?? null;

if (!$start_date || !$end_date) {
    returnError('Brak wymaganych parametrów daty.');
}

// Walidacja formatu daty przy użyciu DateTime
$dateFormat = 'Y-m-d';
$startDateObj = DateTime::createFromFormat($dateFormat, $start_date);
$endDateObj   = DateTime::createFromFormat($dateFormat, $end_date);

if (!$startDateObj || $startDateObj->format($dateFormat) !== $start_date) {
    returnError('Niepoprawny format daty początkowej. Użyj formatu YYYY-MM-DD.');
}

if (!$endDateObj || $endDateObj->format($dateFormat) !== $end_date) {
    returnError('Niepoprawny format daty końcowej. Użyj formatu YYYY-MM-DD.');
}

// Konfiguracja połączenia z bazą danych
$host     = 'mariadb';
$username = 'weather';
$password = 'password';
$db_name  = 'weather';

$dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    returnError('Błąd połączenia z bazą danych.', 500);
}

// Przygotowanie i wykonanie zapytania – wykorzystanie przygotowanych zapytań PDO
$sql = "SELECT Date_Time, Temperature_C, Humidity_pct, Precipitation_mm, Wind_Speed_kmh 
        FROM weather_data 
        WHERE Date_Time BETWEEN :start_date AND :end_date 
        ORDER BY Date_Time ASC";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':start_date' => $start_date . ' 00:00:00',
        ':end_date'   => $end_date . ' 23:59:59',
    ]);
    $data = $stmt->fetchAll();
} catch (PDOException $e) {
    returnError('Błąd zapytania do bazy danych.', 500);
}

// Zwracamy wynik w formacie JSON
echo json_encode($data);
?>