<?php
session_start();
require_once '../controllers/viewEventController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: evenimente.php");
    exit();
}

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$controller = new EventModel();
$data = $controller->getEventDetails($event_id, $user_id);

if ($data === null) {
    header("Location: evenimente.php");
    exit();
}

$event = $data['event'];
$is_past = $data['is_past'];
$is_registered = $data['is_registered'];
$is_creator = $data['is_creator'];
$participants = $data['participants'];
$is_full = $data['is_full'];
$chat_messages = $data['chat_messages'];

$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

$participants_count = count($participants);
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($event['event_name']); ?> - Detalii Eveniment</title>
    <link href="../resources/css/styleStartPage.css" rel="stylesheet">
    <link href="../resources/css/viewEventStyle.css" rel="stylesheet">
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
                <li><a href="../views/dashboard.php">Pagina principala</a></li>
                <li><a href="../views/harta.php">Harta terenuri</a></li>
                <li><a href="../views/evenimente.php">Evenimente</a></li>
                <li><a href="../views/profile.php">Profil</a></li>
                <li><a href="../controllers/logoutController.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="event-container">
        <div class="event-header">
            <h1>
                <?php echo htmlspecialchars($event['event_name']); ?>
                <?php if ($is_past): ?>
                    <span class="event-past-badge">Eveniment trecut</span>
                <?php elseif ($is_full && !$is_registered): ?>
                    <span class="event-full-badge">Complet</span>
                <?php endif; ?>
            </h1>
            <div class="event-badge"><?php echo htmlspecialchars($event['event_type'] ?? 'Sport'); ?></div>
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
        
        <div class="event-content">
            <div class="event-details-section">
                <div class="event-main-details">
                    <div class="detail-row">
                        <div class="detail-icon">📅</div>
                        <div class="detail-info">
                            <div class="detail-label">Data si ora</div>
                            <div class="detail-value"><?php echo date('d.m.Y H:i', strtotime($event['event_date'])); ?></div>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-icon">📍</div>
                        <div class="detail-info">
                            <div class="detail-label">Locatie</div>
                            <span class="detail-value"><?php echo htmlspecialchars(urldecode($event['location'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-icon">👥</div>
                        <div class="detail-info">
                            <div class="detail-label">Participanti</div>
                            <div class="detail-value">
                                <?php 
                                    echo $participants_count; 
                                    if ($event['max_participants'] > 0) {
                                        echo ' / ' . $event['max_participants'];
                                    } else {
                                        echo ' / nelimitat';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-icon">⏰</div>
                        <div class="detail-info">
                            <div class="detail-label">Se termină la</div>
                            <div class="detail-value">
                                <?php 
                                    $end_time = strtotime($event['event_date']) + ($event['duration'] * 3600);
                                    echo date('d.m.Y H:i', $end_time); 
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($event['min_events_participated'] > 0): ?>
                    <div class="detail-row">
                        <div class="detail-icon">🏆</div>
                        <div class="detail-info">
                            <div class="detail-label">Participări minime necesare</div>
                            <div class="detail-value"><?php echo $event['min_events_participated']; ?> evenimente</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="chat-legend">
                    <div class="legend-item">
                        <span class="emoji">👑</span>
                        <span class="legend-text">-> Organizator</span>
                    </div>
                    <div class="legend-item">
                        <span class="emoji">👤</span>
                        <span class="legend-text">-> Participant</span>
                    </div>
                </div>
                
                <?php if (!empty($event['description']) || !empty($event['event_description'])): ?>
                    <div class="event-description">
                        <h3>Descriere</h3>
                        <p><?php echo nl2br(htmlspecialchars($event['description'] ?? $event['event_description'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($event['lat']) && !empty($event['lng'])): ?>
                    <div class="event-location">
                        <h3>Locatie pe harta</h3>
                        <div id="map" class="event-map"></div>
                    </div>
                <?php endif; ?>
                
                <div class="event-actions">
                    <?php if ($is_registered): ?>
                        <form method="POST" action="../controllers/viewEventController.php?id=<?php echo $event_id; ?>">
                            <button type="submit" name="unregister_event" class="btn-unregister">Dezabonare</button>
                        </form>
                    <?php elseif ($is_past): ?>
                        <button class="btn-past" disabled>Eveniment trecut</button>
                    <?php elseif ($is_full): ?>
                        <button class="btn-full" disabled>Locuri epuizate</button>
                    <?php else: ?>
                        <?php if ($event['created_by'] == $_SESSION['user_id']): ?>
                            <button class="btn-register" disabled>Organizator</button>
                        <?php else: ?>
                        <form method="POST" action="../controllers/viewEventController.php?id=<?php echo $event_id; ?>">
                            <button type="submit" name="register_event" class="btn-register">Inscriere</button>
                        </form>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <a href="evenimente.php" class="btn-back">Inapoi la evenimente</a>
                </div>
            </div>
            
            <div class="event-sidebar">
                <div class="participants-section">
                    <h3>Participanti (<?php echo $participants_count; ?>)</h3>
                    
                    <?php if (!empty($participants)): ?>
                        <ul class="participants-list">
                            <?php foreach ($participants as $participant): ?>
                                <li>
                                    <span class="participant-name"><?php echo htmlspecialchars($participant['username']); ?></span>
                                    <span class="participant-date"><?php echo date('d.m.Y', strtotime($participant['registration_date'])); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-participants">Niciun participant inscris inca.</p>
                    <?php endif; ?>
                </div>
                
                <div class="chat-section" id="chat">
                    <h3>Chat eveniment</h3>
                    
                    <div class="chat-messages" id="chatMessages">
                        <?php if (!empty($chat_messages)): ?>
                            <?php foreach ($chat_messages as $message): ?>
                                <div class="chat-message <?php echo ($message['user_id'] == $user_id) ? 'own-message' : ''; ?>">
                                    <div class="message-header">
                                             <span class="message-author">
                                                <?php echo htmlspecialchars($message['username']); 
                                                    if ($message['user_id'] == $event['created_by']) {
                                                        echo ' 👑 '; 
                                                    } else if ($message['user_id'] == $user_id) {
                                                        echo ' 👤'; 
                                                    } ?>
                                            </span>
                                        <span class="message-time"><?php echo date('H:i', strtotime($message['sent_at'])); ?></span>
                                    </div>
                                    <div class="message-content">
                                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-messages">Nu exista mesaje inca. Fii primul care incepe conversatia!</p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($is_registered || $is_creator): ?>
                        <form method="POST" action="../controllers/viewEventController.php?id=<?php echo $event_id; ?>" class="chat-form">
                            <textarea name="message" placeholder="Scrie un mesaj..." required></textarea>
                            <button type="submit" name="send_message">Trimite</button>
                        </form>
                    <?php else: ?>
                        <div class="chat-login-message">Trebuie sa fiti inscris la eveniment pentru a putea trimite mesaje.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!empty($event['lat']) && !empty($event['lng'])): ?>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([<?php echo $event['lat']; ?>, <?php echo $event['lng']; ?>], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            L.marker([<?php echo $event['lat']; ?>, <?php echo $event['lng']; ?>])
                .addTo(map)
                .bindPopup("<?php echo htmlspecialchars($event['location']); ?>");
                
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        });
    </script>
    <?php endif; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chatMessages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
    </script>
</body>
</html>