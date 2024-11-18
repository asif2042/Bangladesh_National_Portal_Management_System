<?php
// Include database configuration
include 'config.php';

// Set the time zone to Dhaka, Bangladesh
date_default_timezone_set('Asia/Dhaka');

session_start();

// Variables for errors and login display
$user_error = $admin_error = "";
$logged_in_user = null;
$logged_in_admin = null;

// Check session for logged-in user/admin
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $logged_in_user = $result->fetch_assoc();
    }
} elseif (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT * FROM admin WHERE adminId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $logged_in_admin = $result->fetch_assoc();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_login'])) {
        // User login form submission
        $email = trim($_POST['email']);
        if (!empty($email)) {
            $sql = "SELECT * FROM user WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $_SESSION['user_email'] = $email;
                $user_data = $result->fetch_assoc();
                $user_id = $user_data['userId'];
                $login_datetime = date("Y-m-d H:i:s");
                $login_type = 'user';

                // Insert login log
                $log_sql = "INSERT INTO login_log (user_id, email, login_datetime, login_type) VALUES (?, ?, ?, ?)";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param("isss", $user_id, $email, $login_datetime, $login_type);
                $log_stmt->execute();

                header('Location: index.php');
                exit();
            } else {
                $user_error = "Invalid email address!";
            }
        } else {
            $user_error = "Please enter your email.";
        }
    } elseif (isset($_POST['admin_login'])) {
        // Admin login form submission
        $admin_mail = trim($_POST['admin_mail']);
        $admin_password = trim($_POST['admin_password']);

        if (!empty($admin_mail) && !empty($admin_password)) {
            $sql = "SELECT * FROM admin WHERE mail = ? AND admin_password = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $admin_mail, $admin_password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $admin_data = $result->fetch_assoc();
                $_SESSION['admin_id'] = $admin_data['adminId'];
                $login_datetime = date("Y-m-d H:i:s");
                $login_type = 'admin';

                // Insert login log
                $log_sql = "INSERT INTO login_log (admin_id, email, login_datetime, login_type) VALUES (?, ?, ?, ?)";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param("isss", $admin_data['adminId'], $admin_mail, $login_datetime, $login_type);
                $log_stmt->execute();

                header('Location: index.php');
                exit();
            } else {
                $admin_error = "Invalid Admin Email or Password!";
            }
        } else {
            $admin_error = "Please fill in all fields.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function toggleAdminLogin() {
            var adminLoginForm = document.getElementById('admin-login-form');
            var userLoginForm = document.getElementById('user-login-form');
            var toggleButton = document.getElementById('toggle-login');
            
            if (adminLoginForm.style.display === 'none') {
                adminLoginForm.style.display = 'block';
                userLoginForm.style.display = 'none';
                toggleButton.innerHTML = "Back to User Login";
            } else {
                adminLoginForm.style.display = 'none';
                userLoginForm.style.display = 'block';
                toggleButton.innerHTML = "Admin Login";
            }
        }
    </script>
</head>
<body class="login-body">
    <div id="login-container">
        <h1 class="signup-header">Bangladesh National Portal</h1>

        <!-- User Login Section -->
        <div class="form-group" id="user-login-form">
            <h2>User Login</h2>
            <?php if (!empty($user_error)) : ?>
                <p class="message error"><?php echo $user_error; ?></p>
            <?php endif; ?>
            <form method="POST" action="" id="user-login">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-input" required placeholder="Enter your email">
                <button type="submit" name="user_login" class="form-button">Login as User</button>
            </form>
        </div>

        <!-- Admin Login Section -->
        <div class="form-group" id="admin-login-form" style="display: none;">
            <h2>Admin Login</h2>
            <?php if (!empty($admin_error)) : ?>
                <p class="message error"><?php echo $admin_error; ?></p>
            <?php endif; ?>
            <form method="POST" action="" id="admin-login">
                <label for="admin_mail" class="form-label">Admin Email:</label>
                <input type="email" id="admin_mail" name="admin_mail" class="form-input" required placeholder="Enter Admin Email">
                <label for="admin_password" class="form-label">Password:</label>
                <input type="password" id="admin_password" name="admin_password" class="form-input" required placeholder="Enter Admin Password">
                <button type="submit" name="admin_login" class="form-button">Login as Admin</button>
            </form>
        </div>

        <!-- Toggle Button -->
        <div>
            <button type="button" id="toggle-login" class="toggle-button" onclick="toggleAdminLogin()">Log in as Admin</button>
        </div>

        <!-- Sign-up Redirect -->
        <p class="redirect">Don't you have an account? <a href="signup.php" class="redirect-link">Sign up here</a></p>
    </div>
</body>
</html>