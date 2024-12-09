<?php
include 'config.php';
session_start();

// Initialize variables to avoid undefined errors
$logged_in_user = null;
$logged_in_admin = null;

// Check session for logged-in user or admin
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
    $sql = "SELECT * FROM user WHERE mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $logged_in_user = $result->fetch_assoc();
    }
} elseif (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT * FROM admin WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $logged_in_admin = $result->fetch_assoc();
    }
}

// SQL query to fetch feedback data
$sql = "
SELECT 
    feedback.feedback_id, 
    user.name AS applicant_name, 
    user.mail, 
    feedback.comments 
FROM feedback 
JOIN applicant ON feedback.applicant_id = applicant.applicant_id 
JOIN application ON applicant.applicant_id = application.applicant_id 
JOIN user ON application.user_id = user.user_id
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Feedback Management</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="nav_logo border">
            <div class="logo"></div>
        </div>
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
            <p class="nav-second" id="datetime">Loading...</p>
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
                <?php if (isset($user_id)): ?>
                    <a href="profile.php?user_id=<?= $user_id ?>" class="menu-item profile">Profile</a>
                <?php elseif (isset($admin_id)): ?>
                    <a href="profile.php?admin_id=<?= $admin_id ?>" class="menu-item profile">Profile</a>
                <?php endif; ?>

				<a href="about.php" class="menu-item about">About</a>			
				<a href="helpline.php" class="menu-item contact">Contact</a>
                <a href="logout.php" class="menu-item log-out">Log out</a>

			</nav>
    </aside>

    <main class="content">
        <div class="panel">
            <<div class="panel-ops">
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
</div>

            <div class="panel-logo border">
                <div class="panel-logo-link"></div>
            </div>
        </div>

        <div class="admin-body">
            <h2 class="service-heading">Feedback Management</h2>
            <table id="feedbackTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Feedback ID</th>
                        <th>Applicant Name</th>
                        <th>Email</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['feedback_id']) ?></td>
                            <td><?= htmlspecialchars($row['applicant_name']) ?></td>
                            <td><?= htmlspecialchars($row['mail']) ?></td>
                            <td><?= htmlspecialchars($row['comments']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#feedbackTable').DataTable();
    });
</script>
</body>
</html>
