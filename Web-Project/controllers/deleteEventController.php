<?php
// filepath: c:\xampp\htdocs\Web-Project\controllers\deleteEventController.php
session_start();
require_once '../models/EventModel.php';

header('Content-Type: application/json');

try {
    // ADĂUGAT: Verifică dacă este cerere DELETE și citește corpul cererii
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        $eventId = $data['event_id'] ?? null;
    } else {
        // Obține ID-ul evenimentului din POST sau GET pentru alte metode
        $eventId = $_POST['event_id'] ?? $_GET['event_id'] ?? null;
    }
    
    // Log pentru debugging
    error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Event ID: " . ($eventId ?: 'null'));
    
    if (!$eventId) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Event ID is required'
        ]);
        exit;
    }
    
    // Verifică autorizarea (opțional, în funcție de aplicația ta)
    $userId = $_SESSION['user_id'] ?? null;
    
    // Inițializează modelul
    $eventModel = new EventModel();
    
    // Șterge evenimentul
    $result = $eventModel->deleteEvent($eventId);
    
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Event deleted successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete event'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}