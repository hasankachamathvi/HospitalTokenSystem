# Hospital Token System - Code Explanation

## Project Overview
This is a **Hospital Queue Management System** that uses a **LinkedList data structure** to manage patient appointments with priority-based queueing.

## System Architecture

### Technology Stack
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL/MariaDB
- **Data Structure**: Singly Linked List

---

## LinkedList Implementation (linkedlist.js)

### 1. Node Class
```javascript
class Node {
    constructor(patient) {
        this.data = patient;  // Stores patient information
        this.next = null;     // Pointer to next node
    }
}
```
**Purpose**: Represents a single node in the linked list, containing patient data and a reference to the next node.

### 2. LinkedList Class
```javascript
class LinkedList {
    constructor() {
        this.head = null;  // Points to first node in list
    }
}
```
**Purpose**: Manages the entire linked list of patients.

### 3. LinkedList Methods

#### insertAtEnd(patient)
```javascript
insertAtEnd(patient) {
    const newNode = new Node(patient);
    if (!this.head) {
        this.head = newNode;
        return;
    }
    let temp = this.head;
    while (temp.next) temp = temp.next;
    temp.next = newNode;
}
```
**Purpose**: Adds a patient to the end of the queue
**Time Complexity**: O(n)
**Used For**: Normal priority patients

#### insertAtBeginning(patient)
```javascript
insertAtBeginning(patient) {
    const newNode = new Node(patient);
    newNode.next = this.head;
    this.head = newNode;
}
```
**Purpose**: Adds a patient to the front of the queue
**Time Complexity**: O(1)
**Used For**: Emergency patients (highest priority)

#### insertAtPosition(patient, position)
```javascript
insertAtPosition(patient, position) {
    if (position === 0) {
        this.insertAtBeginning(patient);
        return;
    }
    const newNode = new Node(patient);
    let temp = this.head;
    for (let i = 0; i < position - 1 && temp; i++) {
        temp = temp.next;
    }
    if (temp) {
        newNode.next = temp.next;
        temp.next = newNode;
    }
}
```
**Purpose**: Inserts a patient at a specific position in the queue
**Time Complexity**: O(n)
**Used For**: VIP patients (inserted after emergency but before normal)

#### deleteFromBeginning()
```javascript
deleteFromBeginning() {
    if (this.head) this.head = this.head.next;
}
```
**Purpose**: Removes the first patient from the queue (FIFO - First In, First Out)
**Time Complexity**: O(1)
**Used For**: Serving the next patient in line

#### deleteFromPosition(position)
```javascript
deleteFromPosition(position) {
    if (position === 0) {
        this.deleteFromBeginning();
        return;
    }
    let temp = this.head;
    for (let i = 0; i < position - 1 && temp; i++) {
        temp = temp.next;
    }
    if (temp && temp.next) temp.next = temp.next.next;
}
```
**Purpose**: Removes a patient from a specific position
**Time Complexity**: O(n)
**Used For**: Deleting a specific patient from queue

#### traverse()
```javascript
traverse() {
    const result = [];
    let temp = this.head;
    while (temp) {
        result.push(temp.data);
        temp = temp.next;
    }
    return result;
}
```
**Purpose**: Returns an array of all patients in the queue
**Time Complexity**: O(n)
**Used For**: Displaying the queue

---

## Admin Page (admin.html)

### LinkedList Integration

#### 1. Initialization
```javascript
const patientQueue = new LinkedList();
```
Creates a global LinkedList instance to manage the queue.

#### 2. Adding Patients (Priority-Based)
```javascript
if(patient.priority === 'emergency') {
    patientQueue.insertAtBeginning(patient);  // Front of queue
} else if(patient.priority === 'vip') {
    // Insert after emergency but before normal
    const patients = patientQueue.traverse();
    let insertPos = patients.findIndex(p => p.priority !== 'emergency');
    if(insertPos === -1) insertPos = patients.length;
    patientQueue.insertAtPosition(patient, insertPos);
} else {
    patientQueue.insertAtEnd(patient);  // End of queue
}
```

**Priority Order**:
1. **Emergency** → Front of queue (insertAtBeginning)
2. **VIP** → After emergency (insertAtPosition)
3. **Normal** → End of queue (insertAtEnd)

#### 3. Serving Patients
```javascript
function servePatient(patientId) {
    patientQueue.deleteFromBeginning();  // Remove first patient
    // Update database via serve_patient.php
}
```
**How it works**: Uses FIFO principle - the first patient in queue is served first.

#### 4. Deleting Patients
```javascript
function deletePatient(patientId) {
    const patients = patientQueue.traverse();
    const position = patients.findIndex(p => p.id === patientId);
    patientQueue.deleteFromPosition(position);  // Remove from specific position
    // Update database via delete_patient.php
}
```

#### 5. Displaying Queue
```javascript
function updateAdminTables() {
    // Rebuild LinkedList from database
    patientQueue.head = null;
    patients.forEach(p => {
        patientQueue.insertAtEnd(p);
    });
    
    // Get all patients using traverse
    const queuePatients = patientQueue.traverse();
    // Display in table
}
```

---

## Booking Page (booking.html)

### LinkedList Integration

#### 1. Initialization
```javascript
const patientQueue = new LinkedList();
```

#### 2. Booking Appointment
```javascript
function bookPatient() {
    // ... validation and form data ...
    
    fetch('add_patient.php', {
        method: 'POST',
        body: formData
    })
    .then(data => {
        if(data.status === 'success') {
            // Add to LinkedList (all bookings are normal priority)
            patientQueue.insertAtEnd(patient);
            console.log('Current LinkedList queue:', patientQueue.traverse());
        }
    });
}
```
**Note**: All public bookings are added with normal priority (end of queue).

