<?php
// filepath: c:\xampp\htdocs\services\api\users.php
require_once '../models/userService.php';
require_once '../models/ServiceResponse.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$userService = new UserService();

switch($method) {
    case 'GET':
        // Adaugă suport pentru metoda GET
        if (isset($_GET['action']) && $_GET['action'] === 'check_admin') {
            $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
            if ($userId > 0) {
                $result = $userService->checkAdminStatus($userId);
                echo json_encode($result);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'User ID is required'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid action'
            ]);
        }
        break;

    case 'POST':
        // Păstrează codul POST existent
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action'])) {
            switch($data['action']) {
                case 'login':
                    // POST /api/users {"action": "login", "username": "...", "password": "..."}
                    $result = $userService->authenticateUser($data['username'], $data['password']);
                    echo json_encode($result);
                    break;
                    
                case 'register':
                    // POST /api/users {"action": "register", ...userData}
                    $result = $userService->registerUser($data);
                    echo json_encode($result);
                    break;
                    
                case 'profile':
                    // POST /api/users {"action": "profile", "user_id": 123}
                    $result = $userService->getUserProfile($data['user_id']);
                    echo json_encode($result);
                    break;
                    
                case 'check_admin':
                    $result = $userService->checkAdminStatus($data['user_id']);
                    echo json_encode($result);
                    break;
                
                default:
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Unknown action'
                    ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Action required'
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Method not allowed'
        ]);
}
?>