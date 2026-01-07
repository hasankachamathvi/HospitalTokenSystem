// ------------------- Popup & Section Controls -------------------
function openPopup(id) { document.getElementById(id).style.display = "block"; }
function closePopup(id) { document.getElementById(id).style.display = "none"; }

function showSection(id) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById(id).style.display = 'block';
}

// ------------------- Linked List Queue Operations -------------------
let servedCount = 0;

function updateDashboard() {
    document.getElementById('totalPatients').innerText = queue.traverse().length + servedCount;
    document.getElementById('waitingPatients').innerText = queue.traverse().length;
    document.getElementById('servedPatients').innerText = servedCount;
    updateTables();
}

function updateTables() {
    const adminTable = document.getElementById("adminQueueTable");
    const liveTable = document.getElementById("adminLiveQueueTable");
    adminTable.innerHTML = "";
    liveTable.innerHTML = "";
    queue.traverse().forEach((p,i)=>{
        // Admin Table
        let tr = document.createElement('tr');
        tr.innerHTML = `<td>${i+1}</td><td>${p}</td><td><button class="danger" onclick="deleteAt(${i})">Remove</button></td>`;
        adminTable.appendChild(tr);
        // Live Table
        let trLive = document.createElement('tr');
        trLive.innerHTML = `<td>${i+1}</td><td>${p}</td>`;
        liveTable.appendChild(trLive);
    });
}

function addNormal() {
    const name = document.getElementById("adminName").value;
    if (!name) return alert("Enter name");
    queue.insertAtEnd(name);
    updateDashboard();
    closePopup('addPatientPopup');
}

function addEmergency() {
    const name = document.getElementById("adminName").value;
    if (!name) return alert("Enter name");
    queue.insertAtBeginning(name);
    updateDashboard();
    closePopup('addPatientPopup');
}

function addVIP() {
    const name = document.getElementById("adminName").value;
    const pos = parseInt(document.getElementById("position").value);
    if (!name || isNaN(pos)) return alert("Enter name and position");
    queue.insertAtPosition(name, pos);
    updateDashboard();
    closePopup('addPatientPopup');
}

function servePatient() {
    queue.deleteFromBeginning();
    servedCount++;
    updateDashboard();
    closePopup('servePopup');
}

function cancelToken() {
    const pos = parseInt(document.getElementById("removePos").value);
    if (isNaN(pos)) return alert("Enter position");
    queue.deleteFromPosition(pos);
    updateDashboard();
    closePopup('cancelPopup');
}

function deleteAt(i) { queue.deleteFromPosition(i); updateDashboard(); }



function bookToken() {
    const name = document.getElementById("patientName").value;
    if (!name) return alert("Enter your name");
    queue.insertAtEnd(name);
    displayQueue("queueList");
    closePopup('bookPopup');
}

// Display queue
function displayQueue(listId) {
    const list = document.getElementById(listId);
    list.innerHTML = "";
    queue.traverse().forEach((p, i) => {
        const row = `<tr><td>${i+1}</td><td>${p}</td></tr>`;
        list.innerHTML += row;
    });
}
