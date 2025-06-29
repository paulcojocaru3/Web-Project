// URL-ul pentru API
const API_URL = 'http://localhost/services/controllers';

/**
 * Obtine evenimentele din API
 */
async function getEvents(lat = null, lon = null) {
    try {
        // Construim URL-ul cu parametri
        let url = `${API_URL}/eventsController.php`;
        const params = [];
        
        if (lat && lon) {
            params.push(`lat=${lat}&lon=${lon}`);
        }
        
        if (window.userId) {
            params.push(`user_id=${window.userId}`);
        }
        
        if (params.length > 0) {
            url += `?${params.join('&')}`;
        }
        
        // Facem cererea catre API
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        // Procesam raspunsul
        const data = await response.json();
        
        if (data.status === 'success') {
            return data.data || {
                future_events: data.future_events || [],
                past_events: data.past_events || []
            };
        } else {
            throw new Error(data.message || 'Eroare la preluarea evenimentelor');
        }
    } catch (error) {
        console.error('Eroare:', error);
        return { 
            future_events: [],
            past_events: []
        };
    }
}

/**
 * Verifica daca exista evenimente la o locatie
 */
async function checkForEvents(lat, lon) {
    try {
        const response = await fetch(`${API_URL}/eventsController.php?action=check&lat=${lat}&lon=${lon}`);
        
        if(!response.ok) {
            throw new Error(`Eroare HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        return data.status === 'success' ? data.data?.marker || 'gray' : 'gray';
    } catch (error) {
        console.error("Eroare la verificarea evenimentelor:", error);
        return 'gray'; 
    }
}

/**
 * Inscrie utilizatorul la un eveniment
 */
async function joinEvent(eventId) {
    try { 
        if (!window.userId) {
            alert('Trebuie să fii autentificat pentru a te înscrie la evenimente!');
            return;
        }
        
        const response = await fetch(`${API_URL}/eventsController.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'join',
                event_id: eventId,
                user_id: window.userId
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            alert('Te-ai înscris cu succes la eveniment!');
            location.reload();
        } else {
            alert('Eroare: ' + (data.message || 'Nu te-ai putut înscrie'));
        }
    } catch (error) {
        console.error('Eroare la inscriere:', error);
        alert('A apărut o eroare la înscriere!');
    }
}

/**
 * Sterge un eveniment
 */
async function deleteEvent(eventId) {
    if (!confirm('Ești sigur că vrei să ștergi acest eveniment?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}/eventsController.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ event_id: eventId })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            alert('Eveniment șters cu succes!');
            location.reload();
        } else {
            alert('Eroare: ' + (data.message || 'Nu s-a putut sterge evenimentul'));
        }
    } catch (error) {
        console.error('Eroare la stergere:', error);
        alert('A apărut o eroare la ștergere!');
    }
}

/**
 * Verifica daca utilizatorul este admin
 */
async function checkAdminStatus() {
    try {
        const response = await fetch('../controllers/checkAdminStatus.php');
        if (!response.ok) return false;
        
        const data = await response.json();
        return data.isAdmin === true;
    } catch (error) {
        console.error('Eroare la verificarea statusului de admin:', error);
        return false;
    }
}

/**
 * Creeaza butonul de actiune pentru un eveniment
 */
function createActionButton(event) {
    // Daca utilizatorul e deja inscris
    if (event.is_registered) {
        return `<button class="btn-registered" disabled>${event.is_past ? 'Ați participat' : 'Inscris'}</button>`;
    }
    
    // Verificam starea evenimentului
    if (event.is_past) {
        return '<button class="btn-past" disabled>Eveniment trecut</button>';
    }
    if (event.is_running) {
        return '<button class="btn-past" disabled>În desfășurare</button>';
    }
    
    // Verificam rolul utilizatorului
    if (window.isAdmin === true) {
        return `<button class="btn-delete" onclick="deleteEvent(${event.event_id})">Șterge eveniment</button>`;
    }
    if (parseInt(event.created_by) === parseInt(window.userId)) {
        return '<button class="btn-owner" disabled>Organizator</button>';
    }
    
    // Verificam capacitatea
    if (event.is_full) {
        return '<button class="btn-full" disabled>Locuri epuizate</button>';
    }
    
    // Buton de inscriere
    return `
        <form method="POST" action="../controllers/joinEventController.php">
            <input type="hidden" name="event_id" value="${event.event_id}">
            <button type="submit" name="register_event" class="btn-register">Inscrie-te</button>
        </form>
    `;
}

/**
 * Creeaza card pentru un eveniment
 */
function createEventCard(event) {
    const template = document.getElementById('eventCardTemplate');
    const card = template.content.cloneNode(true).querySelector('.event-card');
    
    // Setam clasele si badge-urile
    if (event.is_past) card.classList.add('past-event');
    if (event.is_full) card.querySelector('.event-full-badge').style.display = 'block';
    
    // Completam informatiile
    card.querySelector('.event-type-badge').textContent = event.event_type || 'Sport';
    card.querySelector('.event-title').textContent = event.event_name;
    card.querySelector('.date-value').textContent = new Date(event.event_date).toLocaleString();
    card.querySelector('.location-value').textContent = decodeURIComponent(event.location);
    
    // Afisam participantii
    const participantsText = event.max_participants > 0 ? 
        `${event.current_participants} / ${event.max_participants}` : 
        `${event.current_participants} / nelimitat`;
    card.querySelector('.participants-value').textContent = participantsText;
    
    // Afisam cerintele minime daca exista
    if (event.min_events_participated > 0) {
        const requirementsContainer = card.querySelector('.requirements-container');
        requirementsContainer.style.display = 'block';
        card.querySelector('.requirements-value').textContent = 
            `Minim ${event.min_events_participated} participări`;
    }   
    
    // Setam butonul de actiune si link-ul de detalii
    card.querySelector('.action-buttons').innerHTML = createActionButton(event);
    card.querySelector('.btn-details').href = `view_event.php?id=${event.event_id}`;
    
    return card;
}

/**
 * Afiseaza evenimentele in pagina
 */
function showEvents(events) {
    // Normalizam formatul datelor
    if (Array.isArray(events)) {
        const now = new Date();
        const future_events = [];
        const past_events = [];
        
        events.forEach(event => {
            const eventDate = new Date(event.event_date);
            if (eventDate > now) {
                future_events.push(event);
            } else {
                event.is_past = true;
                past_events.push(event);
            }
        });
        
        events = { future_events, past_events };
    }
    else if (!events.future_events && !events.past_events && events.data) {
        events = {
            future_events: events.data?.future_events || [],
            past_events: events.data?.past_events || []
        };
    }
    
    // Obtinem containerele pentru evenimente
    const futureEventsList = document.querySelector('#futureEvents .events-list');
    const pastEventsList = document.querySelector('#pastEvents .events-list');
    
    if (!futureEventsList || !pastEventsList) {
        return;
    }

    // Afisam evenimentele viitoare
    futureEventsList.innerHTML = '';
    if (events.future_events?.length) {
        events.future_events.forEach(event => {
            futureEventsList.appendChild(createEventCard(event));
        });
    } else {
        futureEventsList.innerHTML = '<div class="no-events">Nu există evenimente viitoare</div>';
    }

    // Afisam evenimentele trecute
    pastEventsList.innerHTML = '';
    if (events.past_events?.length) {
        events.past_events.forEach(event => {
            pastEventsList.appendChild(createEventCard(event));
        });
    } else {
        pastEventsList.innerHTML = '<div class="no-events">Nu există evenimente trecute</div>';
    }
}

/**
 * Initializeaza butonul de toggle pentru evenimente trecute
 */
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

/**
 * Cod executat la incarcarea paginii
 */
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Preluam ID-ul utilizatorului
        window.userId = document.querySelector('meta[name="user-id"]')?.content;
        
        // Verificam daca este admin
        window.isAdmin = await checkAdminStatus();
        
        // Preluam parametrii din URL
        const urlParams = new URLSearchParams(window.location.search);
        const lat = urlParams.get('lat');
        const lon = urlParams.get('lon');
        
        // Obtinem si afisam evenimentele
        const response = await getEvents(lat, lon);
        showEvents(response);
        initializePastEventsToggle();
    } catch (error) {
        console.error('Eroare la incarcarea evenimentelor:', error);
    }
});