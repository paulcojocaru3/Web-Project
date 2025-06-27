<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Harta terenuri</title>
    <link href="../resources/css/styleStartPage.css" rel="stylesheet">
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
                <li><a href="../views/harta.php" class="paginaActuala">Harta terenuri</a></li>
                <li><a href="../views/evenimente.php">Evenimente</a></li>
                <li><a href="../views/profile.php">Profil</a></li>
                <li><a href="../controllers/logoutController.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div id="map" style="height: calc(100vh - 80px); width: 100%; margin-top: 80px;"></div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="../resources/js/map.js"></script>
</body>
</html>
