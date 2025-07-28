<?php
include 'db_connector.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = "";

// Handle deletion
if (isset($_GET['delete_id'])) {
  $delete_id = intval($_GET['delete_id']);
  $del_stmt = $conn->prepare("DELETE FROM harvests WHERE id = ?");
  if ($del_stmt) {
    $del_stmt->bind_param("i", $delete_id);
    if ($del_stmt->execute()) {
      $message = "<p style='color:red;'>🗑️ Record deleted successfully.</p>";
    } else {
      $message = "<p style='color:red;'>❌ Delete failed: " . $del_stmt->error . "</p>";
    }
    $del_stmt->close();
  } else {
    $message = "<p style='color:red;'>❌ Prepare for delete failed: " . $conn->error . "</p>";
  }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $date     = $_POST['date'] ?? '';
  $product  = $_POST['product'] ?? '';
  $quantity = $_POST['quantity'] ?? '';
  $person   = $_POST['person'] ?? '';

  if ($date && $product && $quantity && $person) {
    $stmt = $conn->prepare("INSERT INTO harvests (date, product, quantity, person) VALUES (?, ?, ?, ?)");
    if ($stmt) {
      $stmt->bind_param("ssds", $date, $product, $quantity, $person);
      if ($stmt->execute()) {
        $message = "<p style='color:green;'>✅ Harvest record saved.</p>";
      } else {
        $message = "<p style='color:red;'>❌ Execute failed: " . $stmt->error . "</p>";
      }
      $stmt->close();
    } else {
      $message = "<p style='color:red;'>❌ Prepare failed: " . $conn->error . "</p>";
    }
  } else {
    $message = "<p style='color:red;'>❌ All fields must be filled.</p>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Harvest Records</title>
  <link rel="stylesheet" href="harvest.css" />
</head>
<body>
  <div class="card">
    <h2>Saved Harvest Records</h2>
    <?php echo $message; ?>

    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Product</th>
          <th>Quantity (kg)</th>
          <th>Harvested By</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT id, date, product, quantity, person FROM harvests ORDER BY date DESC");

        if (!$result) {
          echo "<tr><td colspan='5' style='color:red;'>❌ Query failed: " . $conn->error . "</td></tr>";
        } elseif ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['date']}</td>
                    <td>{$row['product']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['person']}</td>
                    <td><a href='harvest.php?delete_id={$row['id']}' onclick='return confirm(\"Delete this record?\");'>Delete</a></td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='5' style='text-align:center;'>No harvest records found.</td></tr>";
        }

        $conn->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
