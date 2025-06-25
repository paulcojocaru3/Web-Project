document.addEventListener('DOMContentLoaded', function() {
    const showPastEventsBtn = document.getElementById('showPastEvents');
    const pastEventsContainer = document.getElementById('pastEventsContainer');
    
    if (showPastEventsBtn) {
        showPastEventsBtn.addEventListener('click', function() {
            pastEventsContainer.classList.toggle('visible');
            
            if (pastEventsContainer.classList.contains('visible')) {
                showPastEventsBtn.textContent = 'Ascunde evenimente trecute';
            } else {
                const pastEventsCount = document.querySelectorAll('.past-event').length;
                showPastEventsBtn.textContent = `Arata evenimente trecute (${pastEventsCount})`;
            }
        });
    }
});