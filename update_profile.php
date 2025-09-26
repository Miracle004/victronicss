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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the updated email and phone
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    
    // Profile picture upload logic
    $profile_picture = $_FILES['profile_picture']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_picture);
    $uploadOk = 1;
    
    if (!empty($profile_picture)) {
        // Check if the uploaded file is an image
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES['profile_picture']['tmp_name']);
        
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (limit to 2MB)
        if ($_FILES["profile_picture"]["size"] > 2097152) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow only specific file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // Attempt to upload the file
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                // Update the profile picture in the database
                $sql = "UPDATE employees SET profile_picture='$target_file' WHERE username='$current_user'";
                if ($conn->query($sql) === TRUE) {
                    echo "Profile picture updated successfully.";
                } else {
                    echo "Error updating profile picture: " . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Update email and phone in the database
    $sql = "UPDATE employees SET email='$email', phone='$phone' WHERE username='$current_user'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Profile updated successfully.";
        // Redirect back to profile page (you can set a success message here)
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

$conn->close();
?>
