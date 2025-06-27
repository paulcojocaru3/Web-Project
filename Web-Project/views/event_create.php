<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lat = isset($_GET['lat']) ? htmlspecialchars($_GET['lat']) : '47.151726';
$lon = isset($_GET['lon']) ? htmlspecialchars($_GET['lon']) : '27.587914';
$location = isset($_GET['location']) ? htmlspecialchars(urldecode($_GET['location'])) : '';
$min_datetime = date('Y-m-d\TH:i', strtotime('+1 hour'));
$default_datetime = date('Y-m-d\TH:i', strtotime('+2 hours'));

$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); 
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Creare Eveniment - Iasi Joaca</title>
    <link href="../resources/css/styleStartPage.css" rel="stylesheet">
    <link href="../resources/css/eventCreationStyle.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
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

    <div class="form-container">
        <div class="form-header">
            <h1>Creare Eveniment Nou</h1>
        </div>
        
        <form id="eventForm" method="POST">
            <div class="alert error" style="display: none;">
            </div>
            
            <div class="form-group">
                <label for="event_name" class="required-label">Numele evenimentului</label>
                <input type="text" id="event_name" name="event_name" minlength="3" maxlength="100" required>
            </div>
            
            <div class="form-group">
                <label for="description" class="required-label">Descriere</label>
                <textarea id="description" name="description" rows="4" minlength="10" maxlength="1000" required></textarea>
            </div>

            <div class="form-group">
                <label for="location" class="required-label">Locatie</label>
                <input type="text" id="location" name="location" value="<?php echo $location; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="event_date" class="required-label">Data si Ora</label>
                <input type="datetime-local" id="event_date" name="event_date" 
                       min="<?php echo $min_datetime; ?>"
                       value="<?php echo $default_datetime; ?>" required>
            </div>

            <div class="form-group">
                <label for="duration" class="required-label">Durata eveniment (ore)</label>
                <input type="number" id="duration" min="1" max="5" value="2" required>
                <small>Durata maximă permisă este de 5 ore</small>
            </div>

            <div class="form-group">
                <label for="max_events">Numar maxim de participanti (0 = nelimitat)</label>
                <input type="number" id="max_events" min="0" max="1000" value="10">
            </div>
            
            <div class="form-group">
                <label for="min_events_participated">Numar minim de participari necesare</label>
                <input type="number" id="min_events_participated"  name="min_events_participated"  min="0"  max="100" value="0">
                <small>0 = fara restrictie, oricine poate participa</small>
            </div>

            <div class="map-section">
                <label><strong>🗺️ Locatia evenimentului</strong></label>
                <div id="myEventMap" class="map-container"></div>
            </div>

            <input type="hidden" id="location_lat" value="<?php echo $lat; ?>">
            <input type="hidden" id="location_lon" value="<?php echo $lon; ?>">
            <input type="hidden" id="user_id" value="<?php echo $_SESSION['user_id']; ?>">

            <div class="form-footer">
                <a href="evenimente.php" class="cancel-btn">Anuleaza</a>
                <button type="submit" class="submit-btn">Creeaza Eveniment</button>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="../resources/js/mapEventCreation.js"></script>
    <script src="../resources/js/createEvent.js"></script>
</body>
</html>