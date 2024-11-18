<?php
include 'config.php';
session_start();

// Initialize variables to avoid undefined errors
$logged_in_user = null;
$logged_in_admin = null;

// Check session for logged-in user or admin
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bangladesh National Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="nav_logo border">
            <div class="logo"></div>
        </div>

        <div class="nav-search">
            <select class="search-select">
                <option>All</option>
            </select>
            <input placeholder="Search Bangladesh National Portal" class="search-input">
            <div class="search-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>

        <div class="nav-signin border">
        <?php if ($logged_in_user): ?>
            <p class="login-">
                <span class="user-name"><?= $logged_in_user['name'] ?></span><br>
                <span class="user-email"><?= $logged_in_user['email'] ?></span>
            </p>
        <?php elseif ($logged_in_admin): ?>
            <p class="login-">
                <span class="user-name"><?= $logged_in_admin['name'] ?></span><br>
                <span class="user-email"><?= $logged_in_admin['email'] ?></span>
            </p>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
      </div>

        <div class="nav-second border">
            <p><span>Date</span></p>
            <p class="nav-second" id="datetime">Loading...</p> <!-- Placeholder for dynamic date -->
        </div>
    </div>

     <div class="panel">

     
    <!-- Hamburger icon (panel-all) -->
    <div class="panel-all">
        <i class="fa-solid fa-bars"></i>
    </div>

    <!-- //dfjdkjfdk -->




        <div class="panel-ops">
            <p>Home</p>
            <p>About Bangladesh</p>
            <p>e-Service</p>
            <p>Notification</p>
            <p>Forms</p>
        </div>

        <div class="panel-logo border">
            <div class="panel-logo-link"></div>
        </div>
    </div>
</header>

<div class="hero-section">
    <div class="hero-message">
        <p>You are on Bangladesh National Portal <a>Click here to go to bangladesh.gov.bd</a></p>
    </div>
</div>

<div class="shop-section">
    <div class="box1 box">
        <div class="box-content">
            <h2>Service</h2>
            <div class="box-img" style="background-image: url('service-logo.png');"></div>
            <p>See more</p>
        </div>
    </div>
    <div class="box2 box">
        <h2>Service</h2>
        <div class="box-img" style="background-image: url('service-logo.png');"></div>
        <p>See more</p>
    </div>
    <div class="box3 box">box3</div>
    <div class="box4 box">box4</div>
</div>

<div class="nav-signin border">
    <p><a href="signup.php">Sign Up</a></p>
</div>

<script src="script.js"></script>
</body>
</html>
