CREATE DATABASE vehicle_management;

USE vehicle_management;

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number VARCHAR(20),
    fc_date DATE,
    insurance_date DATE,
    emission_date DATE,
    ap_tp DATE,
    tn_tp DATE,
    kl_tp DATE
);
