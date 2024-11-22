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

// Fetch records from the database
$sql = "
SELECT 
    service.service_id,
    service.service_name,
    service_sector.sector_name
FROM service
JOIN service_sector ON service.sector_id = service_sector.sector_id
ORDER BY service.service_id ASC
";

$result = $conn->query($sql);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Service Management</title>
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
            <a href="#" class="menu-item profile">Profile</a>
            <a href="#" class="menu-item about">About</a>
            <a href="#" class="menu-item contact">Contact</a>
            <a href="logout.php" class="menu-item log-out">Log out</a>

        </nav>
    </aside>

    <main class="content">


    <div class="panel">
            <?php
            $currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
            ?>
            <div class="panel-ops">
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
            <h2 class="service-heading">Service Management</h2>
            <button class="btn-add" onclick="openModal('add')">Add New Service</button>
            <table id="serviceTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Service ID</th>
                        <th>Service Name</th>
                        <th>Sector Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['service_id'] ?></td>
                            <td><?= $row['service_name'] ?></td>
                            <td><?= $row['sector_name'] ?></td>
                            <td>
                                <button class="btn-edit" onclick="editRecord(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                                <button class="btn-delete" onclick="deleteRecord(<?= $row['service_id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div id="serviceModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3 id="modal-title">Add/Edit Service</h3>
                <form id="serviceForm">
                    <input type="hidden" name="service_id" id="serviceId">
                    <label for="service_name">Service Name</label>
                    <input type="text" id="service_name" name="service_name" required>
                    <label for="sector_name">Sector Name</label>
                    <select id="sector_name" name="sector_name">
                        <option value="Education">Education</option>
                        <option value="Health">Health</option>
                        <option value="Agriculture">Agriculture</option>
                        <option value="Finance">Finance</option>
                        <option value="Transport">Transport</option>
                    </select>
                    <button type="submit" id="saveButton">Save</button>
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
<script>
    $(document).ready(function () {
        $('#serviceTable').DataTable();
    });

    function openModal(mode) {
        document.getElementById('serviceModal').style.display = 'block';
        if (mode === 'add') {
            document.getElementById('modal-title').innerText = 'Add New Service';
            document.getElementById('serviceForm').reset();
            document.getElementById('serviceId').value = '';
        }
    }

    function closeModal() {
        document.getElementById('serviceModal').style.display = 'none';
    }

    function editRecord(record) {
        document.getElementById('serviceId').value = record.service_id || '';
        document.getElementById('service_name').value = record.service_name || '';
        document.getElementById('sector_name').value = record.sector_name || 'Education';
        openModal('edit');
    }
//     function deleteRecord(serviceId) {
//     if (confirm('Are you sure you want to delete this service?')) {
//         console.log('Deleting service ID:', serviceId);

//         $.post('delete_service.php', { service_id: serviceId }, function (response) {
//             alert(response.message);
//             if (response.success) {
//                 location.reload();
//             }
//         }, 'json').fail(function (xhr) {
//             alert('Error: ' + xhr.responseText);
//         });
//     }
// }
function deleteRecord(serviceId) {
    if (confirm('Are you sure you want to delete this service?')) {
        fetch('service_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'delete',
                service_id: serviceId
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        })
        .catch(error => {
            console.error('Error:', error.message);
            alert('An error occurred: ' + error.message);
        });
    }
}




//     document.getElementById('saveButton').addEventListener('click', function (e) {
//     e.preventDefault();
//     const formData = new FormData(document.getElementById('serviceForm'));
//     const sectorName = formData.get('sector_name');

//     console.log('Submitting data:', {
//         service_id: formData.get('service_id'),
//         service_name: formData.get('service_name'),
//         sector_name: sectorName
//     });

//     // Fetch sector_id based on sector_name
//     fetch('get_sector_id.php', {
//         method: 'POST',
//         body: JSON.stringify({ sector_name: sectorName }),
//         headers: { 'Content-Type': 'application/json' }
//     })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 formData.append('sector_id', data.sector_id);

//                 // Send final data to backend
//                 return fetch('add_edit_service.php', { method: 'POST', body: formData });
//             } else {
//                 throw new Error(data.message);
//             }
//         })
//         .then(response => response.json())
//         .then(data => {
//             alert(data.message);
//             if (data.success) location.reload();
//         })
//         .catch(error => {
//             alert('Error: ' + error.message);
//             console.error('Error:', error);
//         });
// });

document.getElementById('saveButton').addEventListener('click', function (e) {
    e.preventDefault();

    const formData = new FormData(document.getElementById('serviceForm'));
    const action = formData.get('service_id') ? 'edit' : 'add'; // Determine action

    fetch('service_handler.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: action,
            service_id: formData.get('service_id'),
            service_name: formData.get('service_name'),
            sector_name: formData.get('sector_name')
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    })
    .catch(error => alert('Error: ' + error.message));
});







 // Add error handling for fetch
fetch('add_edit_service.php', { method: 'POST', body: formData })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        alert('An error occurred: ' + error.message);
        console.error('Error:', error);
    });



$('#serviceTable').DataTable().ajax.reload();


</script>
</body>
</html>
