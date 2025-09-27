<?php
require_once 'db_conn.php';

// Handling the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empName = $_POST['empName'];
    $empId = $_POST['empId'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $joiningDate = $_POST['joiningDate'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($empName) || empty($empId) || empty($department) || empty($email) || empty($phone) || empty($joiningDate) || empty($username) || empty($password)) {
        die("All fields are required.");
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO employees (empName, empId, department, email, phone, joiningDate, username, password) VALUES (:empName, :empId, :department, :email, :phone, :joiningDate, :username, :password)";
    $stmt = $db->prepare($sql);
    if ($stmt->execute([
        ':empName' => $empName,
        ':empId' => $empId,
        ':department' => $department,
        ':email' => $email,
        ':phone' => $phone,
        ':joiningDate' => $joiningDate,
        ':username' => $username,
        ':password' => $hashedPassword
    ])) {
        echo "New employee added successfully";
    } else {
        echo "Error: " . implode(", ", $stmt->errorInfo());
    }
}
?>


<!-- HTML Form to collect employee details -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management - Victronics Limited</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Employee Management - Victronics Limited</h1>

        <!-- Form to add a new employee -->
        <form id="employeeForm" action="index.php" method="post">
    <label for="empName">Employee Name</label>
    <input type="text" id="empName" name="empName" required>

    <label for="empId">Employee ID</label>
    <input type="text" id="empId" name="empId" required>

    <label for="department">Department</label>
    <select id="department" name="department" required>
        <option value="">Select a Department</option>
        <option value="HR">HR</option>
        <option value="Finance">Finance</option>
        <option value="IT">IT</option>
        <option value="Marketing">Marketing</option>
        <option value="Sales">Sales</option>
    </select>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="phone">Phone Number</label>
    <input type="tel" id="phone" name="phone" required>

    <label for="joiningDate">Joining Date</label>
    <input type="date" id="joiningDate" name="joiningDate" required>

    <label for="username">Username</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" class="btn">Add Employee</button>
</form>
</a>

<style>
    .btn {
        width: 1000px; /* Set the desired width */
        padding: 10px;
        background-color: #4CAF50; /* Button color */
        color: white; /* Text color */
        border: none; /* Remove border */
        border-radius: 5px; /* Optional: rounded corners */
        cursor: pointer;
        font-size: 16px; /* Optional: font size */
    }

    .btn:hover {
        background-color: #45a049; /* Darker green on hover */
    }
</style>

</a>
        </form>
    </div>
</body>
</html>
