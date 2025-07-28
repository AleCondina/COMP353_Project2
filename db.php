<?php
$host = "fuc353.encs.concordia.ca";
$user = "fuc353_1";
$password = "RA35DBDB";
$database = "fuc353_1";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
