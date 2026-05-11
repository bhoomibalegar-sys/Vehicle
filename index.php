<!DOCTYPE html>
<html>
<head>
<title>Vehicle Compliance Dashboard</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: #f4f6f9;
}

header {
    background: #1e3a8a;
    color: white;
    padding: 15px;
    text-align: center;
    font-size: 22px;
}

.container {
    padding: 20px;
}

.card {
    background: white;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 5px;
}

input {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    padding: 8px 12px;
    border: none;
    cursor: pointer;
    color: white;
    border-radius: 5px;
}

.add { background: #2563eb; }
.update { background: orange; }
.delete { background: red; }
.download { background: green; }

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #2563eb;
    color: white;
    padding: 10px;
}

td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.ok { color: green; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
.expired { color: red; font-weight: bold; }

</style>
</head>

<body>

<header>🚗 Vehicle Compliance Dashboard</header>

<div class="container">

<!-- DOWNLOAD BUTTON -->
<div class="card" style="text-align:right;">
    <button class="download" onclick="downloadCSV()">⬇ Download CSV</button>
</div>

<!-- FORM -->
<div class="card">

<h3>Add / Update Vehicle</h3>

<input type="hidden" id="edit_id">

<div class="form-grid">

<div class="form-group">
<label>Vehicle Number</label>
<input id="vehicle">
</div>

<div class="form-group">
<label>FC</label>
<input type="date" id="fc">
</div>

<div class="form-group">
<label>Insurance</label>
<input type="date" id="insurance">
</div>

<div class="form-group">
<label>Emission</label>
<input type="date" id="emission">
</div>

<div class="form-group">
<label>AP</label>
<input type="date" id="ap">
</div>

<div class="form-group">
<label>TN</label>
<input type="date" id="tn">
</div>

<div class="form-group">
<label>KL</label>
<input type="date" id="kl">
</div>

</div>

<br>

<button class="add" onclick="addVehicle()">Add</button>
<button class="update" onclick="updateVehicle()">Update</button>

</div>

<!-- TABLE -->
<div class="card">

<table>
<thead>
<tr>
<th>#</th>
<th>Vehicle</th>
<th>FC</th>
<th>Insurance</th>
<th>Emission</th>
<th>AP</th>
<th>TN</th>
<th>KL</th>
<th>Compliance %</th>
<th>Action</th>
</tr>
</thead>

<tbody id="tableData">
<tr><td colspan="10">Loading...</td></tr>
</tbody>

</table>

</div>

</div>

<script>

// STATUS
function getStatus(date) {
    const today = new Date();
    const d = new Date(date);
    const diff = (d - today) / (1000 * 60 * 60 * 24);

    if (diff < 0) return "expired";
    if (diff < 30) return "warning";
    return "ok";
}

// PERCENTAGE
function calculatePercentage(row) {
    let total = 6, valid = 0;

    if (getStatus(row.fc_date) === "ok") valid++;
    if (getStatus(row.insurance_date) === "ok") valid++;
    if (getStatus(row.emission_date) === "ok") valid++;
    if (getStatus(row.ap_tp) === "ok") valid++;
    if (getStatus(row.tn_tp) === "ok") valid++;
    if (getStatus(row.kl_tp) === "ok") valid++;

    return Math.round((valid / total) * 100);
}

// LOAD DATA
function loadData() {
    fetch("fetch.php")
    .then(res => res.json())
    .then(data => {

        let output = "";

        data.forEach((row, i) => {
            output += `
            <tr>
                <td>${i+1}</td>
                <td>${row.vehicle_number}</td>

                <td class="${getStatus(row.fc_date)}">${row.fc_date}</td>
                <td class="${getStatus(row.insurance_date)}">${row.insurance_date}</td>
                <td class="${getStatus(row.emission_date)}">${row.emission_date}</td>
                <td class="${getStatus(row.ap_tp)}">${row.ap_tp}</td>
                <td class="${getStatus(row.tn_tp)}">${row.tn_tp}</td>
                <td class="${getStatus(row.kl_tp)}">${row.kl_tp}</td>

                <td>${calculatePercentage(row)}%</td>

                <td>
                    <button onclick='editVehicle(${JSON.stringify(row)})'>Edit</button>
                    <button class="delete" onclick="deleteVehicle(${row.id})">Delete</button>
                </td>
            </tr>`;
        });

        document.getElementById("tableData").innerHTML = output;
    });
}

// ADD
function addVehicle() {

    if (!vehicle.value.trim()) {
        alert("Vehicle number is required");
        return;
    }

    if (!fc.value || !insurance.value || !emission.value) {
        alert("All date fields are required");
        return;
    }

    let today = new Date();

    if (new Date(fc.value) < today) {
        alert("FC date cannot be in the past");
        return;
    }

    let data = {
        vehicle_number: vehicle.value,
        fc: fc.value,
        insurance: insurance.value,
        emission: emission.value,
        ap: ap.value,
        tn: tn.value,
        kl: kl.value
    };

    fetch("add.php", {
        method: "POST",
        body: JSON.stringify(data)
    })
    .then(() => location.reload());
}

// EDIT
function editVehicle(row) {
    edit_id.value = row.id;
    vehicle.value = row.vehicle_number;
    fc.value = row.fc_date;
    insurance.value = row.insurance_date;
    emission.value = row.emission_date;
    ap.value = row.ap_tp;
    tn.value = row.tn_tp;
    kl.value = row.kl_tp;

    window.scrollTo(0,0);
}

// UPDATE
function updateVehicle() {

    if (!edit_id.value) {
        alert("Select record first");
        return;
    }

    if (!vehicle.value.trim()) {
        alert("Vehicle number is required");
        return;
    }

    let data = {
        id: edit_id.value,
        vehicle_number: vehicle.value,
        fc: fc.value,
        insurance: insurance.value,
        emission: emission.value,
        ap: ap.value,
        tn: tn.value,
        kl: kl.value
    };

    fetch("update.php", {
        method: "POST",
        body: JSON.stringify(data)
    })
    .then(() => location.reload());
}

// DELETE
function deleteVehicle(id) {
    fetch("delete.php?id=" + id)
    .then(() => location.reload());
}

// DOWNLOAD
function downloadCSV() {
    window.open("download.php", "_blank");
}

// INIT
loadData();

</script>

</body>
</html>
