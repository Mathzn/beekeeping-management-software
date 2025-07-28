<?php
include 'db_connector.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle task deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $delete_id = $_POST['delete_id'];
  $del_stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
  if ($del_stmt) {
    $del_stmt->bind_param("i", $delete_id);
    $del_stmt->execute();
    $del_stmt->close();
    $message = "<p style='color:green;'>🗑️ Task deleted successfully!</p>";
  } else {
    $message = "<p style='color:red;'>❌ Delete error: " . $conn->error . "</p>";
  }
}

// Handle task creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_name'])) {
  $name     = $_POST['task_name'] ?? '';
  $desc     = $_POST['description'] ?? '';
  $hive     = $_POST['hive_id'] ?? '';
  $date     = $_POST['task_date'] ?? '';
  $priority = $_POST['priority'] ?? 'Medium';

  if (!empty($name) && !empty($desc) && !empty($hive) && !empty($date)) {
    $stmt = $conn->prepare("INSERT INTO tasks (task_name, description, hive_id, task_date, priority) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
      $stmt->bind_param("sssss", $name, $desc, $hive, $date, $priority);
      $stmt->execute();
      $stmt->close();
      $message = "<p style='color:green;'>✅ Task saved successfully!</p>";
    } else {
      $message = "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
    }
  } else {
    $message = "<p style='color:red;'>❌ Missing required fields.</p>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Hive Task Records</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="treatment-container">
    <h2>All Hive Tasks</h2>

    <?php if (isset($message)) echo $message; ?>

    <table>
      <thead>
        <tr>
          <th>Task Name</th>
          <th>Description</th>
          <th>Hive</th>
          <th>Date</th>
          <th>Priority</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT id, task_name, description, hive_id, task_date, priority FROM tasks ORDER BY task_date DESC");

        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['task_name']) . "</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td>" . htmlspecialchars($row['hive_id']) . "</td>
                    <td>" . date("d M Y", strtotime($row['task_date'])) . "</td>
                    <td>" . htmlspecialchars($row['priority']) . "</td>
                    <td>
                      <form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this task?');\">
                        <input type='hidden' name='delete_id' value='" . $row['id'] . "' />
                        <button type='submit' style='background-color:red;color:white;'>Delete</button>
                      </form>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No tasks recorded yet.</td></tr>";
        }

        $conn->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
