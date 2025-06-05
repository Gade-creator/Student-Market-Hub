<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'student_market';

// Enable exception reporting for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    // echo 'Connected!';
} catch (mysqli_sql_exception $e) {
    echo 'Database connection failed: ' . $e->getMessage();
    exit;
}
?>