<?php
// koneksi.php - File koneksi database
$host     = 'localhost';
$user     = 'root';
$password = '';
$dbname   = 'blog_uts';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        'status'  => 'error',
        'message' => 'Koneksi database gagal: ' . $conn->connect_error
    ]));
}

$conn->set_charset('utf8mb4');
