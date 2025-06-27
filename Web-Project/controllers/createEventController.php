<?php
header('Content-Type: application/json');

require_once '../models/EventModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$errors = [];

if (empty($data['event_name'])) {
    $errors[] = "Numele evenimentului este obligatoriu!";
} elseif (strlen($data['event_name']) < 3) {
    $errors[] = "Numele evenimentului trebuie să aibă cel puțin 3 caractere!";
}

if (empty($data['location'])) {
    $errors[] = "Locația este obligatorie!";
}

if (empty($data['description'])) {
    $errors[] = "Descrierea este obligatorie!";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'errors' => $errors
    ]);
    exit();
}

$eventModel = new EventModel();

$overlapping = $eventModel->checkOverlappingEvents(
    $data['location_lat'],
    $data['location_lon'],
    $data['event_date'],
    date('Y-m-d H:i:s', strtotime($data['event_date']) + ($data['duration'] * 3600))
);

if ($overlapping) {
    http_response_code(409);
    echo json_encode([
        'status' => 'error',
        'message' => 'Există deja un eveniment planificat la această locație în intervalul orar selectat.'
    ]);
    exit();
}

$result = $eventModel->createEventComplete(
    $data['event_name'],
    $data['location'],
    $data['description'],
    $data['event_date'],
    $data['location_lat'],
    $data['location_lon'],
    $data['max_participants'],
    $data['created_by'],
    $data['duration'],
    $data['min_events_participated']
);

if ($result) {
    http_response_code(201);
    echo json_encode([
        'status' => 'success',
        'message' => 'Eveniment creat cu succes',
        'event_id' => $result
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Eroare la crearea evenimentului'
    ]);
}
