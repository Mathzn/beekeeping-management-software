<?php
$host = "localhost";
$db = "beekeeping_db";
$user = "root"; // replace with your actual DB username
$pass = "";     // replace with your DB password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}
?>
