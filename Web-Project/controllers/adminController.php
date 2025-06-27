<?php
session_start();
require_once '../models/UserModel.php';
require_once '../models/EventModel.php';

header('Content-Type: application/json');

// Verifică dacă utilizatorul este autentificat și admin
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Nu sunteți autentificat']);
    exit();
}

$userModel = new UserModel();
if (!$userModel->isAdmin($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Nu aveți permisiuni de administrator']);
    exit();
}

// Procesează acțiunile de administrator
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_event' && isset($_POST['event_id'])) {
        $event_id = (int)$_POST['event_id'];
        
        $eventModel = new EventModel();
        if ($eventModel->deleteEvent($event_id)) {
            echo json_encode(['status' => 'success', 'message' => 'Eveniment șters cu succes']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Eroare la ștergerea evenimentului']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Acțiune invalidă']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metodă nepermisă']);
}
?>