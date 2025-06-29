<?php
header('Content-Type: application/json');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Dezactivăm afișarea erorilor în răspunsul API
ini_set('error_log', __DIR__ . '/getevents_error.log'); // Logăm erorile într-un fișier separat

error_log("--- getEventsController started ---");

// Pentru API-uri publice
$allowPublicAccess = true;

try {
    // Încercăm să includem clasa model
    error_log("Încercăm să includem EventModel.php");
    require_once('../models/EventModel.php');
    error_log("EventModel.php inclus cu succes");
    
    // Parametrii GET
    $lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
    $lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;
    error_log("Parametri: lat=$lat, lon=$lon");

    // Aici implementăm decizia: date statice sau dinamice
    $useDynamicData = true; // Schimbă la TRUE pentru a testa modelul

    if ($useDynamicData) {
        // Încercăm varianta dinamică
        error_log("Creăm instanță EventModel");
        $eventModel = new EventModel();
        error_log("EventModel instanțiat");
        
        error_log("Apelăm getEvenimente()");
        $events = $eventModel->getEvenimente($lat, $lon);
        error_log("getEvenimente() returnat cu succes");
        
        echo json_encode([
            'status' => 'success',
            'future_events' => $events['future_events'],
            'past_events' => $events['past_events']
        ]);
    }

} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    error_log("PHP Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'PHP Error: ' . $e->getMessage()
    ]);
}

error_log("--- getEventsController finished ---");
?>