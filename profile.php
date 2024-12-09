<?php
include 'config.php';
session_start();

// Fetch user or admin details
$profile_data = [];
$is_user = false;

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $sql = "SELECT user.user_id, user.name, user.mail, user.phone, COUNT(application.user_id) AS total_application 
            FROM user 
           left JOIN application ON user.user_id = application.user_id 
            WHERE user.user_id = ? 
            GROUP BY user.user_id, user.name, user.mail, user.phone";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $profile_data = $result->fetch_assoc();
        $is_user = true;
    }
} elseif (isset($_GET['admin_id'])) {
    $admin_id = $_GET['admin_id'];
    $sql = "SELECT * FROM admin WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $profile_data = $result->fetch_assoc();
    }
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Bangladesh National Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <!-- Navbar -->
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
        
        <div class="nav-second border">
            <p><span>Date</span></p>
            <p id="datetime">Loading...</p>
        </div>
    </div>
</header>

<div class="app">
    <div class="menu-toggle">
        <div class="hamburger">
            <span></span>
        </div>
    </div>
    <aside class="sidebar">
			<h3>Menu</h3>
			
			<nav class="menu">

                  <!-- Display index.php link for user -->
                  <?php if (isset($logged_in_user)): ?>
            <a href="index.php?user_id=<?= $logged_in_user['user_id'] ?>" class="menu-item is-active home">Home</a>
        
        <!-- Display amdin_home.php link for admin -->
        <?php elseif (isset($logged_in_admin)): ?>
            <a href="admin_home.php?admin_id=<?= $logged_in_admin['admin_id'] ?>" class="menu-item is-active home">Home</a>
        <?php endif; ?>
                <!-- for profile -->
                 <!-- Display Profile link for user -->
        <?php if (isset($logged_in_user)): ?>
            <a href="profile.php?user_id=<?= $logged_in_user['user_id'] ?>" class="menu-item profile">Profile</a>
        
        <!-- Display Profile link for admin -->
        <?php elseif (isset($logged_in_admin)): ?>
            <a href="profile.php?admin_id=<?= $logged_in_admin['admin_id'] ?>" class="menu-item profile">Profile</a>
        <?php endif; ?>
				<a href="#" class="menu-item about">About</a>			
				<a href="helpline.php" class="menu-item contact">Contact</a>
                <a href="logout.php" class="menu-item log-out">Log out</a>

			</nav>

		</aside>
    <main class="content">
        <div class="panel">
        <!-- <div class="panel-ops">
            <p class="<?= $currentPage == 'admin_home' ? 'active' : '' ?>">
                <a href="admin_home.php" class="panel-menu">Home</a>
            </p>
            <p class="<?= $currentPage == 'service' ? 'active' : '' ?>">
                <a href="service.php" class="panel-menu">Service</a>
            </p>
            <p class="<?= $currentPage == 'user' ? 'active' : '' ?>">
                <a href="user.php" class="panel-menu">User</a>
            </p>
            <p class="<?= $currentPage == 'applicant' ? 'active' : '' ?>">
                <a href="applicant.php" class="panel-menu">Applicant</a>
            </p>
            <p class="<?= $currentPage == 'feedback' ? 'active' : '' ?>">
              <a href="feedback.php" class="panel-menu">Feedback</a>
            </p>
        </div> -->
            <div class="panel-logo border">
                <div class="panel-logo-link"></div>
            </div>
        </div>
        <div class="service-body">
    <div class="profile-container">
        <h2 class="profile-heading">Profile Details</h2>
        <div class="profile-card">
            <div class="profile-photo">
                <i class="fa-solid fa-user-circle fa-5x"></i> <!-- Placeholder for user/admin photo -->
            </div>
            <div class="profile-details">
                <?php if ($is_user): ?>
                    <p><strong>Name:</strong> <?= htmlspecialchars($profile_data['name']); ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($profile_data['mail']); ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($profile_data['phone']); ?></p>
                    <p><strong>Total Applications:</strong> <?= htmlspecialchars($profile_data['total_application']); ?></p>
                <?php else: ?>
                    <p><strong>Admin Name:</strong> <?= htmlspecialchars($profile_data['name']); ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($profile_data['mail']); ?></p>
                    <p><strong>Role:</strong> Administrator</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="action-buttons">
           
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
    </div>
</div>

         
    </main>
</div>

<div class="footer-body">
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col">
                    <h4>Quick Access</h4>
                    <ul>
                        <li><a href="#">Our Services</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Get Help</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
<script src="script.js"></script>
</body>
</html>
