// linkedlist.js
class Node {
    constructor(patient) { // patient = {name, email, phone, date}
        this.data = patient;
        this.next = null;
    }
}

class LinkedList {
    constructor() {
        this.head = null;
    }

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

    insertAtBeginning(patient) {
        const newNode = new Node(patient);
        newNode.next = this.head;
        this.head = newNode;
    }

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

    deleteFromBeginning() {
        if (this.head) this.head = this.head.next;
    }

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

    traverse() {
        const result = [];
        let temp = this.head;
        while (temp) {
            result.push(temp.data);
            temp = temp.next;
        }
        return result;
    }
}

// Global queue
const queue = new LinkedList();
