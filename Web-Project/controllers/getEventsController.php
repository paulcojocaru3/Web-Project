<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized'
    ]);
    exit();
}

require_once('../models/EventModel.php');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $lat = isset($data['lat']) ? floatval($data['lat']) : null;
    $lon = isset($data['lon']) ? floatval($data['lon']) : null;

    $eventModel = new EventModel();
    $events = $eventModel->getEvenimente($lat, $lon);
    echo json_encode([
        'status' => 'success',
        'future_events' => $events['future_events'],
        'past_events' => $events['past_events']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>