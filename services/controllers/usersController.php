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
    case 'POST':
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
                    // POST /api/users {"action": "check_admin", "user_id": 123}
                    $result = $userService->checkAdminStatus($data['user_id']);
                    echo json_encode($result);
                    break;
                    
                default:
                  //  echo ServiceResponse::error('Unknown action', 400);
            }
        } else {
          //  echo ServiceResponse::error('Action required', 400);
        }
        break;
        
    default:
      //  echo ServiceResponse::error('Method not allowed', 405);
}