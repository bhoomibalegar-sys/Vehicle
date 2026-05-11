<?php
$conn = new mysqli("127.0.0.1", "root", "", "vehicle_management");

if ($conn->connect_error) {
    die("Connection failed");
}
?>
