function addNormal() {
    const name = document.getElementById("adminName").value;
    if (!name) return alert("Enter patient name");
    queue.insertAtEnd(name);
    displayQueue("adminQueue");
}

function addEmergency() {
    const name = document.getElementById("adminName").value;
    if (!name) return alert("Enter patient name");
    queue.insertAtBeginning(name);
    displayQueue("adminQueue");
}

function addVIP() {
    const name = document.getElementById("adminName").value;
    const pos = parseInt(document.getElementById("position").value);

    if (!name || isNaN(pos)) {
        alert("Enter patient name and position");
        return;
    }

    queue.insertAtPosition(name, pos);
    displayQueue("adminQueue");
}

function servePatient() {
    queue.deleteFromBeginning();
    displayQueue("adminQueue");
}

function cancelToken() {
    const pos = parseInt(document.getElementById("removePos").value);
    if (isNaN(pos)) return alert("Enter position to cancel");
    queue.deleteFromPosition(pos);
    displayQueue("adminQueue");
}

function displayQueue(listId) {
    const list = document.getElementById(listId);
    list.innerHTML = "";

    queue.traverse().forEach((p, index) => {
        const li = document.createElement("li");
        li.textContent = `Token ${index + 1} : ${p}`;
        list.appendChild(li);
    });
}
