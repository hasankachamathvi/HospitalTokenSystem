// ------------------- Admin Popup -------------------
function openAddPatient() {
    document.getElementById("addPatientPopup").style.display = "flex";
}
function closeAddPatient() {
    document.getElementById("addPatientPopup").style.display = "none";
}

// ------------------- Add Patient (Admin) -------------------
function addPatientFromAdmin(){
    const patient = {
        name: document.getElementById("adminPatientName").value.trim(),
        email: document.getElementById("adminPatientEmail").value.trim(),
        phone: document.getElementById("adminPatientPhone").value.trim(),
        date: document.getElementById("adminPatientDate").value
    };
    if(!patient.name || !patient.email || !patient.phone || !patient.date) return alert("Fill all fields");

    fetch('add_patient.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`name=${encodeURIComponent(patient.name)}&email=${encodeURIComponent(patient.email)}&phone=${encodeURIComponent(patient.phone)}&date=${encodeURIComponent(patient.date)}`
    })
        .then(res=>res.json())
        .then(data=>{
            if(data.status==='success'){
                closeAddPatient();
                fetchQueueFromDB();
            }else{
                alert(data.message);
            }
        });
}

// ------------------- Booking from Website -------------------
function bookPatient() {
    const patient = {
        name: document.getElementById("patientName").value.trim(),
        email: document.getElementById("patientEmail").value.trim(),
        phone: document.getElementById("patientPhone").value.trim(),
        date: document.getElementById("appointmentDate").value
    };
    if(!patient.name || !patient.email || !patient.phone || !patient.date) return alert("Fill all fields");

    fetch('add_patient.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`name=${encodeURIComponent(patient.name)}&email=${encodeURIComponent(patient.email)}&phone=${encodeURIComponent(patient.phone)}&date=${encodeURIComponent(patient.date)}`
    })
        .then(res=>res.json())
        .then(data=>{
            if(data.status==='success'){
                alert("Appointment booked!");
                document.getElementById("patientName").value = "";
                document.getElementById("patientEmail").value = "";
                document.getElementById("patientPhone").value = "";
                document.getElementById("appointmentDate").value = "";
                fetchQueueFromDB();
            }else{
                alert(data.message);
            }
        });
}

// ------------------- Fetch Queue from DB -------------------
function fetchQueueFromDB(){
    fetch('get_queue.php')
        .then(res=>res.json())
        .then(data=>{
            const adminTable = document.getElementById("adminQueueTable");
            const liveTable = document.getElementById("adminLiveQueueTable");
            const queueList = document.getElementById("queueList"); // for booking page
            if(adminTable) adminTable.innerHTML = "";
            if(liveTable) liveTable.innerHTML = "";
            if(queueList) queueList.innerHTML = "";

            data.forEach((p,i)=>{
                if(adminTable){
                    let tr = adminTable.insertRow();
                    tr.innerHTML = `<td>${i+1}</td>
                                <td>${p.name}</td>
                                <td>${p.email}</td>
                                <td>${p.phone}</td>
                                <td>${p.date}</td>
                                <td><button onclick="servePatientDB(${p.id})">Serve</button></td>`;
                }
                if(liveTable){
                    let trLive = liveTable.insertRow();
                    trLive.innerHTML = `<td>${i+1}</td>
                                    <td>${p.name}</td>
                                    <td>${p.email}</td>
                                    <td>${p.phone}</td>
                                    <td>${p.date}</td>`;
                }
                if(queueList){
                    let trList = queueList.insertRow();
                    trList.innerHTML = `<td>${i+1}</td>
                                    <td>${p.name}</td>
                                    <td>${p.email}</td>
                                    <td>${p.phone}</td>
                                    <td>${p.date}</td>`;
                }
            });

            // Update overview cards if exist
            const total = data.length;
            const totalElem = document.getElementById('totalPatients');
            const waitingElem = document.getElementById('waitingPatients');
            if(totalElem) totalElem.innerText = total;
            if(waitingElem) waitingElem.innerText = total;
        });
}

// ------------------- Serve Patient -------------------
function servePatientDB(id){
    fetch('serve_patient.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`id=${id}`
    }).then(()=>fetchQueueFromDB());
}

// ------------------- Initial Fetch -------------------
fetchQueueFromDB();
setInterval(fetchQueueFromDB, 2000); // live update every 2 sec
