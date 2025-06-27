async function createEvent(eventData) {
    try {
        const errorDiv = document.querySelector('.alert.error');
        if (!errorDiv) {
            console.error('Error div not found!');
            return;
        }
        errorDiv.style.display = 'none';
        
        const response = await fetch('/Web-Project/controllers/createEventController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(eventData)
        });

        const data = await response.json();
        console.log('Server response:', data);

        if (!response.ok) {
            errorDiv.textContent = data.message || 'A aparut o eroare';
            errorDiv.style.display = 'block';
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            console.log('error:', errorDiv.textContent);
            return;
        }

        if (data.status === 'success') {
            window.location.href = '../views/evenimente.php';
        }
    } catch (error) {
        console.error('Caught error:', error);
        const errorDiv = document.querySelector('.alert.error');
        if (errorDiv) {
            errorDiv.textContent = 'A apărut o eroare la server';
            errorDiv.style.display = 'block';
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}

document.getElementById('eventForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        event_name: document.getElementById('event_name').value,
        location: document.getElementById('location').value,
        description: document.getElementById('description').value,
        event_date: document.getElementById('event_date').value,
        location_lat: parseFloat(document.getElementById('location_lat').value),
        location_lon: parseFloat(document.getElementById('location_lon').value),
        max_participants: parseInt(document.getElementById('max_events').value),
        duration: parseInt(document.getElementById('duration').value),
        min_events_participated: parseInt(document.getElementById('min_events_participated').value),
        created_by: document.getElementById('user_id').value
    };

    await createEvent(formData);
});
