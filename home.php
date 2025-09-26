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

// Fetch tasks assigned to the user and count them based on their status
$sql_tasks = "SELECT status FROM tasks WHERE assigned_to = '$current_user'";
$tasks_result = $conn->query($sql_tasks);

// Initialize task counts
$task_count = [
    'total' => 0,
    'Accepted' => 0,
    'Rejected' => 0,
    'Pending' => 0
];

// Count tasks by status
while ($task = $tasks_result->fetch_assoc()) {
    $task_count['total']++;
    switch ($task['status']) {
        case 'accepted':
            $task_count['Accepted']++;
            break;
        case 'rejected':
            $task_count['Rejected']++;
            break;
        case 'pending':
            $task_count['Pending']++;
            break;
    }
}

$conn->close();
?>

<link rel="stylesheet" href="style.css">

<div class="page">
  <div class="pageHeader">
    <img class="logo" src="images/images (1).jpeg" alt="Tasks" class="title-image" />
    <div class="userPanel">
      <i class="fa fa-chevron-down"></i>
      <span class="username"><?php echo htmlspecialchars($user_full_name); ?></span>
      <form action="logout.php" method="POST" style="display:inline;">
        <button style="margin-left: 30px" type="submit" class="logoutButton">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
              <path d="M16 13v-2H7V9l-5 3 5 3v-2h9zM13 3v2h4v14h-4v2h6V3h-6z"/>
          </svg>
        </button>
      </form>
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
          <li class="active">
            <a href="home.php" style="text-decoration: none; color: inherit;">
              <i class="fa fa-home"></i> Home
            </a>
          </li>
          <li>
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
        <div class="title">Metrics Overview</div>
      </div>
      
      <div class="metrics">
        <div class="metric-card">
          <h3>Total Tasks</h3>
          <p><?php echo $task_count['total']; ?></p>
        </div>
        <div class="metric-card accepted">
          <h3>Accepted Tasks</h3>
          <p><?php echo $task_count['Accepted']; ?></p>
        </div>
        <div class="metric-card rejected">
          <h3>Rejected Tasks</h3>
          <p><?php echo $task_count['Rejected']; ?></p>
        </div>
        <div class="metric-card pending">
          <h3>Pending Tasks</h3>
          <p><?php echo $task_count['Pending']; ?></p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Style for metrics and navigation -->
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

  .logo {
    width: 130px;
    height: auto;
    margin-top: 5px;
  }

  .logoutButton:hover {
    background-color: #696969;
  }

  .metrics {
    display: flex;
    justify-content: space-between;
    margin: 20px 0;
  }

  .metric-card {
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 20px;
    flex: 1;
    margin: 0 10px;
    text-align: center;
  }

  .metric-card h3 {
    margin: 0 0 10px;
  }

  .metric-card p {
    font-size: 24px;
    font-weight: bold;
  }

  .accepted {
    background-color: #d4edda; /* Light green */
  }

  .rejected {
    background-color: #f8d7da; /* Light red */
  }

  .pending {
    background-color: #fff3cd; /* Light yellow */
  }
</style>
