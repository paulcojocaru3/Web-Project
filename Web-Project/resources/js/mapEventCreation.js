 const startLat = parseFloat(document.getElementById('location_lat').value);
 const startLng = parseFloat(document.getElementById('location_lon').value);        
        setTimeout(function() {            
            const map = L.map('myEventMap', {
                center: [startLat, startLng],
                zoom: 15,
                zoomControl: true,
                scrollWheelZoom: true
            });
            L.marker([startLat, startLng], {
                draggable: false,
            }).addTo(map);
  
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);
                        
            setTimeout(function() {
                map.invalidateSize();
                console.log('Harta redimensionata');
            }, 100);
            
        }, 500);