const form = document.getElementById('bookingForm');
const messageDiv = document.getElementById('message');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = {
        name: form.name.value,
        email: form.email.value,
        phone: form.phone.value,
        department: form.department.value,
        doctor: form.doctor.value,
        date: form.date.value,
        time: form.time.value
    };

    const res = await fetch('http://localhost:3000/api/bookings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });

    const result = await res.text();
    messageDiv.innerText = result;
    form.reset();
});
