<?php
include 'db_connector.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $delete_id = $_POST['delete_id'];
  $stmt = $conn->prepare("DELETE FROM treatments WHERE id = ?");
  if ($stmt) {
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    echo "<p style='color:orange;'>🗑️ Record deleted successfully.</p>";
  } else {
    echo "<p style='color:red;'>❌ Delete error: " . $conn->error . "</p>";
  }
}

// Handle insertion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['treatment_date'])) {
  $date   = $_POST['treatment_date'] ?? '';
  $hive   = $_POST['hive_identifier'] ?? '';
  $type   = $_POST['treatment_type'] ?? '';
  $dosage = $_POST['dosage'] ?? '';
  $notes  = $_POST['notes'] ?? '';

  if (!empty($date) && !empty($hive) && !empty($type)) {
    $stmt = $conn->prepare("INSERT INTO treatments (treatment_date, hive_identifier, treatment_type, dosage, notes)
                            VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
      $stmt->bind_param("sssss", $date, $hive, $type, $dosage, $notes);
      $stmt->execute();
      $stmt->close();
      echo "<p style='color:green;'>✅ Treatment record saved successfully!</p>";
    } else {
      echo "<p style='color:red;'>❌ Database error: " . $conn->error . "</p>";
    }
  } else {
    echo "<p style='color:red;'>❌ Required fields missing.</p>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Hive Treatment Records</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="treatment-container">
    <h2>All Recorded Treatments</h2>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Hive</th>
          <th>Type</th>
          <th>Dosage</th>
          <th>Notes</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT id, treatment_date, hive_identifier, treatment_type, dosage, notes FROM treatments ORDER BY treatment_date DESC");
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['treatment_date']}</td>
                    <td>{$row['hive_identifier']}</td>
                    <td>{$row['treatment_type']}</td>
                    <td>{$row['dosage']}</td>
                    <td>{$row['notes']}</td>
                    <td>
                      <form method='POST' style='display:inline;' onsubmit=\"return confirm('Delete this record?');\">
                        <input type='hidden' name='delete_id' value='{$row['id']}' />
                        <button type='submit' style='color:red;'>🗑️ Delete</button>
                      </form>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No treatment records found.</td></tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
