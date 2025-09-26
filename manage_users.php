<?php
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all users for management
$sql_users = "SELECT * FROM employees";
$users_result = $conn->query($sql_users);

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM employees WHERE id = $delete_id";
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['user_message'] = "User deleted successfully.";
    } else {
        $_SESSION['user_message'] = "Error deleting user: " . $conn->error;
    }
    header("Location: manage_users.php");
    exit();
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $update_id = $_POST['user_id'];
    $username = $_POST['username'];
    $empName = $_POST['empName'];
    $email = $_POST['email'];
    $post = $_POST['post'];

    $update_sql = "UPDATE employees SET username='$username', empName='$empName', email='$email', post='$post' WHERE id=$update_id";
    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['user_message'] = "User updated successfully.";
    } else {
        $_SESSION['user_message'] = "Error updating user: " . $conn->error;
    }
    header("Location: manage_users.php");
    exit();
}

// Handle new user addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
  $username = $_POST['new_username'];
  $empName = $_POST['new_empName'];
  $email = $_POST['new_email'];
  $post = $_POST['new_post'];
  $password = bin2hex(random_bytes(5)); // Generate a random alphanumeric password
  $encrypted_password = password_hash($password, PASSWORD_BCRYPT); // Encrypt password

  // Insert new user into the database
  $add_sql = "INSERT INTO employees (username, empName, email, post, password) VALUES ('$username', '$empName', '$email', '$post', '$encrypted_password')";

  if ($conn->query($add_sql) === TRUE) {
      // Send email
      $mail = new PHPMailer(true);
        try {
            // Brevo SMTP settings
            $mail->isSMTP();
            $mail->Host = 'smtp-relay.brevo.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ddegreeproject@gmail.com';
            $mail->Password = 'xsmtpsib-d219e2013a5ce1bf42ada0609e149db7e26554fb5f13d446ddf4e891909d4291-4YxQIdg7053fpGkM';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

          $mail->setFrom('samstringz98@gmail.com', 'Admin'); // Your email
          $mail->addAddress($email); // Add recipient's email
          $mail->Subject = 'Your Login Details';
          $mail->Body = "Hello $empName,\n\nYour account has been created.\nUsername: $username\nPassword: $password\n\nLogin at: yourdashboardlink.com";

          // Send the email
          $mail->send();
          $_SESSION['user_message'] = "User added successfully and email sent.";
      } catch (Exception $e) {
          $_SESSION['user_message'] = "User added, but email failed to send: " . $mail->ErrorInfo;
      }
  } else {
      $_SESSION['user_message'] = "Error adding user: " . $conn->error;
  }
  header("Location: manage_users.php");
  exit();
}

// Close connection
$conn->close();
?>


<link rel="stylesheet" href="style.css">

<div class="admin-page">
  <div class="pageHeader">
    <div class="title">Manage Users</div>
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
          <li><a href="admin_dashboard.php">Dashboard</a></li>
          <li class="active"><a href="manage_users.php">Manage Users</a></li>
        </ul>
      </div>
    </div>

    <div class="view">
      <div class="viewHeader">
        <div class="title-users">User List</div>
        <button class="add-button" onclick="openAddUserModal()">Add New User</button> <!-- Add New User button -->
      </div>

      <div class="content">
        <div class="user-list-section">
          <?php if ($users_result->num_rows > 0): ?>
            <table class="user-table">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>Employee Name</th>
                  <th>Email</th>
                  <th>Department</th>
                  <th>Post</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['empName']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['department']); ?></td>
                    <td><?php echo htmlspecialchars($user['post']); ?></td>
                    <td>
                      <button class="edit-button" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)">Edit</button> | 
                      <a href="?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No users found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal for editing user -->
<div id="editUserModal" class="modal">
  <div class="modal-content">
    <form method="POST" id="editUserForm">
      <h2>Edit User</h2>
      <input type="hidden" name="user_id" id="user_id">
      <label for="username">Username:</label>
      <input type="text" name="username" id="username" required>

      <label for="empName">Employee Name:</label>
      <input type="text" name="empName" id="empName" required>

      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required>

      <label for="post">Post:</label>
      <input type="text" name="post" id="post" required>

      <button type="submit" name="update_user" class="task-submit-btn">Update User</button>
      <button type="button" onclick="closeEditModal()">Cancel</button>
    </form>
  </div>
</div>

<!-- Modal for adding new user -->
<div id="addUserModal" class="modal">
  <div class="modal-content">
    <form method="POST" id="addUserForm">
      <h2 class="add-new">Add New User</h2>
      <label for="new_username">Username:</label>
      <input type="text" name="new_username" required>

      <label for="new_empName">Employee Name:</label>
      <input type="text" name="new_empName" required>

      <label for="new_email">Email:</label>
      <input type="email" name="new_email" required>

      <label for="new_post">Post:</label>
      <select name="new_post" required>
        <option value="staff">Staff</option>
        <option value="admin">Admin</option>
      </select>

      <button type="submit" name="add_user" class="task-submit-btn">Add User</button>
      <button type="button" class="cancel" onclick="closeAddUserModal()">Cancel</button>
    </form>
  </div>
</div>

<!-- Modal for user message -->
<?php if (isset($_SESSION['user_message'])): ?>
  <div id="userModal" class="modal">
    <div class="modal-content">
      <p><?php echo $_SESSION['user_message']; ?></p>
      <button onclick="closeModal()">Close</button>
    </div>
  </div>
  <script>
    // Function to close modal
    function closeModal() {
      document.getElementById('userModal').style.display = 'none';
    }
    // Show modal on page load
    window.onload = function() {
      if (document.getElementById('userModal')) {
        document.getElementById('userModal').style.display = 'block';
      }
    };
  </script>
  <?php unset($_SESSION['user_message']); ?>
<?php endif; ?>

<!-- Styling for manage users page -->
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

  .user-list-section {
    margin-top: 30px;
  }

  .title-users{
    margin-right: 10px;
    font-size: 20px;
  }

  .add-new{
    margin-bottom: 20px;
  }

  .user-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  .user-table th, .user-table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
  }

  .user-table th {
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
    overflow-y: auto; /* Allow scrolling if content exceeds the viewport */
  }

  .modal-content {
    background-color: white;
    margin: 10% auto; /* Adjust for better centering */
    padding: 20px;
    border-radius: 5px;
    width: 90%; /* Responsive width */
    max-width: 500px; /* Max width for larger screens */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
  }

  .content{
    margin-top: 25px;
  }

  .edit-button, .add-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 4px; /* Rounded corners for buttons */
    margin-right: 10px; /* Space between buttons */
  }

  .task-submit-btn{
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 4px; /* Rounded corners for buttons */
    margin-right: 10px;
  }

  .cancel{
    background-color: red;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 4px; /* Rounded corners for buttons */
    margin-right: 10px;
  }

  .edit-button:hover, .add-button:hover {
    background-color: #0056b3;
  }

  @media (max-width: 600px) {
    .modal-content {
      width: 95%; /* Slightly wider on smaller screens */
    }
  }
</style>

<script>
  function openEditModal(user) {
    document.getElementById('user_id').value = user.id;
    document.getElementById('username').value = user.username;
    document.getElementById('empName').value = user.empName;
    document.getElementById('email').value = user.email;
    document.getElementById('post').value = user.post;
    document.getElementById('editUserModal').style.display = 'block';
  }

  function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
  }

  function openAddUserModal() {
    document.getElementById('addUserModal').style.display = 'block';
  }

  function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
  }
</script>
