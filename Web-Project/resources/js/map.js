function redirectCreateEvent(lat, lon, fieldName) {
    console.log("Redirecționare către event_create.php...");
    window.location.href = "/Web-Project/views/event_create.php?lat=" + lat + "&lon=" + lon + "&location=" + encodeURIComponent(fieldName);
}

async function checkForEvents(lat, lon) {
    try {
        const response = await fetch(`/Web-Project/controllers/checkEventsController.php?lat=${lat}&lon=${lon}`);
        if(!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();

        if (data.success !== undefined) {
            return data.success ? data.marker : 'gray'; 
        } else {
            console.error("Unexpected response format:", data);
            return 'gray';
        }
    } catch (error) {
        console.error("Error checking for events:", error);
        return 'gray'; // Return gray on error
    }
}

const map = L.map('map', {
    center: [47.151726, 27.587914], 
    zoom: 12.9,  
    minZoom: 12,  
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

const bounds = [
    [47.10, 27.50],  
    [47.20, 27.70]   
];

map.setMaxBounds(bounds);
map.on('drag', function() {
    map.panInsideBounds(bounds);  
});

const overpassUrl = "https://overpass-api.de/api/interpreter";
const query = `
    [out:json];
    area[name="Zona Metropolitană Iași"]->.searchArea;
    (
        node["leisure"="pitch"](area.searchArea);
        way["leisure"="pitch"](area.searchArea);
        relation["leisure"="pitch"](area.searchArea);
    );
    out center tags;
`;

const greenIcon = L.icon({
    iconUrl: '../resources/images/markers/green_icon.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34]
});
const grayIcon = L.icon({
    iconUrl: '../resources/images/markers/gray_icon.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34]
});
const redIcon = L.icon({
    iconUrl: '../resources/images/markers/red_icon.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34]
});

fetch(overpassUrl, {
    method: "POST",
    headers: { "Content-Type": "text/plain" },
    body: query
})
.then(response => response.json())
.then(data => {
    data.elements.forEach(async element => {
        const lat = element.lat || element.center?.lat;
        const lon = element.lon || element.center?.lon;
        
        if (lat && lon) {
            const fieldName = element.tags?.name || 
                             element.tags?.sport && `Teren de ${element.tags.sport}` ||
                             'Teren de sport';
            
            const markerColor = await checkForEvents(lat, lon); 
            const icon = markerColor === 'green' ? greenIcon : grayIcon;
            
            const popupContent = `
                <strong>${fieldName}</strong><br>
                <small>Locație: ${lat.toFixed(6)}, ${lon.toFixed(6)}</small><br>
                ${markerColor === 'green' ? 
                    `<button onclick="window.location.href='../views/evenimente.php?lat=${lat}&lon=${lon}&location=${encodeURIComponent(fieldName)}'">Vezi Evenimente</button>` : 
                    `<button onclick="redirectCreateEvent(${lat}, ${lon}, '${encodeURIComponent(fieldName)}')">Creează Eveniment</button>`
                }
            `;

            L.marker([lat, lon], { icon: icon })
                .addTo(map)
                .bindPopup(popupContent);
        }
    });
})
.catch(error => console.error("Eroare:", error));
