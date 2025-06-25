// Script pentru autentificare prin AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Verific daca sunt pe pagina de login
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    
    // Adaug handler pentru evenimentul de submit
    loginForm.addEventListener('submit', async function(e) {
        // Opresc comportamentul normal al formularului
        e.preventDefault();
        
        // Modific butonul ca sa arate ca se proceseaza cererea
        const submitBtn = document.querySelector('.submit-btn');
        const originalBtnText = submitBtn.textContent;
        submitBtn.textContent = 'Autentificare...';
        submitBtn.disabled = true;
        
        // Pregatesc containerul pentru afisarea erorilor
        let errorDiv = document.querySelector('.error-message');
        errorDiv.style.display = 'none';
        
        try {
            // Fac cererea la API cu datele din formular
            const response = await fetch('../controllers/loginController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: document.getElementById('username').value,
                    password: document.getElementById('password').value
                })
            });
            
            // Procesez raspunsul de la server
            const data = await response.json();
            
            if (response.ok && data.status === 'success') {
                // Redirectionez utilizatorul catre dashboard daca totul e ok
                window.location.href = '../views/dashboard.php';
            } else {
                // Afisez eroarea primita de la server
                errorDiv.textContent = data.message || 'Eroare de autentificare!';
                errorDiv.style.display = 'block';
                
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