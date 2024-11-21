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
    <!-- Similar header design as in applicant.php -->
</header>

<div class="app">
    <div class="menu-toggle">
        <div class="hamburger">
            <span></span>
        </div>
    </div>
    <aside class="sidebar">
        <!-- Similar sidebar design as in applicant.php -->
    </aside>

    <main class="content">
        <div class="admin-body">
            <h2>Service Management</h2>
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
    function deleteRecord(serviceId) {
    if (confirm('Are you sure you want to delete this service?')) {
        console.log('Deleting service ID:', serviceId);

        $.post('delete_service.php', { service_id: serviceId }, function (response) {
            alert(response.message);
            if (response.success) {
                location.reload();
            }
        }, 'json').fail(function (xhr) {
            alert('Error: ' + xhr.responseText);
        });
    }
}



    document.getElementById('saveButton').addEventListener('click', function (e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('serviceForm'));
    const sectorName = formData.get('sector_name');

    console.log('Submitting data:', {
        service_id: formData.get('service_id'),
        service_name: formData.get('service_name'),
        sector_name: sectorName
    });

    // Fetch sector_id based on sector_name
    fetch('get_sector_id.php', {
        method: 'POST',
        body: JSON.stringify({ sector_name: sectorName }),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                formData.append('sector_id', data.sector_id);

                // Send final data to backend
                return fetch('add_edit_service.php', { method: 'POST', body: formData });
            } else {
                throw new Error(data.message);
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        })
        .catch(error => {
            alert('Error: ' + error.message);
            console.error('Error:', error);
        });
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
