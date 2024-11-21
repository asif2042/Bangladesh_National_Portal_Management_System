<?php
include 'config.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['sector_name'])) {
    $sector_name = $data['sector_name'];

    $stmt = $conn->prepare("SELECT sector_id FROM service_sector WHERE sector_name = ?");
    $stmt->bind_param('s', $sector_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'sector_id' => $row['sector_id']]);
    } else {
        error_log("Sector name not found: $sector_name");
        echo json_encode(['success' => false, 'message' => 'Sector not found']);
    }
} else {
    error_log("Invalid input to get_sector_id.php: " . json_encode($data));
    echo json_encode(['success' => false, 'message' => 'Invalid sector name']);
}
?>
