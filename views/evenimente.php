<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;
$location_name = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '';
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evenimente - Iasi Joaca</title>
    <link href="../resources/css/evenimenteStyle.css" rel="stylesheet">
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
                <li><a href="../views/dashboard.php">Dashboard</a></li>
                <li><a href="../views/harta.php">Harta terenuri</a></li>
                <li><a href="../views/evenimente.php" class="paginaActuala">Evenimente</a></li>
                <li><a href="../views/profile.php">Profil</a></li>
                <li><a href="../controllers/logoutController.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="events-container">
        <div class="events-header">
            <h1>Evenimente</h1>
            <?php if ($lat && $lon): ?>
                <a href="event_create.php?lat=<?php echo $lat; ?>&lon=<?php echo $lon; ?>&location=<?php echo urlencode($location_name); ?>" 
                   class="btn-create">Creaza eveniment nou aici</a>
            <?php else: ?>
                <a href="harta.php" class="btn-create">Creeaza Eveniment</a>
            <?php endif; ?>
        </div>

        <div id="eventsContent">
            <div id="futureEvents" class="events-section">
                <h2 class="section-title">Evenimente Viitoare</h2>
                <div class="events-list">
                    <div class="loading">Se încarcă evenimentele...</div>
                </div>
            </div>

            <button id="togglePastEvents" class="toggle-past-events">
                Arată evenimente trecute
            </button>

            <div id="pastEvents" class="events-section" style="display: none;">
                <h2 class="section-title">Evenimente Trecute</h2>
                <div class="events-list">
                    <div class="loading">Se încarcă evenimentele...</div>
                </div>
            </div>
        </div>

        <template id="eventCardTemplate">
            <div class="event-card">
                <div class="event-type-badge"></div>
                <div class="event-full-badge" style="display: none;">Complet</div>
                <h2 class="event-title"></h2>
                <div class="event-details">
                    <div class="detail-item">
                        <span class="detail-label">Data:</span>
                        <span class="detail-value date-value"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Locatie:</span>
                        <span class="detail-value location-value"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Participanti:</span>
                        <span class="detail-value participants-value"></span>
                    </div>
                    <div class="detail-item requirements-container" style="display: none;">
                        <span class="detail-label">Cerință:</span>
                        <span class="detail-value requirements-value"></span>
                    </div>
                </div>
                <div class="event-actions">
                    <div class="action-buttons"></div>
                    <a class="btn-details">Detalii</a>
                </div>
        </template>
    </div>

    <script src="../resources/js/getEvents.js"></script>
    <script>
        window.userId = <?php echo $_SESSION['user_id']; ?>;
    </script>
</body>
</html>