<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include_once '../controllers/profileController.php';

$controller = new ProfileController();
$data = $controller->getProfileData($_SESSION['user_id']);
$events = $controller->getUserEvents($_SESSION['user_id']);

$user = $data['user'];
// Eliminaam referinta la $profile

$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil</title>
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
            <h1>Profil Utilizator</h1>
            <a href="../views/edit_profile.php" class="btn-edit">Editare Profil</a>
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
            <div class="profile-section user-info">
                <h2>Informatii Utilizator</h2>
                
                <div class="info-item">
                    <span class="info-label">Nume utilizator:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Nume:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['firstname'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Prenume:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['lastname'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Data inregistrarii:</span>
                    <span class="info-value">
                        <?php 
                            echo isset($user['created_at']) ? date('d.m.Y', strtotime($user['created_at'])) : 'N/A'; 
                        ?>
                    </span>
                </div>
            </div>
            
            <div class="profile-section events-section">
                <h2>Evenimente</h2>
                
                <?php if (!empty($events)): ?>
                    <div class="events-list">
                        <?php foreach ($events as $event): ?>
                            <div class="event-item">
                                <div>
                                    <div class="event-title">
                                        <a href="../views/view_event.php?id=<?php echo $event['event_id']; ?>">
                                            <?php echo htmlspecialchars($event['event_name']); ?>
                                        </a>
                                    </div>
                                    <div class="event-details">
                                        <?php echo date('d.m.Y H:i', strtotime($event['event_date'])); ?> 
                                        <?php echo htmlspecialchars(urldecode($event['location'])); ?>
                                    </div>
                                </div>
                                <div class="event-status">
                                    Participant
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-events">Nu aveti evenimente. <a href="../views/evenimente.php">Explorati evenimente</a> sau <a href="../views/event_create.php">creati unul nou</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>