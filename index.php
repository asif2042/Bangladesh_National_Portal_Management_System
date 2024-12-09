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
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"> -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>


<div class="navbar">
        <div class="nav_logo border">
            <div class="logo"></div>
        </div>
        



    <!-- <div class="nav-search">
            <select class="search-select">
                <option>All</option>
            </select>
            <input placeholder="Search Bangladesh National Portal" class="search-input" id="search-input" onkeyup="fetchSearchResults(this.value)">
            <div class="search-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <div id="search-results" class="search-results"></div>
     </div> -->


     <div class="nav-search">
            <div class="search-container">
                <input type="text" id="search-bar" class="search-input" placeholder="Search services">
                <div id="search-results" class="search-results"></div>
            </div>
            <div class="search-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
    </div>




    <div class="nav-signin border">
    <?php if ($logged_in_user): ?>
        <a href="profile.php?user_id=<?= htmlspecialchars($logged_in_user['user_id']) ?>" class="profile-link">
            <p class="login-">
                <span class="user-name"><?= htmlspecialchars($logged_in_user['name']) ?></span><br>
                <span class="user-email"><?= htmlspecialchars($logged_in_user['mail']) ?></span>
            </p>
        </a>
    <?php elseif ($logged_in_admin): ?>
        <a href="profile.php?admin_id=<?= htmlspecialchars($logged_in_admin['admin_id']) ?>" class="profile-link">
            <p class="login-">
                <span class="user-name"><?= htmlspecialchars($logged_in_admin['name']) ?></span><br>
                <span class="user-email"><?= htmlspecialchars($logged_in_admin['mail']) ?></span>
            </p>
        </a>
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
                <!-- for profile -->
                 <!-- Display Profile link for user -->
        <?php if (isset($logged_in_user)): ?>
            <a href="profile.php?user_id=<?= $logged_in_user['user_id'] ?>" class="menu-item profile">Profile</a>
        
        <!-- Display Profile link for admin -->
        <?php elseif (isset($logged_in_admin)): ?>
            <a href="profile.php?admin_id=<?= $logged_in_admin['admin_id'] ?>" class="menu-item profile">Profile</a>
        <?php endif; ?>
				<a href="about.php" class="menu-item about">About</a>			
				<a href="helpline.php" class="menu-item contact">Contact</a>
                <a href="logout.php" class="menu-item log-out">Log out</a>

			</nav>

		</aside>

		<main class="content">
           <div class = 'panel'>   



           <?php
            $currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
            ?>
            <div class="panel-ops">
                <p class="<?= $currentPage == 'index' ? 'active' : '' ?>"><a href="index.php", class="panel-menu">Home</a></p>
                <p class="<?= $currentPage == 'education' ? 'active' : '' ?>"><a href="education.php", class="panel-menu">Education</a></p>
                <p class="<?= $currentPage == 'health' ? 'active' : '' ?>"><a href="health.php", class="panel-menu">Health</a></p>
                <p class="<?= $currentPage == 'agriculture' ? 'active' : '' ?>"><a href="agriculture.php", class="panel-menu">Agriculture</a></p>
                <p class="<?= $currentPage == 'finance' ? 'active' : '' ?>"><a href="finance.php", class="panel-menu">Finance</a></p>
                <p class="<?= $currentPage == 'transport' ? 'active' : '' ?>"><a href="transport.php", class="panel-menu">Transport</a></p>
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
