<!DOCTYPE html>
<html>
<head>
<title>Vehicle Compliance Dashboard</title>

<style>
body { margin:0; font-family:Arial; background:#f4f6f9; }
header { background:#1e3a8a; color:white; padding:15px; text-align:center; font-size:22px; }

.container { padding:20px; }

.card {
    background:white;
    padding:15px;
    margin-bottom:15px;
    border-radius:8px;
    box-shadow:0px 2px 8px rgba(0,0,0,0.1);
}

.form-grid {
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
}

.form-group {
    display:flex;
    flex-direction:column;
}

.form-group label {
    font-weight:bold;
}

input {
    padding:8px;
    border:1px solid #ccc;
    border-radius:5px;
}

button {
    padding:8px 12px;
    border:none;
    cursor:pointer;
    color:white;
    border-radius:5px;
}

.add { background:#2563eb; }
.update { background:orange; }
.delete { background:red; }
.download { background:green; }

table { width:100%; border-collapse:collapse; }

th {
    background:#2563eb;
    color:white;
    padding:10px;
}

td {
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

.ok { color:green; font-weight:bold; }
.warning { color:orange; font-weight:bold; }
.expired { color:red; font-weight:bold; }

.error { border:2px solid red !important; }
.success { border:2px solid green !important; }

.error-text {
    color:red;
    font-size:12px;
}

a {
    color:#2563eb;
    font-weight:bold;
    text-decoration:none;
}
</style>

</head>
<body>

<header>🚗 Vehicle Compliance Dashboard</header>

<div class="container">

<!-- DOWNLOAD -->
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
<input id="vehicle" onkeyup="validateVehicle()">
<span id="vehicle_error" class="error-text"></span>
</div>

<div class="form-group">
<label>FC</label>
<input type="date" id="fc" onchange="validateDate('fc')">
<input type="file" id="fc_file">
<span id="fc_error" class="error-text"></span>
</div>

<div class="form-group">
<label>Insurance</label>
<input type="date" id="insurance" onchange="validateDate('insurance')">
<input type="file" id="insurance_file">
<span id="insurance_error" class="error-text"></span>
</div>

<div class="form-group">
<label>Emission</label>
<input type="date" id="emission" onchange="validateDate('emission')">
<input type="file" id="emission_file">
<span id="emission_error" class="error-text"></span>
</div>

<div class="form-group">
<label>AP</label>
<input type="date" id="ap">
<input type="file" id="ap_file">
</div>

<div class="form-group">
<label>TN</label>
<input type="date" id="tn">
<input type="file" id="tn_file">
</div>

<div class="form-group">
<label>KL</label>
<input type="date" id="kl">
<input type="file" id="kl_file">
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
<th>%</th>
<th>Certificates</th>
<th>Last Updated</th>
<th>Action</th>
</tr>
</thead>

<tbody id="tableData">
<tr><td colspan="12">Loading...</td></tr>
</tbody>

</table>

</div>

</div>

<script>

// STATUS
function getStatus(date){
let today=new Date();
let d=new Date(date);
let diff=(d-today)/(1000*60*60*24);

if(diff<0) return "expired";
if(diff<30) return "warning";
return "ok";
}

// %
function calculatePercentage(row){
let total=6, valid=0;

if(getStatus(row.fc_date)==="ok") valid++;
if(getStatus(row.insurance_date)==="ok") valid++;
if(getStatus(row.emission_date)==="ok") valid++;
if(getStatus(row.ap_tp)==="ok") valid++;
if(getStatus(row.tn_tp)==="ok") valid++;
if(getStatus(row.kl_tp)==="ok") valid++;

return Math.round((valid/total)*100);
}

// FORMAT DATE
function formatDateTime(dt){
let d=new Date(dt);
return d.toLocaleString();
}

// VALIDATION
function setError(id,msg){
document.getElementById(id).classList.add("error");
document.getElementById(id+"_error").innerText=msg;
}

function setSuccess(id){
document.getElementById(id).classList.remove("error");
document.getElementById(id).classList.add("success");
document.getElementById(id+"_error").innerText="";
}

function validateVehicle(){
let v=vehicle.value.trim();
if(v===""){ setError("vehicle","Required"); return false; }
setSuccess("vehicle"); return true;
}

function validateDate(id){
let val=document.getElementById(id).value;
if(!val){ setError(id,"Required"); return false; }
setSuccess(id); return true;
}

function validateForm(){
return validateVehicle() && validateDate("fc") && validateDate("insurance") && validateDate("emission");
}

// LOAD DATA
function loadData(){
fetch("fetch.php")
.then(res=>res.json())
.then(data=>{

let output="";

data.forEach((row,i)=>{

output+=`
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
<a href="uploads/${row.fc_file}" target="_blank">FC</a> |
<a href="uploads/${row.insurance_file}" target="_blank">INS</a> |
<a href="uploads/${row.emission_file}" target="_blank">EMI</a>
</td>

<td>${formatDateTime(row.last_updated)}</td>

<td>
<button onclick='editVehicle(${JSON.stringify(row)})'>Edit</button>
<button class="delete" onclick="deleteVehicle(${row.id})">Delete</button>
</td>
</tr>
`;
});

tableData.innerHTML=output;
});
}

// ADD
function addVehicle(){
if(!validateForm()) return;

let fd=new FormData();

fd.append("vehicle_number",vehicle.value);
fd.append("fc",fc.value);
fd.append("insurance",insurance.value);
fd.append("emission",emission.value);
fd.append("ap",ap.value);
fd.append("tn",tn.value);
fd.append("kl",kl.value);

fd.append("fc_file",fc_file.files[0]);
fd.append("insurance_file",insurance_file.files[0]);
fd.append("emission_file",emission_file.files[0]);

fetch("add.php",{method:"POST",body:fd})
.then(()=>location.reload());
}

// EDIT
function editVehicle(r){
edit_id.value=r.id;
vehicle.value=r.vehicle_number;
fc.value=r.fc_date;
insurance.value=r.insurance_date;
emission.value=r.emission_date;
ap.value=r.ap_tp;
tn.value=r.tn_tp;
kl.value=r.kl_tp;

scrollTo(0,0);
}

// UPDATE
function updateVehicle(){

let fd=new FormData();

fd.append("id",edit_id.value);
fd.append("vehicle_number",vehicle.value);
fd.append("fc",fc.value);
fd.append("insurance",insurance.value);
fd.append("emission",emission.value);
fd.append("ap",ap.value);
fd.append("tn",tn.value);
fd.append("kl",kl.value);

fetch("update.php",{method:"POST",body:fd})
.then(()=>location.reload());
}

// DELETE
function deleteVehicle(id){
fetch("delete.php?id="+id)
.then(()=>location.reload());
}

// DOWNLOAD
function downloadCSV(){
window.open("download.php","_blank");
}

// INIT
loadData();

</script>

</body>
</html>
