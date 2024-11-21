<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($userId > 0) {
        // Delete related records from application and applicant
        $deleteApplications = "DELETE FROM application WHERE user_id = $userId";
        $conn->query($deleteApplications);

        $deleteApplicants = "DELETE FROM applicant WHERE applicant_id IN (SELECT applicant_id FROM application WHERE user_id = $userId)";
        $conn->query($deleteApplicants);

        // Delete related records from user_service_form
        $deleteUserServiceForm = "DELETE FROM user_service_form WHERE user_id = $userId";
        $conn->query($deleteUserServiceForm);

        // Delete related forms
        $deleteForms = "DELETE FROM form WHERE form_id IN (SELECT form_id FROM user_service_form WHERE user_id = $userId)";
        $conn->query($deleteForms);

        // Delete the user
        $deleteUser = "DELETE FROM user WHERE user_id = $userId";
        if ($conn->query($deleteUser) === TRUE) {
            echo json_encode(["success" => true, "message" => "User and related records deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error deleting user: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid user ID."]);
    }
}

$conn->close();
?>
