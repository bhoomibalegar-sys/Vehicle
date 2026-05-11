<?php
$conn = new mysqli("127.0.0.1", "root", "", "vehicle_management");

$id = $_GET['id'];

$conn->query("DELETE FROM vehicles WHERE id=$id");
?>
