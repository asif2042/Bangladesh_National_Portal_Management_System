<?php
include 'config.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $query = $query . '%';

    $sql = "SELECT service_name FROM service WHERE service_name LIKE ? ORDER BY service_name ASC LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $query);
    $stmt->execute();
    $result = $stmt->get_result();

    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row['service_name'];
    }

    echo json_encode($services);
    exit;
}
?>
