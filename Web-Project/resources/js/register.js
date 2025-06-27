// Script pentru inregistrare prin AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Verific daca sunt pe pagina de inregistrare
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;
    
    // Adaug handler pentru evenimentul de submit
    registerForm.addEventListener('submit', async function(e) {
        // Opresc comportamentul normal al formularului
        e.preventDefault();
        
        // Modific butonul ca sa arate ca se proceseaza cererea
        const submitBtn = document.querySelector('.submit-btn');
        const originalBtnText = submitBtn.textContent;
        submitBtn.textContent = 'Inregistrare...';
        submitBtn.disabled = true;
        
        // Pregatesc containerul pentru afisarea erorilor
        let errorDiv = document.querySelector('.error-message');
        errorDiv.style.display = 'none';
        
        try {
            // Fac cererea la API cu datele din formular
            const response = await fetch('../controllers/registerController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: document.getElementById('username').value,
                    email: document.getElementById('email').value,
                    first_name: document.getElementById('first_name').value,
                    last_name: document.getElementById('last_name').value,
                    birth_date: document.getElementById('birth_date').value,
                    gender: document.getElementById('gender').value,
                    password: document.getElementById('password').value
                })
            });
            
            // Procesez raspunsul de la server
            const data = await response.json();
            
            if (response.ok && data.status === 'success') {
                // Redirectionez direct la login daca totul e ok
                window.location.href = '../views/login.php';
            } else {
                // Afisez erorile primite de la server
                if (Array.isArray(data.errors)) {
                    errorDiv.innerHTML = data.errors.join('<br>');
                } else {
                    errorDiv.textContent = data.message || 'Eroare la inregistrare!';
                }
                errorDiv.style.display = 'block';
                
                // Fac scroll catre mesajul de eroare
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Resetez butonul la starea initiala
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('Eroare la conectare:', error);
            
            // Afisez un mesaj generic pentru erori de conexiune
            errorDiv.textContent = 'Eroare de conexiune! Incercati din nou.';
            errorDiv.style.display = 'block';
            
            // Resetez butonul
            submitBtn.textContent = originalBtnText;
            submitBtn.disabled = false;
        }
    });
});