<?php
session_start();
require_once '../models/UserModel.php';
require_once '../models/EventModel.php';

$userModel = new UserModel();
$eventModel = new EventModel();

if (!isset($_SESSION['user_id']) || !$userModel->isAdmin($_SESSION['user_id'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        if ($extension === 'csv') {
            $handle = fopen($file['tmp_name'], 'r');
            $header = fgetcsv($handle);
            
            $imported = 0;
            while (($data = fgetcsv($handle)) !== FALSE) {
                $eventData = [
                    'event_name' => $data[1],
                    'location' => $data[2],
                    'description' => $data[3],
                    'event_date' => $data[4],
                    'location_lat' => $data[5],
                    'location_lon' => $data[6],
                    'max_participants' => $data[7],
                    'created_by' => $data[8],
                    'status' => $data[9],
                    'min_age' => $data[11],
                    'required_gender' => $data[12],
                    'min_events_participated' => $data[13],
                    'event_type' => $data[14],
                    'event_description' => $data[15],
                    'lat' => $data[16],
                    'lng' => $data[17],
                    'participation_policy' => $data[18],
                    'min_participations' => $data[19],
                    'duration' => $data[20]
                ];
                
                if ($eventModel->createEventComplete($eventData)) {
                    $imported++;
                }
            }
            
            fclose($handle);
            echo json_encode(['status' => 'success', 'message' => "Imported $imported events"]);
        } 
        else if ($extension === 'json') {
            // Import from JSON
            $jsonData = file_get_contents($file['tmp_name']);
            $events = json_decode($jsonData, true);
            
            if ($events) {
                $imported = 0;
                foreach ($events as $event) {
                    if ($eventModel->createEvent($event)) {
                        $imported++;
                    }
                }
                
                echo json_encode(['status' => 'success', 'message' => "Imported $imported events"]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
            }
        } 
        else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
    }
}