document.getElementById('importFile').addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('action', 'import');

            try {
                const response = await fetch('../controllers/dataController.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                alert(result.message);
                
                if (result.status === 'success') {
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('A apărut o eroare la import');
            }
        });