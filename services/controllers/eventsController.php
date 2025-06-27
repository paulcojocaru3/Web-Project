<?php
// filepath: c:\xampp\htdocs\services\api\events.php
require_once '../models/eventService.php';
require_once '../models/ServiceResponse.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

$eventService = new EventService();

switch($method) {
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] === 'check') {
            // GET /api/events?action=check&lat=47.151&lon=27.587
            $lat = $_GET['lat'] ?? null;
            $lon = $_GET['lon'] ?? null;
            
            if ($lat && $lon) {
                $result = $eventService->checkEventsAtLocation($lat, $lon);
                echo json_encode($result);
            } else {
               // echo ServiceResponse::error('Latitude and longitude required', 400);
            }
        } else {
            // GET /api/events
            $lat = $_GET['lat'] ?? null;
            $lon = $_GET['lon'] ?? null;
            $result = $eventService->getAllEvents($lat, $lon);
            echo json_encode($result);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action'])) {
            switch($data['action']) {
                case 'join':
                    // POST /api/events {"action": "join", "event_id": 123, "user_id": 456}
                    $result = $eventService->joinEvent($data['event_id'], $data['user_id']);
                    echo json_encode($result);
                    break;
                    
                case 'details':
                    // POST /api/events {"action": "details", "event_id": 123, "user_id": 456}
                    $result = $eventService->getEventDetails($data['event_id'], $data['user_id']);
                    echo json_encode($result);
                    break;
                    
                default:
                   // echo ServiceResponse::error('Unknown action', 400);
            }
        } else {
            // POST /api/events - create new event
            $result = $eventService->createEvent($data);
            echo json_encode($result);
        }
        break;
        
    default:
       // echo ServiceResponse::error('Method not allowed', 405);
}