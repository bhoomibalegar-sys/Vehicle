function fetchData(){
    fetch("fetch.php")
    .then(res => res.json())
    .then(data => {
        let output = "";
        data.forEach((row,i)=>{
            output += `
            <tr>
            <td>${i+1}</td>
            <td>${row.vehicle_number}</td>
            <td>${row.fc_date}</td>
            <td>${row.insurance_date}</td>
            <td>${row.emission_date}</td>
            <td>${row.ap_tp}</td>
            <td>${row.tn_tp}</td>
            <td>${row.kl_tp}</td>
            <td>
            <button onclick="deleteData(${row.id})">Delete</button>
            </td>
            </tr>`;
        });
        document.getElementById("table").innerHTML = output;
    });
}

function addVehicle(){
    let data = {
        vehicle_number: vehicle.value,
        fc: fc.value,
        insurance: insurance.value,
        emission: emission.value,
        ap: ap.value,
        tn: tn.value,
        kl: kl.value
    };

    fetch("add.php",{
        method:"POST",
        body: JSON.stringify(data)
    }).then(()=>fetchData());
}

function deleteData(id){
    fetch("delete.php?id="+id)
    .then(()=>fetchData());
}

function downloadCSV(){
    window.location.href = "download.php";
}

fetchData();