#### 3. Displaying Queue
```javascript
function displayQueue() {
    fetch('get_queue.php')
    .then(patients => {
        // Rebuild LinkedList
        patientQueue.head = null;
        patients.forEach(p => {
            patientQueue.insertAtEnd(p);
        });
        
        // Display using traverse
        const queuePatients = patientQueue.traverse();
        // Render in table
    });
}
```

---

## PHP Backend Files

### add_patient.php
- Inserts patient into MySQL database
- Sets default priority as 'normal'
- Returns JSON response

### add_patient_position.php
- Inserts patient with custom priority
- Used by admin to set emergency/VIP priority
- Calculates queue position based on priority

### get_queue.php
- Fetches all waiting patients from database
- Orders by priority and queue_position
- Returns JSON array

### serve_patient.php
- Updates patient status to 'served'
- Records serve time

### delete_patient.php
- Removes patient from database
- Used for cancellations

### db_config.php
- Database connection configuration
- MySQL credentials

---

## Database Schema

### patients Table
```sql
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    date DATETIME NOT NULL,
    priority VARCHAR(20) DEFAULT 'normal',    -- emergency, vip, normal
    status VARCHAR(20) DEFAULT 'waiting',      -- waiting, served
    queue_position INT DEFAULT 999,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### admins Table
```sql
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

---

## How Priority Queueing Works

### Example Queue Order:

**Initial State**: Empty queue

**Action 1**: Add Normal patient "John"
```
Queue: [John(normal)]
LinkedList: insertAtEnd()
```

**Action 2**: Add Normal patient "Alice"
```
Queue: [John(normal), Alice(normal)]
LinkedList: insertAtEnd()
```

**Action 3**: Add Emergency patient "Bob"
```
Queue: [Bob(emergency), John(normal), Alice(normal)]
LinkedList: insertAtBeginning()
```

**Action 4**: Add VIP patient "Carol"
```
Queue: [Bob(emergency), Carol(vip), John(normal), Alice(normal)]
LinkedList: insertAtPosition(1)
```

**Action 5**: Serve next patient
```
Queue: [Carol(vip), John(normal), Alice(normal)]
LinkedList: deleteFromBeginning()
Result: Bob is served
```

---

## Key Features

### 1. Real-time Updates
- Queue refreshes every 2 seconds
- Both admin and booking pages show live queue

### 2. Priority Management
- **Emergency**: Immediate attention (front of queue)
- **VIP**: Priority service (after emergency)
- **Normal**: Standard queue (end of queue)

### 3. Admin Controls
- Add patients with any priority
- Serve patients (FIFO)
- Delete patients from queue
- View statistics (total, waiting, served)

### 4. Session Management
- Admin login required for admin.html
- PHP sessions for authentication
- Automatic redirect if not logged in

---

## Console Logging

The system logs LinkedList operations to browser console for debugging:

```
Using LinkedList: Adding EMERGENCY patient (insertAtBeginning operation)
Current LinkedList queue: [Array of patients]
Using LinkedList: Serving patient (deleteFromBeginning operation)
Patient removed from LinkedList. Remaining queue: [Array]
```

**To view**: Press F12 in browser → Console tab

---

## File Structure

```
web/
├── index.html              # Homepage
├── booking.html            # Patient booking page (uses LinkedList)
├── admin.html              # Admin dashboard (uses LinkedList)
├── login.html              # Admin login
├── linkedlist.js           # LinkedList implementation
├── webstyle.css           # Styles
├── style.css              # Additional styles
├── db_config.php          # Database config
├── add_patient.php        # Add normal patient
├── add_patient_position.php # Add with priority
├── get_queue.php          # Fetch queue
├── serve_patient.php      # Mark as served
├── delete_patient.php     # Remove patient
├── login.php              # Authentication
├── logout.php             # End session
├── check_session.php      # Verify login
└── database.sql           # Database schema
```

---

## Running the Application

### Prerequisites
- WAMP/XAMPP server
- MySQL database
- Modern web browser

### Setup Steps
1. Start Apache and MySQL servers
2. Import database.sql into MySQL
3. Configure db_config.php with database credentials
4. Access http://localhost/hospital/index.html

### Default Admin Login
- **Username**: admin
- **Password**: admin123

---

## Data Structure Advantages

### Why LinkedList?

1. **Dynamic Size**: Can grow/shrink as patients are added/removed
2. **Efficient Insertion**: O(1) for emergency patients at front
3. **FIFO Queue**: Natural implementation of queue operations
4. **Priority Management**: Easy to insert at specific positions
5. **No Memory Waste**: Only allocates memory when needed

### Time Complexities
- Insert at beginning: O(1)
- Insert at end: O(n)
- Insert at position: O(n)
- Delete from beginning: O(1)
- Delete from position: O(n)
- Traverse: O(n)

---

## Future Enhancements

1. Use doubly linked list for faster deletions
2. Implement circular linked list for round-robin scheduling
3. Add queue time estimates
4. SMS/Email notifications
5. Multiple department queues
6. Appointment history for patients

---

## Testing the LinkedList

### Browser Console Tests:

```javascript
// Create new LinkedList
const queue = new LinkedList();

// Add patients
queue.insertAtEnd({name: "John", priority: "normal"});
queue.insertAtBeginning({name: "Emergency", priority: "emergency"});
queue.insertAtPosition({name: "VIP", priority: "vip"}, 1);

// View queue
console.log(queue.traverse());

// Serve patient
queue.deleteFromBeginning();
console.log(queue.traverse());
```

---

## Contact & Support

For issues or questions about the code implementation, check:
- Browser console for LinkedList operation logs
- PHP error logs for backend issues
- MySQL logs for database problems

---

**End of Code Explanation**
