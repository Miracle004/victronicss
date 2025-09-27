<?php
session_start();
require_once 'db_conn.php';

// Assuming user is logged in and username is stored in session
$current_user = $_SESSION['username'];

// Fetch the user's full name from the database
$sql = "SELECT empName FROM employees WHERE username = :username";
$stmt = $db->prepare($sql);
$stmt->execute([':username' => $current_user]);
$user_full_name = 'John Doe';
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $user_full_name = $row['empName'];
}

// Fetch user messages from the database
$sql_messages = "SELECT sender, message, sent_at FROM messages WHERE recipient = :username ORDER BY sent_at DESC";
$stmt_messages = $db->prepare($sql_messages);
$stmt_messages->execute([':username' => $current_user]);
$messages_result = $stmt_messages->fetchAll(PDO::FETCH_ASSOC);

$login_success = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : false;
unset($_SESSION['login_success']);
?>

<link rel="stylesheet" href="style.css">
<style>
  .message-box {
      background-color: #f8f9fa;
      border-radius: 5px;
      padding: 20px;
      margin-bottom: 20px;
  }
  .message-header {
      font-weight: bold;
      color: #333;
  }
  .message-time {
      color: #888;
      font-size: 0.9em;
  }
  .message-body {
      margin-top: 10px;
      line-height: 1.6;
  }
  .send-message-form {
      display: flex;
      flex-direction: column;
      gap: 10px;
  }
  .send-message-form input, .send-message-form textarea {
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
  }
  .send-message-form button {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 5px;
      cursor: pointer;
  }
  .send-message-form button:hover {
      background-color: #218838;
  }
</style>

<div class="page">
  <div class="pageHeader">
    <div class="title">Messages</div>
    <div class="userPanel">
      <i class="fa fa-chevron-down"></i>
      <span class="username"><?php echo htmlspecialchars($user_full_name); ?>
      <form action="logout.php" method="POST" style="display:inline;">
  <button style="margin-left: 30px" type="submit" class="logoutButton">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
        <path d="M16 13v-2H7V9l-5 3 5 3v-2h9zM13 3v2h4v14h-4v2h6V3h-6z"/>
    </svg>
</button>
</form></span>
      <img style="width:40px; height:40px;" src="https://gravatar.com/avatar/27205e5c51cb03f862138b22bcb5dc20f94a342e744ff6df1b8dc8af3c865109" />
    </div>
</div>

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

  .logoutButton:hover {
    background-color: #696969;
  }
</style>


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
        <ul>
  <li>
    <a href="home.php" style="text-decoration: none; color: inherit;">
      <i class="fa fa-home"></i> Home
    </a>
  </li>
  <li>
    <a href="activity.php" style="text-decoration: none; color: inherit;">
      <i class="fa fa-signal"></i> Activity
    </a>
  </li>
  <li>
    <a href="dashboard.php" style="text-decoration: none; color: inherit;">
      <i class="fa fa-tasks"></i> Manage Tasks
    </a>
  </li>
  <li class="active">
    <a href="messages.php" style="text-decoration: none; color: inherit;">
      <i class="fa fa-envelope"></i> Messages
    </a>
  </li>
  <li>
            <a href="profile.php" style="text-decoration: none; color: inherit;">
              <i class="fa fa-user"></i> Profile
            </a>
          </li>
</ul>

        </ul>
      </div>
    </div>

    <div class="view">
      <div class="viewHeader">
        <div class="title">Your Messages</div>
      </div>

      

        <!-- Send new message form -->
        <div class="send-message-form">
          <form action="send_message.php" method="POST">
            <input type="text" name="recipient" placeholder="Recipient" required>
            <textarea name="message" rows="4" placeholder="Type your message..." required></textarea>
            <button type="submit">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>