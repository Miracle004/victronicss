<?php
session_start();
// Database connection
$servername = "localhost"; 
$db_username = "root"; 
$db_password = ""; 
$dbname = "employment"; 

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users for task assignment
$sql_users = "SELECT username, empName FROM employees";
$users_result = $conn->query($sql_users);

// Handle task creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_description = $_POST['task_description'];
    $assigned_to = $_POST['assigned_to'];
    $status = 'pending'; // New task starts as pending

    // Insert the new task into the tasks table
    $insert_sql = "INSERT INTO tasks (task_description, assigned_to, status) VALUES ('$task_description', '$assigned_to', '$status')";
    if ($conn->query($insert_sql) === TRUE) {
        $_SESSION['task_message'] = "Task created successfully.";
    } else {
        $_SESSION['task_message'] = "Error creating task: " . $conn->error;
    }

    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all tasks for the admin to manage
$sql_tasks = "SELECT t.task_id, t.task_description, t.status, e.empName 
              FROM tasks t 
              JOIN employees e ON t.assigned_to = e.username";
$tasks_result = $conn->query($sql_tasks);

$conn->close();
?>

<link rel="stylesheet" href="style.css">

<div class="admin-page">
  <div class="pageHeader">
    <div class="title">Admin Dashboard</div>
    <div class="userPanel">
      <i class="fa fa-chevron-down"></i>
      <form action="logout.php" method="POST" style="display:inline;">
        <button style="margin-left: 30px" type="submit" class="logoutButton">Logout</button>
      </form>
    </div>
  </div>

  <div class="main">
    <div class="nav">
      <div class="menu">
        <div class="title">Navigation</div>
        <ul>
          <li class="active"><a href="admin_dashboard.php">Dashboard</a></li>
          <li><a href="manage_users.php">Manage Users</a></li>
        </ul>
      </div>
    </div>

    <div class="view">
      <div class="viewHeader">
        <div class="title">Create New Task</div>
      </div>

      <div class="content">
        <!-- Task Creation Form -->
        <form method="POST" class="task-form">
          <label for="task_description">Task Description:</label>
          <textarea name="task_description" id="task_description" required></textarea>

          <label for="assigned_to">Assign To:</label>
          <select name="assigned_to" id="assigned_to" required>
            <?php while ($user = $users_result->fetch_assoc()): ?>
              <option value="<?php echo htmlspecialchars($user['username']); ?>">
                <?php echo htmlspecialchars($user['empName']); ?>
              </option>
            <?php endwhile; ?>
          </select>

          <button type="submit" class="task-submit-btn">Create Task</button>
        </form>

        <!-- Display tasks if any -->
        <div class="task-list-section">
          <h2>Task List</h2>
          <?php if ($tasks_result->num_rows > 0): ?>
            <table class="task-table">
              <thead>
                <tr>
                  <th>Task ID</th>
                  <th>Task Description</th>
                  <th>Assigned To</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($task = $tasks_result->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo $task['task_id']; ?></td>
                    <td><?php echo htmlspecialchars($task['task_description']); ?></td>
                    <td><?php echo htmlspecialchars($task['empName']); ?></td>
                    <td><?php echo htmlspecialchars($task['status']); ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No tasks found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal for task message -->
<?php if (isset($_SESSION['task_message'])): ?>
  <div id="taskModal" class="modal">
    <div class="modal-content">
      <p><?php echo $_SESSION['task_message']; ?></p>
      <button onclick="closeModal()">Close</button>
    </div>
  </div>
  <script>
    // Function to close modal
    function closeModal() {
      document.getElementById('taskModal').style.display = 'none';
    }
    // Show modal on page load
    window.onload = function() {
      document.getElementById('taskModal').style.display = 'block';
    };
  </script>
  <?php unset($_SESSION['task_message']); ?>
<?php endif; ?>

<!-- Styling for admin dashboard -->
<style>
  .logoutButton {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #808080;
    color: white;
    border: none;
    cursor: pointer;
  }

  .logoutButton:hover {
    background-color: #696969;
  }

  .task-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .task-form label {
    font-weight: bold;
  }

  .task-form textarea {
    width: 100%;
    height: 80px;
    padding: 5px;
  }

  .task-form select, .task-submit-btn {
    padding: 8px;
    font-size: 14px;
    cursor: pointer;
  }

  .task-submit-btn {
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
  }

  .task-list-section {
    margin-top: 30px;
  }

  .task-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  .task-table th, .task-table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
  }

  .task-table th {
    background-color: #f8f9fa;
  }

  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
  }

  .modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border-radius: 5px;
    width: 300px;
    text-align: center;
  }
</style>
