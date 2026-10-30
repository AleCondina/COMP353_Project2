<?php
// Configure these variables in the environment of the PHP process.
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');

if ($host === false || $host === '' ||
    $user === false || $user === '' ||
    $password === false ||
    $database === false || $database === '') {
    http_response_code(500);
    exit('Database configuration is missing. See README.md for setup instructions.');
}

try {
    $conn = new mysqli($host, $user, $password, $database);
    if ($conn->connect_errno) {
        throw new RuntimeException('Database connection failed.');
    }
} catch (Throwable $error) {
    http_response_code(500);
    exit('Unable to connect to the database. Check the server configuration.');
}
