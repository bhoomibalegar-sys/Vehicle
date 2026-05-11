<?php

// FORCE DOWNLOAD
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="vehicle_data.csv"');

// CONNECT DB
$conn = new mysqli("127.0.0.1", "root", "", "vehicle_management");

if ($conn->connect_error) {
    die("Connection failed");
}

// OPEN OUTPUT STREAM
$output = fopen("php://output", "w");

// COLUMN HEADERS
fputcsv($output, [
    'ID',
    'Vehicle Number',
    'FC Date',
    'Insurance Date',
    'Emission Date',
    'AP',
    'TN',
    'KL'
]);

// FETCH DATA
$result = $conn->query("SELECT * FROM vehicles");

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['vehicle_number'],
        $row['fc_date'],
        $row['insurance_date'],
        $row['emission_date'],
        $row['ap_tp'],
        $row['tn_tp'],
        $row['kl_tp']
    ]);
}

// CLOSE
fclose($output);
exit;
?>
