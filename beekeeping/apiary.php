<?php
include 'db_connector.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle delete first
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $delete_id = $_POST['delete_id'];
  $stmt = $conn->prepare("DELETE FROM apiaries WHERE id = ?");
  $stmt->bind_param("i", $delete_id);
  $stmt->execute();
  $stmt->close();
  header("Location: apiary.php");
  exit;
}

// Handle insert second
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['city'], $_POST['country'])) {
  $name = $_POST['name'] ?? '';
  $city = $_POST['city'] ?? '';
  $country = $_POST['country'] ?? '';
  $latitude = $_POST['latitude'] ?? null;
  $longitude = $_POST['longitude'] ?? null;
  $hives = $_POST['hives'] ?? 0;

  if (empty($name) || empty($city) || empty($country)) {
    echo "❌ Required fields missing.";
  } else {
    $stmt = $conn->prepare("INSERT INTO apiaries (name, city, country, latitude, longitude, hives) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssddi", $name, $city, $country, $latitude, $longitude, $hives);
    if ($stmt->execute()) {
      echo "✅ Apiary saved successfully!";
    } else {
      echo "❌ Error saving apiary: " . $stmt->error;
    }
    $stmt->close();
  }
}

// Display records
$sql = "SELECT id, name, city, country, latitude, longitude, hives FROM apiaries";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  echo "<h2>🌍 Apiary Records</h2><table border='1' cellpadding='8'>
        <tr><th>Name</th><th>City</th><th>Country</th><th>Latitude</th>
        <th>Longitude</th><th>Hives</th><th>Delete</th></tr>";

  while ($row = $result->fetch_assoc()) {
    echo "<tr>
          <td>{$row['name']}</td>
          <td>{$row['city']}</td>
          <td>{$row['country']}</td>
          <td>{$row['latitude']}</td>
          <td>{$row['longitude']}</td>
          <td>{$row['hives']}</td>
          <td>
            <form method='post' action='apiary.php' onsubmit=\"return confirm('Delete this record?');\">
              <input type='hidden' name='delete_id' value='{$row['id']}'>
              <input type='submit' value='🗑️ Delete'>
            </form>
          </td>
          </tr>";
  }
  echo "</table>";
} else {
  echo "<p>📭 No records found.</p>";
}

$conn->close();
