<?php

$conn = new mysqli("127.0.0.1", "root", "", "vehicle_management");

$uploadDir = "uploads/";

// FUNCTION TO SAVE FILE
function saveFile($file, $uploadDir) {
    if ($file['name'] == "") return "";

    $filename = time() . "_" . basename($file['name']);
    $target = $uploadDir . $filename;

    move_uploaded_file($file['tmp_name'], $target);

    return $filename;
}

// SAVE FILES
$fc_file = saveFile($_FILES['fc_file'], $uploadDir);
$insurance_file = saveFile($_FILES['insurance_file'], $uploadDir);
$emission_file = saveFile($_FILES['emission_file'], $uploadDir);
$ap_file = saveFile($_FILES['ap_file'], $uploadDir);
$tn_file = saveFile($_FILES['tn_file'], $uploadDir);
$kl_file = saveFile($_FILES['kl_file'], $uploadDir);

// INSERT DATA
$conn->query("INSERT INTO vehicles 
(vehicle_number, fc_date, insurance_date, emission_date, ap_tp, tn_tp, kl_tp,
fc_file, insurance_file, emission_file, ap_file, tn_file, kl_file)
VALUES 
('{$_POST['vehicle_number']}','{$_POST['fc']}','{$_POST['insurance']}','{$_POST['emission']}',
'{$_POST['ap']}','{$_POST['tn']}','{$_POST['kl']}',
'$fc_file','$insurance_file','$emission_file','$ap_file','$tn_file','$kl_file')");
?>
