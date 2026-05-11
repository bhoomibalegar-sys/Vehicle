<?php
header('Content-Type: application/json');

$conn = new mysqli("127.0.0.1", "root", "", "vehicle_management");

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$result = $conn->query("SELECT * FROM vehicles");

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
