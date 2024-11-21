<?php
include 'config.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['service_name']) && !empty($data['sector_id'])) {
    $service_name = $data['service_name'];
    $sector_id = $data['sector_id'];
    $service_id = $data['service_id'] ?? null;

    if ($service_id) {
        // Update service
        $stmt = $conn->prepare("UPDATE service SET service_name = ?, sector_id = ? WHERE service_id = ?");
        $stmt->bind_param('sii', $service_name, $sector_id, $service_id);
    } else {
        // Add new service
        $stmt = $conn->prepare("INSERT INTO service (service_name, sector_id) VALUES (?, ?)");
        $stmt->bind_param('si', $service_name, $sector_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Service successfully saved!']);
    } else {
        error_log("Database error: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
} else {
    error_log("Invalid input data: " . json_encode($data));
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
}
?>
