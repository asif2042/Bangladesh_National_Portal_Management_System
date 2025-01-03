

<?php
include 'config.php';
session_start();

// Initialize variables
$logged_in_user = null;
$service_id = null;

// Check session for logged-in user
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
    $sql = "SELECT * FROM user WHERE mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $logged_in_user = $result->fetch_assoc();
        $_SESSION['user_id'] = $logged_in_user['user_id']; // Set user_id in session
    } else {
        die("Error: User not found in database.");
    }
} else {
    die("Error: User not logged in.");
}

// // if (isset($_GET['service_id'])) {
// //     $service_id = $_GET['service_id'];
// //     echo "<script>console.log('Service ID: $service_id');</script>";
// // } else {
// //     die("Error: Service ID not provided.");
// // }

// // Get the service_id from the URL
// $service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;

if ($service_id === 0) {
    error_log("Service ID not provided or invalid.");
    echo "<script>console.error('Error: Service ID not provided or invalid.');</script>";
    // You can redirect the user or show a user-friendly message here instead of terminating the script.
}



$sql = "SELECT * FROM service WHERE service_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = null;

if ($result->num_rows > 0) {
    $service = $result->fetch_assoc();
} else {
    die("Error: Service not found. Check service_id: $service_id");
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_name = $_POST['form_name'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $education = $_POST['education'];
    $address = $_POST['address'];
    $nationality = $_POST['nationality'];
    $gender = $_POST['gender'];
    $mail = $_POST['mail'];
    $comment = $_POST['comment'];

    // Insert into form table
    $form_sql = "INSERT INTO form (form_name, service_id, first_name, last_name, phone, education, address, nationality, gender, mail) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($form_sql);
    $stmt->bind_param("sissssssss", $form_name, $service_id, $first_name, $last_name, $phone, $education, $address, $nationality, $gender, $mail);
//     // Debugging the form data
// var_dump($form_name, $service_id, $first_name, $last_name, $phone, $education, $address, $nationality, $gender, $mail);
// exit();

    if ($stmt->execute()) {
        // Get the newly inserted form ID
        $form_id = $conn->insert_id;

        // Insert into applicant table
        $applicant_sql = "INSERT INTO applicant (status) VALUES ('Pending')";






        if ($conn->query($applicant_sql)) {
            $applicant_id = $conn->insert_id;

            // Insert into feedback table
    $feedback_sql = "INSERT INTO feedback (applicant_id, comments) VALUES (?, ?)";
    $feedback_stmt = $conn->prepare($feedback_sql); // Create a new prepared statement
    if ($feedback_stmt) {
        $feedback_stmt->bind_param("is", $applicant_id, $comment); // Bind the correct parameters
        if (!$feedback_stmt->execute()) {
            echo "<script>alert('Error inserting into feedback: " . $feedback_stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error preparing feedback SQL: " . $conn->error . "');</script>";
    }


            // Insert into application table
            $user_id = $_SESSION['user_id']; // Use session-stored user_id
            $application_sql = "INSERT INTO application (user_id, applicant_id, service_id, date_time) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
            $stmt = $conn->prepare($application_sql);
            $stmt->bind_param("iii", $user_id, $applicant_id, $service_id);
            if ($stmt->execute()) {
                // Insert into user_service_form table
                $user_service_sql = "INSERT INTO user_service_form (user_id, service_id, form_id, submission_date) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
                $stmt = $conn->prepare($user_service_sql);
                $stmt->bind_param("iii", $user_id, $service_id, $form_id);
                if ($stmt->execute()) {
                    echo "<script>alert('Form submitted successfully and linked to user!');</script>";
                } else {
                    echo "<script>alert('Error inserting into user_service_form: " . $stmt->error . "');</script>";
                }
            } else {
                echo "<script>alert('Error inserting into application: " . $stmt->error . "');</script>";
            }
        } else {
            echo "<script>alert('Error inserting into applicant: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error submitting the form: " . $stmt->error . "');</script>";
    }

    sleep(4);
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
                <!-- for profile -->
                <?php if (isset($user_id)): ?>
                    <a href="profile.php?user_id=<?= $user_id ?>" class="menu-item profile">Profile</a>
                <?php elseif (isset($admin_id)): ?>
                    <a href="profile.php?admin_id=<?= $admin_id ?>" class="menu-item profile">Profile</a>
                <?php endif; ?>

				<a href="#" class="menu-item about">About</a>			
				<a href="#" class="menu-item contact">Contact</a>
                <a href="logout.php" class="menu-item log-out">Log out</a>

			</nav>

		</aside>

		<main class="content">
           <div class = 'panel'>   



           <?php
            $currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
            ?>
            <div class="panel-ops">
                <p class="<?= $currentPage == 'index' ? 'active' : '' ?>"><a href="index.php">Home</a></p>
                <p class="<?= $currentPage == 'education' ? 'active' : '' ?>"><a href="education.php">Education</a></p>
                <p class="<?= $currentPage == 'health' ? 'active' : '' ?>"><a href="health.php">Health</a></p>
                <p class="<?= $currentPage == 'agriculture' ? 'active' : '' ?>"><a href="agriculture.php">Agriculture</a></p>
                <p class="<?= $currentPage == 'finance' ? 'active' : '' ?>"><a href="finance.php">Finance</a></p>
                <p class="<?= $currentPage == 'transport' ? 'active' : '' ?>"><a href="transport.php">Transport</a></p>
            </div>




                <div class="panel-logo border">
                    <div class="panel-logo-link"></div>
                </div>        
           </div>
                    
        


   <div class="application-form">
    <h2>Application Form</h2>
    <form method="POST" action="">
        <!-- <label for="form_name">Form Name</label> -->
        <input type="hidden" name="form_name" value="<?= $service['service_name'] ?>">


        <label for="first_name">First Name</label>
        <input type="text" name="first_name" id="first_name" placeholder="Enter first name" required>
        
        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" id="last_name" placeholder="Enter last name (optional)">

        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" placeholder="Enter phone number" required>

        <label for="education">Education</label>
        <input type="text" name="education" id="education" placeholder="Enter education details" required>

        <label for="address">Address</label>
        <textarea name="address" id="address" placeholder="Enter address" required></textarea>

        <label for="nationality">Nationality</label>
        <input type="text" name="nationality" id="nationality" placeholder="Enter nationality" required>

        <label for="gender">Gender</label>
        <select name="gender" id="gender" required>
            <option value="">Select gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="mail">Email</label>
        <input type="email" name="mail" id="mail" placeholder="Enter email" required>
        <?php if ($logged_in_user): ?>
            <span class="auto-fill" onclick="autoFill()">Use logged-in user info</span>
        <?php endif; ?>

        <label for="comment">Comment</label>
        <input type="text" name="comment" id="comment" placeholder="We are looking for your valuable feedback.(optional)">


        <div class="btn-group">
            <button type="submit">Submit</button>
        </div>
    </form>
</div>

        

<script>
function autoFill() {
    const loggedInUser = <?= json_encode($logged_in_user); ?>;
    if (loggedInUser) {
        document.getElementById('first_name').value = loggedInUser.name.split(' ')[0];
        document.getElementById('last_name').value = loggedInUser.name.split(' ')[1] || '';
        document.getElementById('phone').value = loggedInUser.contactNumber;
        document.getElementById('mail').value = loggedInUser.mail;
    }
}
</script>


          


         
		
        


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
