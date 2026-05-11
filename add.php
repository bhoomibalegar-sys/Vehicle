<?php
$conn = new mysqli("127.0.0.1", "root", "", "vehicle_management");

$data = json_decode(file_get_contents("php://input"));

$conn->query("INSERT INTO vehicles 
(vehicle_number, fc_date, insurance_date, emission_date, ap_tp, tn_tp, kl_tp)
VALUES 
('$data->vehicle_number','$data->fc','$data->insurance','$data->emission','$data->ap','$data->tn','$data->kl')");
?>
