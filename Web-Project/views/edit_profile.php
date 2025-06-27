<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include_once '../controllers/profileController.php';

$controller = new ProfileController();
$data = $controller->getProfileData($_SESSION['user_id']);

$user = $data['user'];
$profile = $data['profile'];
$user_id = $_SESSION['user_id'];

$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editare Profil</title>
    <link href="../resources/css/styleStartPage.css" rel="stylesheet">
    <link href="../resources/css/profileStyle.css" rel="stylesheet">
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
                <li><a href="../views/evenimente.php">Evenimente</a></li>
                <li><a href="../views/profile.php" class="paginaActuala">Profil</a></li>
                <li><a href="../controllers/logoutController.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="profile-container">
        <div class="profile-header">
            <h1>Editare Profil</h1>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-content">
            <div class="profile-section">
                <form method="POST" action="../controllers/profileController.php" class="edit-profile-form">
                    <div class="form-group">
                        <label for="first_name">Nume:</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['firstname'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Prenume:</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['lastname'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="update_profile" class="btn-save">Salveaza</button>
                        <a href="profile.php" class="btn-cancel">Anuleaza</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>