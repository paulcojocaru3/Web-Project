<?php
session_start();

require_once __DIR__ . '/../models/eventService.php';

// Functie pentru logare
function logDebug($message, $data = null) {
    error_log(sprintf("[DEBUG] %s: %s", $message, 
        $data ? json_encode($data) : 'null'));
}

// Configurare headere
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Functie pentru trimiterea raspunsurilor JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
}

try {
    $eventService = new EventService();

    // Procesare cereri GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $lat = $_GET['lat'] ?? null;
        $lon = $_GET['lon'] ?? null;
        $userId = $_GET['user_id'] ?? null;
        
        // Verificare evenimente in locatie
        if (isset($_GET['action']) && $_GET['action'] === 'check') {
            if (!$lat || !$lon) {
                sendJsonResponse([
                    'status' => 'error',
                    'message' => 'fara coordonate'
                ], 400);
                exit;
            }
            
            $result = $eventService->checkEventsAtLocation($lat, $lon);
            sendJsonResponse([
                'status' => 'success',
                'data' => $result
            ]);
        } 
        // Obtine toate evenimentele
        else {
            $result = $eventService->getAllEvents($lat, $lon, $userId);
            sendJsonResponse([
                'status' => 'success',
                'data' => $result
            ]);
        }
    } 
    // Procesare cereri POST
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            sendJsonResponse([
                'status' => 'error',
                'message' => 'json invalid'
            ], 400);
            exit;
        }
        
        // Inscriere la eveniment
        if (isset($data['action']) && $data['action'] === 'join') {
            if (empty($data['event_id']) || empty($data['user_id'])) {
                sendJsonResponse([
                    'status' => 'error',
                    'message' => 'lipsesc fields'
                ], 400);
                exit;
            }
            
            $result = $eventService->joinEvent($data['event_id'], $data['user_id']);
            sendJsonResponse($result);
        } 
        // Creare eveniment
        else if (isset($data['action']) && $data['action'] === 'create') {
            if (empty($data['event_name']) || empty($data['event_date'])) {
                sendJsonResponse([
                    'status' => 'error',
                    'message' => 'lipsesc fields'
                ], 400);
                exit;
            }
            
            $result = $eventService->createEvent($data);
            sendJsonResponse($result, $result['status'] === 'success' ? 201 : 400);
        }
        // Stergere eveniment (prin POST)
        else if (isset($data['action']) && $data['action'] === 'delete') {
            if (empty($data['event_id'])) {
                sendJsonResponse([
                    'status' => 'error',
                    'message' => 'event_id required'
                ], 400);
                exit;
            }
            
            $result = $eventService->deleteEvent($data['event_id']);
            sendJsonResponse($result);
        }
        // Actiune necunoscuta
        else {
            sendJsonResponse([
                'status' => 'error',
                'message' => 'Invalid action'
            ], 400);
        }
    } 
    // Procesare cereri DELETE
    else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $eventId = $data['event_id'] ?? null;
        
        if (!$eventId) {
            sendJsonResponse([
                'status' => 'error',
                'message' => 'event_id required'
            ], 400);
            exit;
        }
        
        $result = $eventService->deleteEvent($eventId);
        sendJsonResponse($result);
    } 
    // OPTIONS pentru CORS
    else if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        sendJsonResponse(['status' => 'success']);
    }
    // Metoda nepermisa
    else {
        sendJsonResponse([
            'status' => 'error',
            'message' => 'restricted method'
        ], 405);
    }
} catch (Exception $e) {
    sendJsonResponse([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ], 500);
}