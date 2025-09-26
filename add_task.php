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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_description = $_POST['task_description'];
    $assigned_to = $_POST['assigned_to'];

    // Validate inputs
    if (!empty($task_description) && !empty($assigned_to)) {
        // Insert task into the database
        $sql = "INSERT INTO tasks (task_description, assigned_to, status) VALUES ('$task_description', '$assigned_to', 'pending')";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['task_add_message'] = "Task added successfully!";
        } else {
            $_SESSION['task_add_message'] = "Error: " . $conn->error;
        }
    } else {
        $_SESSION['task_add_message'] = "Please fill in all fields.";
    }

    // Redirect to the same page to display the message and avoid form resubmission on refresh
    header("Location: add_task.php");
    exit();
}

// Fetch all employees from the database for the "assigned to" dropdown
$sql_employees = "SELECT username, empName FROM employees";
$employees_result = $conn->query($sql_employees);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Add New Task</h2>

        <!-- Display success or error message -->
        <?php if (isset($_SESSION['task_add_message'])): ?>
            <div class="message">
                <p><?php echo $_SESSION['task_add_message']; ?></p>
            </div>
            <?php unset($_SESSION['task_add_message']); ?>
        <?php endif; ?>

        <!-- Task Add Form -->
        <form action="add_task.php" method="POST">
            <div class="form-group">
                <label for="task_description">Task Description:</label>
                <textarea id="task_description" name="task_description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="assigned_to">Assign to:</label>
                <select id="assigned_to" name="assigned_to" required>
                    <option value="">Select a user</option>
                    <?php if ($employees_result->num_rows > 0): ?>
                        <?php while ($employee = $employees_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($employee['username']); ?>">
                                <?php echo htmlspecialchars($employee['empName']); ?> (<?php echo htmlspecialchars($employee['username']); ?>)
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">No users found</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Add Task</button>
            </div>
        </form>
    </div>

<!-- Style for the form -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
    }

    .container {
        max-width: 500px;
        margin: 50px auto;
        padding: 20px;
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    h2 {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    textarea, input[type="text"], select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .btn {
        background-color: #28a745;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #218838;
    }

    .message {
        margin-bottom: 20px;
        padding: 10px;
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 4px;
    }
</style>

</body>
</html>
