<?php
include 'config.php';
session_start();

// Initialize variables to avoid undefined errors
$logged_in_user = null;
$logged_in_admin = null;

// Check session for logged-in user or admin
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
    $sql = "SELECT * FROM user WHERE mail = ?"; // Updated 'email' to 'mail'
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $logged_in_user = $result->fetch_assoc();
    }
} elseif (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT * FROM admin WHERE adminId = ?"; // Admin logic is unchanged
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
                <span class="user-email"><?= $logged_in_user['mail'] ?></span> <!-- Updated 'email' to 'mail' -->
            </p>
        <?php elseif ($logged_in_admin): ?>
            <p class="login-">
                <span class="user-name"><?= $logged_in_admin['name'] ?></span><br>
                <span class="user-email"><?= $logged_in_admin['mail'] ?></span> <!-- Updated 'email' to 'mail' -->
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
				<a href="#" class="menu-item is-active home">Home</a>
                <a href="#" class="menu-item profile">Profile</a>
				<a href="#" class="menu-item about">About</a>			
				<a href="#" class="menu-item contact">Contact</a>
                <a href="#" class="menu-item log-out">Log out</a>
			</nav>

		</aside>

		<main class="content">
           <div class = 'panel'>   
                <div class="panel-ops">
                    <p class = 'education'>Education</p>
                    <p class="health">Health</p>
                    <p class="agriculture">Agriculture</p>
                    <p class="finance">Finance</p>
                    <p class="transport">Transport</p>
                </div>
                 <div class="panel-logo border">
                    <div class="panel-logo-link"></div>
                </div>        
            </div>
                        
		<div class="hero-section">
            <div class="hero-message">
                <p>You are on Bangladesh National Portal <a href="https://bangladesh.gov.bd">Click here to go to bangladesh.gov.bd</a></p>
            </div>
        </div>

        <div class="shop-section">
            <div class="box1 box">
                <div class="box-content">
                    <h2>Education</h2>
                    <div class="box-img" style="background-image: url('picture/education.png');"></div>
                 
                </div>
            </div>
            <div class="box2 box">
                 <h2>Health</h2>
                <div class="box-img" style="background-image: url('picture/health.png');"></div>
            
              
            </div>
            <div class="box3 box">
                <h2>Agriculture</h2>
                <div class="box-img" style="background-image: url('picture/agriculture.png');"></div>
            </div>
            <div class="box4 box">
                <h2>Finance</h2>
                <div class="box-img" style="background-image: url('picture/finance.png');"></div>
            </div>
            <div class="box4 box">
                <h2>Transport</h2>
                <div class="box-img" style="background-image: url('picture/transport.png');"></div>
            </div>
        </div>

        


		</main>

	</div>

<!-- footer -->
 <div class="footer-body">
    <footer class="footer">
  	 <div class="container">
  	 	<div class="row">
  	 		<div class="footer-col">
  	 			<h4>Quick Access</h4>
  	 			<ul> 				
  	 				<li><a href="#">our services</a></li>
  	 				
  	 			
  	 			</ul>
  	 		</div>
  	 		<div class="footer-col">
  	 			<h4>get help</h4>
  	 			<ul>
                   <li><a href="#">privacy policy</a></li>
  	 				
  	 			</ul>
  	 		</div>
  	 		
  	 		<div class="footer-col">
  	 			<h4>follow us</h4>
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
