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

// Fetch the user's details from the database
$sql = "SELECT empName, department, email, phone, username, joiningDate, profile_picture FROM employees WHERE username = '$current_user'";
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Error fetching user details: " . $conn->error);
}

$user_details = [
    'empName' => 'John Doe',  // Default values in case no result
    'department' => 'N/A',
    'email' => 'N/A',
    'phone' => 'N/A',
    'username' => 'N/A',
    'joiningDate' => 'N/A',
    'profile_picture' => 'default.jpg' // Default profile picture
];

if ($result->num_rows > 0) {
    $user_details = $result->fetch_assoc();
}

$conn->close();
?>

<link rel="stylesheet" href="style.css">

<?php require_once 'nav.php'; ?>

<!-- Profile Section -->
<div class="view">
    <div class="viewHeader">
        <div class="title-container">
            <div class="title">User Profile</div>
            <button id="editProfileBtn" class="edit-btn">Edit Profile</button>
        </div>
    </div>

    <div class="contents">
        <!-- Display profile picture -->
        <div>
            <img  class="profile_pic" src="<?php echo htmlspecialchars($user_details['profile_picture']); ?>" alt="Profile Picture" width="150" height="150" />
        </div>

        <table class="profile-table">
            <tr>
                <th>Full Name:</th>
                <td><?php echo htmlspecialchars($user_details['empName']); ?></td>
            </tr>
            <tr>
                <th>Department:</th>
                <td><?php echo htmlspecialchars($user_details['department']); ?></td>
            </tr>
            <tr>
                <th>Phone:</th>
                <td><?php echo htmlspecialchars($user_details['phone']); ?></td>
            </tr>
            <tr>
                <th>Joined Date:</th>
                <td><?php echo htmlspecialchars($user_details['joiningDate']); ?></td>
            </tr>
        </table>
    </div>
</div>

<!-- Modal Section -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Profile</h2>
        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_details['email']); ?>" required>
            </div>
            <div>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_details['phone']); ?>" required>
            </div>
            <div>
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture">
            </div>
            <div>
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Styles -->
<style>
.profile_pic {
    border-radius: 50%; /* Makes it circular */
    margin-left: 30px;
    margin-top: 20px;
    width: 150px; /* Adjust width to your desired size */
    height: 150px; /* Ensure height matches width for perfect circle */
    object-fit: cover; /* Ensures the image fits well inside the circle */
    border: 3px solid #ddd; /* Optional: Add a subtle border */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: Add a shadow for depth */
}
  
  /* Style the modal (hidden by default) */
  .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
  }

  /* Modal Content */
  .modal-content {
      background-color: white;
      margin: 10% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 40%;
      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
      animation: fadeIn 0.5s;
  }

  /* Close button (X) */
  .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
  }

  .close:hover, .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
  }

  .edit-btn {
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-left: auto;
  }

  .edit-btn:hover {
      background-color: #0056b3;
  }

  /* Fade in animation */
  @keyframes fadeIn {
      from {opacity: 0;}
      to {opacity: 1;}
  }

  .title-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
  }

  .title {
      font-size: 1.8em;
  }
</style>

<!-- JavaScript to Open and Close the Modal -->
<script>
    // Get modal element
    var modal = document.getElementById("editModal");

    // Get the button that opens the modal
    var btn = document.getElementById("editProfileBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
