<?php
// search_service.php
include 'config.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT name FROM services WHERE name LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row['name'];
    }
    echo json_encode($services);
}
?>
