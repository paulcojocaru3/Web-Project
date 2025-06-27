
async function getEvents(lat = null, lon = null) {
    try {
        // Apelează serviciul REST pentru evenimente
        const response = await fetch('http://localhost/services/controllers/eventsController.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (response.success) {
            return response.data;
        } else {
            throw new Error(response.data?.message || 'Failed to fetch events');
        }
    } catch (error) {
        console.error('Error fetching events:', error);
        return { status: 'error', message: error.message };
    }
}

async function checkForEvents(lat, lon) {
    try {
        const response = await fetch(`http://localhost/services/controllers/eventsController.php?action=check&lat=${lat}&lon=${lon}`, {
            method: 'GET'
        });
        
        if (response.success && response.data.status === 'success') {
            return response.data.data?.marker || 'gray';
        }
        return 'gray';
    } catch (error) {
        console.error("Error checking for events:", error);
        return 'gray';
    }
}

async function joinEvent(eventId) {
    try {
        const response = await restClient.makeRequest('http://localhost/services/controllers/eventsController.php', {
            method: 'POST',
            body: JSON.stringify({
                action: 'join',
                event_id: eventId,
                user_id: window.userId
            })
        });
        
        if (response.success && response.data.status === 'success') {
            alert('Te-ai înscris cu succes la eveniment!');
            location.reload();
        } else {
            alert('Eroare: ' + (response.data?.message || 'Nu te-ai putut înscrie'));
        }
    } catch (error) {
        console.error('Error joining event:', error);
        alert('A apărut o eroare la înscriere!');
    }
}

async function deleteEvent(eventId) {
    if (!confirm('Ești sigur că vrei să ștergi acest eveniment?')) {
        return;
    }
    
    try {
        const response = await restClient.makeRequest('http://localhost/services/controllers/eventsController.php', {
            method: 'DELETE',
            body: JSON.stringify({
                event_id: eventId
            })
        });
        
        if (response.success) {
            alert('Eveniment șters cu succes!');
            location.reload();
        } else {
            alert('Eroare la ștergerea evenimentului!');
        }
    } catch (error) {
        console.error('Error deleting event:', error);
        alert('A apărut o eroare la ștergere!');
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    try {
        window.isAdmin = await checkAdminStatus();
        
        const urlParams = new URLSearchParams(window.location.search);
        const lat = urlParams.get('lat');
        const lon = urlParams.get('lon');
        
        const response = await getEvents(lat, lon);
        showEvents(response);
        initializePastEventsToggle();
    } catch (error) {
        console.error('Error loading events:', error);
    }
});

function createEventCard(event) {
    const template = document.getElementById('eventCardTemplate');
    const card = template.content.cloneNode(true).querySelector('.event-card');
    
    if (event.is_past) card.classList.add('past-event');
    if (event.is_full) card.querySelector('.event-full-badge').style.display = 'block';

    card.querySelector('.event-type-badge').textContent = event.event_type || 'Sport';
    card.querySelector('.event-title').textContent = event.event_name;
    
    card.querySelector('.date-value').textContent = new Date(event.event_date).toLocaleString();
    card.querySelector('.location-value').textContent = decodeURIComponent(event.location);
    
    const participantsText = event.max_participants > 0 ? 
        `${event.current_participants} / ${event.max_participants}` : 
        `${event.current_participants} / nelimitat`;
    card.querySelector('.participants-value').textContent = participantsText;
    
    if (event.min_events_participated > 0) {
        const requirementsContainer = card.querySelector('.requirements-container');
        requirementsContainer.style.display = 'block';
        card.querySelector('.requirements-value').textContent = 
            `Minim ${event.min_events_participated} participări`;
    }   
    
    const actionButtons = card.querySelector('.action-buttons');
    actionButtons.innerHTML = createActionButton(event);
    
    const detailsLink = card.querySelector('.btn-details');
    detailsLink.href = `view_event.php?id=${event.event_id}`;
    
    return card;
}

async function checkAdminStatus() {
    try {
        const response = await fetch('../controllers/checkAdminStatus.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data.isAdmin === true;
    } catch (error) {
        console.error('Error checking admin status:', error);
        return false;
    }
}

function createActionButton(event) {
    if (event.is_registered) {
        return `<button class="btn-registered" disabled>${event.is_past ? 'Ați participat' : 'Inscris'}</button>`;
    }
    if (event.is_past) {
        return '<button class="btn-past" disabled>Eveniment trecut</button>';
    }
    if (event.is_running) {
        return '<button class="btn-past" disabled>În desfășurare</button>';
    }
     console.log(window.isAdmin);
     if (window.isAdmin === true) {
        console.log('Admin user detected, showing delete button for event', event.event_id);
        return `
            <button class="btn-delete" onclick="deleteEvent(${event.event_id})">Șterge eveniment</button>
        `;
    }
    if (event.created_by === window.userId) {
        return '<button class="btn-owner" disabled>Organizator</button>';
    }
    console.log(event.is_running);
    if (event.is_full) {
        return '<button class="btn-full" disabled>Locuri epuizate</button>';
    }
    
    return `
        <form method="POST" action="../controllers/joinEventController.php">
            <input type="hidden" name="event_id" value="${event.event_id}">
            <button type="submit" name="register_event" class="btn-register">Inscrie-te</button>
        </form>
    `;
}

function showEvents(events) {
    const futureEventsList = document.querySelector('#futureEvents .events-list');
    const pastEventsList = document.querySelector('#pastEvents .events-list');

    futureEventsList.innerHTML = '';
    pastEventsList.innerHTML = '';

    if (events.future_events?.length) {
        events.future_events.forEach(event => {
            futureEventsList.appendChild(createEventCard(event));
        });
    } else {
        futureEventsList.innerHTML = '<div class="no-events">Nu există evenimente viitoare</div>';
    }

    if (events.past_events?.length) {
        events.past_events.forEach(event => {
            pastEventsList.appendChild(createEventCard(event));
        });
    } else {
        pastEventsList.innerHTML = '<div class="no-events">Nu există evenimente trecute</div>';
    }
}

function initializePastEventsToggle() {
    const toggleButton = document.getElementById('togglePastEvents');
    const pastEventsSection = document.getElementById('pastEvents');

    if (toggleButton && pastEventsSection) {
        toggleButton.addEventListener('click', () => {
            const isHidden = pastEventsSection.style.display === 'none';
            pastEventsSection.style.display = isHidden ? 'block' : 'none';
            pastEventsSection.classList.toggle('visible');
            toggleButton.textContent = isHidden ? 'Ascunde evenimente trecute' : 'Arată evenimente trecute';
            toggleButton.classList.toggle('active');
        });
    }
}