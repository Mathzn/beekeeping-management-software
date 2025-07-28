<?php
include 'db_connector.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = "";

// Handle deletion
if (isset($_GET['delete_id'])) {
  $delete_id = intval($_GET['delete_id']);
  $delete_sql = "DELETE FROM finances WHERE id = ?";
  $del_stmt = $conn->prepare($delete_sql);

  if ($del_stmt) {
    $del_stmt->bind_param("i", $delete_id);
    $del_stmt->execute();
    $message = "<p style='color:red;'>🗑️ Record deleted.</p>";
    $del_stmt->close();
  } else {
    $message = "<p style='color:red;'>❌ Delete failed: " . $conn->error . "</p>";
  }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $date   = $_POST['transaction_date'] ?? '';
  $desc   = $_POST['description'] ?? '';
  $type   = $_POST['type'] ?? '';
  $amount = $_POST['amount'] ?? '';
  $notes  = $_POST['notes'] ?? '';

  if ($date && $desc && $type && $amount) {
    $sql = "INSERT INTO finances (transaction_date, description, type, amount, notes) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
      $stmt->bind_param("sssds", $date, $desc, $type, $amount, $notes);
      $stmt->execute();
      $message = "<p style='color:green;'>✅ Financial record saved.</p>";
      $stmt->close();
    } else {
      $message = "<p style='color:red;'>❌ Prepare failed: " . $conn->error . "</p>";
    }
  } else {
    $message = "<p style='color:red;'>❌ Please fill all required fields.</p>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Financial Records</title>
  <link rel="stylesheet" href="financial.css" />
</head>
<body>
  <div class="card">
    <h2>All Financial Records</h2>
    <?php echo $message; ?>

    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Description</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Notes</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT id, transaction_date, description, type, amount, notes FROM finances ORDER BY transaction_date DESC");

        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['transaction_date']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['type']}</td>
                    <td>KSh {$row['amount']}</td>
                    <td>{$row['notes']}</td>
                    <td><a href='financial.php?delete_id={$row['id']}' onclick='return confirm(\"Delete this record?\");'>Delete</a></td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No financial records found.</td></tr>";
        }

        $conn->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
