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
    $sql = "SELECT * FROM admin WHERE adminId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $logged_in_admin = $result->fetch_assoc();
    }
}

// Fetch records from the database
$sql = "
SELECT 
    applicant.applicant_id,  
    user.name, 
    user.mail, 
    user.phone, 
    feedback.comments AS review, 
    applicant.date_time AS application_date, 
    applicant.status AS application_status
FROM applicant 
JOIN application ON applicant.applicant_id = application.applicant_id
JOIN user ON user.user_id = application.user_id
JOIN service ON application.service_id = service.service_id
LEFT JOIN feedback ON applicant.applicant_id = feedback.feedback_id
ORDER BY applicant.applicant_id ASC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Applicant Management</title>
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
                <p class="login-">
                    <span class="user-name"><?= $logged_in_user['name'] ?></span><br>
                    <span class="user-email"><?= $logged_in_user['mail'] ?></span>
                </p>
            <?php elseif ($logged_in_admin): ?>
                <p class="login-">
                    <span class="user-name"><?= $logged_in_admin['name'] ?></span><br>
                    <span class="user-email"><?= $logged_in_admin['mail'] ?></span>
                </p>
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
            <a href="#" class="menu-item profile">Profile</a>
            <a href="#" class="menu-item about">About</a>
            <a href="#" class="menu-item contact">Contact</a>
            <a href="#" class="menu-item log-out">Log out</a>
        </nav>
    </aside>

    <main class="content">
        <div class="panel">
            <?php
            $currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
            ?>
            <div class="panel-ops">
                <p class="<?= $currentPage == 'admin_home' ? 'active' : '' ?>">
                    <a href="admin_home.php">Home</a>
                </p>
                <p class="<?= $currentPage == 'service' ? 'active' : '' ?>">
                    <a href="service.php">Service</a>
                </p>
                <p class="<?= $currentPage == 'user' ? 'active' : '' ?>">
                    <a href="user.php">User</a>
                </p>

                <p class="<?= $currentPage == 'applicant' ? 'active' : '' ?>">
                    <a href="applicant.php">Applicant</a>
                </p>
                <p class="<?= $currentPage == 'helpline' ? 'active' : '' ?>">
                    <a href="helpline.php">Helpline</a>
                </p>
                <p class="<?= $currentPage == 'feedback' ? 'active' : '' ?>">
                    <a href="feedback.php">Feedback</a>
                </p>
            </div>

            <div class="panel-logo border">
                <div class="panel-logo-link"></div>
            </div>
        </div>

        <div class="admin-body">
            <h2>Applicant Management</h2>
            <button  class="applicant-btn-add to-hide" onclick="openModal('add')">Add New Applicant</button>
            <table id="userTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Applicant ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Review</th>
                        <th>Application Date</th>
                        <th>Application Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['applicant_id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['mail'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td><?= $row['review'] ?? 'N/A' ?></td>
                            <td><?= $row['application_date'] ?></td>
                            <td><?= $row['application_status'] ?></td>
                            <td>
                                <button class="btn-edit" onclick="editRecord(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                                <button class="btn-delete to-hide" onclick="deleteRecord(<?= $row['applicant_id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>



        <!-- information section -->


        
                <!-- Modal -->
                <div id="userModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal()">&times;</span>
                        <h3 id="modal-title">Add/Edit User</h3>
                        <form id="userForm">
                            <input type="hidden" name="user_id" id="userId">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                            <label for="mail">Email</label>
                            <input type="email" id="mail" name="mail" required>
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" required>
                            <!-- <label for="user_role">User Role</label>
                            <input type="text" id="user_role" name="user_role"> -->
                            <label for="feedback_comments">Feedback Comments</label>
                            <textarea id="feedback_comments" name="feedback_comments"></textarea>
                            <label for="application_status">Application Status</label>
                            <!-- <input type="text" id="application_status" name="application_status"> -->
                            <select id="application_status" name="application_status">
                                <option value="Pending" selected>Pending</option>
                                <option value="Processed">Processed</option>
                                <option value="Under Review">Under Review</option>

                            </select>

                            <button type="submit">Save</button>
                        </form>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="script.js"></script>
</body>
</html>