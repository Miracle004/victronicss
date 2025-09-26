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

// Assuming user is logged in and username is stored in session
$current_user = $_SESSION['username'];

// Fetch the user's full name from the database
$sql = "SELECT empName FROM employees WHERE username = '$current_user'";
$result = $conn->query($sql);
$user_full_name = 'John Doe'; // Default value in case no result
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_full_name = $row['empName'];
}

// Fetch tasks assigned to the user from the database
$sql_tasks = "SELECT task_id, task_description, status FROM tasks WHERE assigned_to = '$current_user'";
$tasks_result = $conn->query($sql_tasks);

// Handle task status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $action = $_POST['action']; // Either 'accept' or 'reject'

    if ($action === 'accept') {
        $update_sql = "UPDATE tasks SET status = 'accepted' WHERE task_id = $task_id AND assigned_to = '$current_user'";
        $conn->query($update_sql);
        $_SESSION['task_message'] = "Task accepted successfully.";
    } elseif ($action === 'reject') {
        $update_sql = "UPDATE tasks SET status = 'rejected' WHERE task_id = $task_id AND assigned_to = '$current_user'";
        $conn->query($update_sql);
        $_SESSION['task_message'] = "Task rejected.";
    }

    header("Location: home.php"); // Redirect to prevent resubmission on refresh
    exit();
}

$conn->close();
?>

<link rel="stylesheet" href="style.css">

<div class="page">
  <div class="pageHeader">
  <img class="logo" src="images/images (1).jpeg" alt="Tasks" class="title-image" />
    <div class="userPanel">
      <i class="fa fa-chevron-down"></i>
      <span class="username"><?php echo htmlspecialchars($user_full_name); ?>
      <form action="logout.php" method="POST" style="display:inline;">
        <button style="margin-left: 30px" type="submit" class="logoutButton">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
              <path d="M16 13v-2H7V9l-5 3 5 3v-2h9zM13 3v2h4v14h-4v2h6V3h-6z"/>
          </svg>
        </button>
      </form>
      </span>
      <img style="width:40px; height:40px;" src="https://gravatar.com/avatar/27205e5c51cb03f862138b22bcb5dc20f94a342e744ff6df1b8dc8af3c865109" />
    </div>
  </div>

  <div class="main">
    <div class="nav">
      <div class="searchbox">
        <div style="display: flex; align-items: center; gap: 8px;">
          <i class="fa fa-search"></i>
          <input type="search" placeholder="Search" style="padding: 5px;" />
        </div>
      </div>
      <div class="menu">
        <div class="title">Navigation</div>
        <ul>
          <li >
            <a href="home.php" style="text-decoration: none; color: inherit;">
              <i class="fa fa-home"></i> Home
            </a>
          </li>
          <li class="active">
            <a href="task.php" style="text-decoration: none; color: inherit;">
              <i class="fa fa-tasks"></i> Tasks
            </a>
          </li>
          <li>
            <a href="profile.php" style="text-decoration: none; color: inherit;">
              <i class="fa fa-user"></i> Profile
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="view">
      <div class="viewHeader">
        <div class="title">Your Tasks</div>
      </div>

      <div class="content">
        <!-- Display tasks if any -->
        <?php if ($tasks_result->num_rows > 0): ?>
          <table class="task-table">
            <thead>
              <tr>
                <th>Task Description</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($task = $tasks_result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($task['task_description']); ?></td>
                  <td><?php echo htmlspecialchars($task['status']); ?></td>
                  <td>
                    <?php if ($task['status'] == 'pending'): ?>
                      <form method="POST" class="task-actions">
                        <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                        <button type="submit" name="action" value="accept" class="task-btn accept-btn">Accept</button>
                        <button type="submit" name="action" value="reject" class="task-btn reject-btn">Reject</button>
                      </form>
                    <?php else: ?>
                      <span>N/A</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No tasks assigned to you.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Modal for task status message -->
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

<!-- Style for tasks and modal -->
<style>
  .logoutButton {
    width: 40px;  
    height: 40px; 
    border-radius: 50%; 
    background-color: #808080; 
    color: white; 
    border: none; 
    cursor: pointer; 
    font-size: 16px; 
    text-align: center; 
    vertical-align: middle; 
  }

  .logo{
    width: 130px;
    height: auto;
    margin-top: 5px;
  }

  .logoutButton:hover {
    background-color: #696969;
  }

  .task-table {
    width: 100%;
    border-collapse: collapse;
  }

  .task-table th, .task-table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
  }

  .task-table th {
    background-color: #f8f9fa;
  }

  .task-actions {
    display: flex;
    gap: 10px;
  }

  .task-btn {
    padding: 5px 10px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-size: 14px;
  }

  .accept-btn {
    background-color: #28a745;
    color: white;
  }

  .reject-btn {
    background-color: #dc3545;
    color: white;
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

  .modal-content p {
    margin-bottom: 20px;
  }
</style>
