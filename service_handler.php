<?php
include 'config.php';
header('Content-Type: application/json');
session_start();

// Check for the 'action' parameter
if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $_POST['action'];

try {
    switch ($action) {
        case 'add':
        case 'edit':
            // Add or Edit Service
            $service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : null;
            $service_name = $_POST['service_name'];
            $sector_name = $_POST['sector_name'];

            // Get sector_id from sector_name
            $sector_stmt = $conn->prepare("SELECT sector_id FROM service_sector WHERE sector_name = ?");
            $sector_stmt->bind_param("s", $sector_name);
            $sector_stmt->execute();
            $sector_result = $sector_stmt->get_result();

            if ($sector_result->num_rows > 0) {
                $sector_id = $sector_result->fetch_assoc()['sector_id'];
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid sector name']);
                exit;
            }

            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO service (service_name, sector_id) VALUES (?, ?)");
                $stmt->bind_param("si", $service_name, $sector_id);
            } else {
                $stmt = $conn->prepare("UPDATE service SET service_name = ?, sector_id = ? WHERE service_id = ?");
                $stmt->bind_param("sii", $service_name, $sector_id, $service_id);
            }

            $stmt->execute();
            echo json_encode(['success' => true, 'message' => ucfirst($action) . ' successful']);
            break;

        case 'delete':
            // Delete Service
            // $service_id = intval($_POST['service_id']);
            // $stmt = $conn->prepare("DELETE FROM service WHERE service_id = ?");
            // $stmt->bind_param("i", $service_id);
            // $stmt->execute();

            // echo json_encode(['success' => true, 'message' => 'Delete successful']);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Check if the action is delete
                if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                    $service_id = $_POST['service_id'];
            
                    // Validate service_id
                    if (empty($service_id) || !is_numeric($service_id)) {
                        echo json_encode(['success' => false, 'message' => 'Invalid service ID.']);
                        exit;
                    }
            
                    // Delete query
                    $sql = "DELETE FROM service WHERE service_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $service_id);
            
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Service deleted successfully.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to delete service.']);
                    }
                    $stmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            }



            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
