<?php
// Include database configuration
include 'config.php';

// Initialize variables
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contactNumber'];

    // Check if the email already exists
    $checkEmailQuery = "SELECT * FROM user WHERE mail = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email is already registered!";
    } else {
        // Insert new user into the database
        $insertQuery = "INSERT INTO user (name, mail, phone) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sss", $username, $email, $contactNumber);

        if ($stmt->execute()) {
            $success = "Signup successful! You can now log in.";
            header('Location: success.php');
            sleep(5);
            header('Location: index.php');
            
        } else {
            $error = "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body class = 'signup-body'>
    <div id="signup-container" class="container">
        <h1 class="signup-header">Sign Up</h1>
        <?php if (!empty($error)) : ?>
            <p class="message error"><?php echo $error; ?></p>
        <?php elseif (!empty($success)) : ?>
            <p class="message success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form id="signup-form" method="POST" action="">
            <div class="form-group">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-input" required placeholder="Enter your username">
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-input" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="contactNumber" class="form-label">Contact Number:</label>
                <input type="text" id="contactNumber" name="contactNumber" class="form-input" required placeholder="Enter your contact number">
            </div>

            <button type="submit" class="form-button">Sign Up</button>
        </form>
        <p class="redirect">Already have an account? <a href="login.php" class="redirect-link">Log in here</a></p>
    </div>
</body>
</html>

