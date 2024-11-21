<?php
include 'config.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Unauthorized access. Admin login required.']);
    exit;
}

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $applicant_id = isset($_POST['applicant_id']) ? intval($_POST['applicant_id']) : null;
    $application_status = isset($_POST['application_status']) ? $_POST['application_status'] : null;

    // Validate required fields
    if ($applicant_id === null || $application_status === null) {
        echo json_encode(['error' => 'Missing required fields (applicant_id or application_status).']);
        exit;
    }

    // Update the applicant's status
    $sql = "UPDATE applicant SET status = ? WHERE applicant_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $application_status, $applicant_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Application status updated successfully.']);
        } else {
            echo json_encode(['error' => 'Failed to update application status.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Failed to prepare the SQL statement.']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method.']);
}

$conn->close();
?>
