<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../models/UserModel.php';

$userModel = new UserModel();
$user = $userModel->getUserById($_SESSION['user_id']);
$username = $user['username'];
$isAdmin = $userModel->isAdmin($_SESSION['user_id']); // Add this line
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Iasi Joaca</title>
    <link href="../resources/css/styleStartPage.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo-container">
            <a href="../views/dashboard.php">
                <img src="../resources/images/logoSite2.png" alt="Iasi Joaca" class="logoSite">
            </a>
        </div>
        <nav>
            <ul class="menu">
                <li><a href="../views/dashboard.php" class="paginaActuala">Dashboard</a></li>
                <li><a href="../views/harta.php">Harta terenuri</a></li> 
                <li><a href="../views/evenimente.php">Evenimente</a></li>
                <li><a href="../views/profile.php">Profil</a></li>
                <li><a href="../controllers/logoutController.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="welcome-section">
        <h1>Bine ai venit la Iasi Joaca, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Descopera terenurile de sport din Iasi si participa la evenimente sportive organizate de comunitate.</p>
    </div>

    <div class="features-section">
        <div class="feature-card">
            <h2>Harta Terenurilor</h2>
            <p>Exploreaza terenurile de sport publice din Iasi si creaza evenimente noi.</p>
            <a href="../views/harta.php" class="feature-btn">Vezi harta</a>
        </div>

        <div class="feature-card">
           <h2>Primește notificări despre evenimente noi prin RSS</h2>
            <p>Vei primi actualizări despre evenimentele viitoare.</p>
            <a href="rss.php" class="btn-rss" target="_blank">Abonează-te la RSS</a>
        </div>

        <div class="feature-card">
            <h2>Profilul Meu</h2>
            <p>Verifica profilul tau si istoricul participarilor la evenimente.</p>
            <a href="../views/profile.php" class="feature-btn">Vezi profil</a>
        </div>

        <?php if ($isAdmin): ?>
            <div class="feature-card">
                <h2>Administrare Date</h2>
                <p>Import/Export date în formate CSV și JSON</p>
                <div class="admin-buttons">
                    <div class="export-buttons">
                        <a href="../controllers/dataController.php?action=export&format=csv" class="feature-btn export-btn">Export CSV</a>
                        <a href="../controllers/dataController.php?action=export&format=json" class="feature-btn export-btn">Export JSON</a>
                    </div>
                    <label for="importFile" class="feature-btn import-btn">Import Date</label>
                    <input type="file" id="importFile" accept=".csv,.json" style="display: none;">
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($isAdmin): ?>
    <script>
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
    </script>
    <?php endif; ?>
</body>
</html>
