<?php

$conn = new mysqli("127.0.0.1", "root", "", "vehicle_management");

$data = json_decode(file_get_contents("php://input"));

$id = $data->id;

$conn->query("UPDATE vehicles SET 
vehicle_number='$data->vehicle_number',
fc_date='$data->fc',
insurance_date='$data->insurance',
emission_date='$data->emission',
ap_tp='$data->ap',
tn_tp='$data->tn',
kl_tp='$data->kl'
WHERE id=$id");

?>
