<?php
include 'db_connector.php';
session_start();

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (empty($username) || empty($password)) {
  exit("❌ Both fields are required.");
}

$sql = "SELECT password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
  $stmt->bind_result($hashedPassword);
  $stmt->fetch();

  if (password_verify($password, $hashedPassword)) {
    $_SESSION['username'] = $username;

    // ✅ Redirect immediately to dashboard.html
    header("Location: dashboard.html");
    exit();
  } else {
    echo "❌ Incorrect password.";
  }
} else {
  echo "❌ Username not found.";
}

$stmt->close();
$conn->close();
