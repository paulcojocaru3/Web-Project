<?php
session_start();
require_once '../models/UserModel.php';
require_once '../models/EventModel.php';

$userModel = new UserModel();
$eventModel = new EventModel();

// Verifică dacă utilizatorul este admin
if (!isset($_SESSION['user_id']) || !$userModel->isAdmin($_SESSION['user_id'])) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit();
}

// Handler pentru import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($extension === 'csv') {
            $handle = fopen($file['tmp_name'], 'r');
            $header = fgetcsv($handle); // Skip header row
            
            $imported = 0;
            while (($data = fgetcsv($handle)) !== FALSE) {
                // Verificăm dacă avem toate datele necesare
                if (count($data) < 5) {
                    continue; // Skip invalid rows
                }

                $eventData = [
                    'event_name' => $data[1] ?? '',
                    'location' => $data[2] ?? '',
                    'description' => $data[3] ?? '',
                    'event_date' => $data[4] ?? date('Y-m-d H:i:s'),
                    'location_lat' => $data[5] ?? 0,
                    'location_lon' => $data[6] ?? 0,
                    'max_participants' => intval($data[7] ?? 0),
                    'created_by' => $_SESSION['user_id'], // Folosim ID-ul utilizatorului curent
                    'event_type' => $data[14] ?? 'general',
                    'min_events_participated' => intval($data[13] ?? 0),
                    'duration' => intval($data[20] ?? 1)
                ];

                try {
                    if ($eventModel->createEventComplete(
                        $eventData['event_name'],
                        $eventData['location'],
                        $eventData['description'],
                        $eventData['event_date'],
                        $eventData['location_lat'],
                        $eventData['location_lon'],
                        $eventData['max_participants'],
                        $eventData['created_by'],
                        $eventData['duration'],
                        $eventData['min_events_participated']
                    )) {
                        $imported++;
                    }
                } catch (Exception $e) {
                    error_log("Error importing event: " . $e->getMessage());
                    continue;
                }
            }
            
            fclose($handle);
            echo json_encode([
                'status' => 'success', 
                'message' => "Successfully imported $imported events"
            ]);
        } 
        else if ($extension === 'json') {
            $jsonData = file_get_contents($file['tmp_name']);
            $events = json_decode($jsonData, true);
            
            if ($events && is_array($events)) {
                $imported = 0;
                foreach ($events as $event) {
                    try {
                        if ($eventModel->createEventComplete(
                            $event['event_name'],
                            $event['location'],
                            $event['description'],
                            $event['event_date'],
                            $event['location_lat'],
                            $event['location_lon'],
                            $event['max_participants'],
                            $_SESSION['user_id'],
                            $event['duration'] ?? 1,
                            $event['min_events_participated'] ?? 0
                        )) {
                            $imported++;
                        }
                    } catch (Exception $e) {
                        error_log("Error importing event from JSON: " . $e->getMessage());
                        continue;
                    }
                }
                
                echo json_encode([
                    'status' => 'success', 
                    'message' => "Successfully imported $imported events"
                ]);
            } else {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Invalid JSON format'
                ]);
            }
        } 
        else {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Invalid file type. Only CSV and JSON are supported.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'File upload failed: ' . $file['error']
        ]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'export') {
        $format = isset($_GET['format']) ? $_GET['format'] : 'csv';
        $events = $eventModel->getAllEvents();
        
        if ($format === 'json') {
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="evenimente_'.date('Y-m-d').'.json"');
            echo json_encode($events, JSON_PRETTY_PRINT);
        } else {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="evenimente_'.date('Y-m-d').'.csv"');
            
            $output = fopen('php://output', 'w');
            
            // Updated CSV header with all columns
            fputcsv($output, [
                'ID',
                'Nume Eveniment',
                'Locație',
                'Descriere',
                'Data Eveniment',
                'Latitudine',
                'Longitudine',
                'Participanți Max',
                'Creat De',
                'Status',
                'Data Creare',
                'Vârstă Minimă',
                'Gen Necesar',
                'Evenimente Minime Participare',
                'Tip Eveniment',
                'Descriere Eveniment',
                'Latitudine',
                'Longitudine',
                'Politică Participare',
                'Participări Minime',
                'Durată'
            ]);
            
            // Updated data rows with all columns
            foreach ($events as $event) {
                fputcsv($output, [
                    $event['event_id'],
                    $event['event_name'],
                    $event['location'],
                    $event['description'],
                    $event['event_date'],
                    $event['location_lat'],
                    $event['location_lon'],
                    $event['max_participants'],
                    $event['created_by'],
                    $event['status'],
                    $event['created_at'],
                    $event['min_age'],
                    $event['required_gender'],
                    $event['min_events_participated'],
                    $event['event_type'],
                    $event['event_description'],
                    $event['lat'],
                    $event['lng'],
                    $event['participation_policy'],
                    $event['min_participations'],
                    $event['duration']
                ]);
            }
            
            fclose($output);
        }
        exit();
    }
}