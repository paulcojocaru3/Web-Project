<?php
// filepath: c:\xampp\htdocs\services\models\userService.php
require_once 'cURLservice.php';

class UserService {
    private $curlClient;
    private $baseUrl;
    
    public function __construct() {
        $this->baseUrl = 'http://localhost/Web-Project/controllers';
        $this->curlClient = new CurlClient($this->baseUrl);
    }
    
    public function authenticateUser($username, $password) {
        try {
            $data = ['username' => $username, 'password' => $password];
            
            // Apelează controller-ul tău existent pentru login
            $response = $this->curlClient->post('/loginController.php', $data);
            
            if ($response['status_code'] === 200) {
                return $response['data'];
            } else {
                return ['status' => 'error', 'message' => 'Invalid credentials'];
            }
        } catch (Exception $e) {
            error_log("UserService Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function registerUser($userData) {
        try {
            // Apelează controller-ul tău existent pentru register
            $response = $this->curlClient->post('/registerController.php', $userData);
            
            if ($response['status_code'] === 200) {
                return $response['data'];
            } else {
                return ['status' => 'error', 'message' => 'Registration failed'];
            }
        } catch (Exception $e) {
            error_log("UserService Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function getUserProfile($userId) {
        try {
            $data = ['user_id' => $userId];
            
            // Apelează controller-ul tău existent pentru profil
            $response = $this->curlClient->post('/profileController.php', $data);
            
            if ($response['status_code'] === 200) {
                return $response['data'];
            } else {
                return ['status' => 'error', 'message' => 'User not found'];
            }
        } catch (Exception $e) {
            error_log("UserService Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function checkAdminStatus($userId) {
        try {
            $data = ['user_id' => $userId];
            
            // Apelează controller-ul tău existent pentru admin check
            $response = $this->curlClient->post('/checkAdminStatus.php', $data);
            
            if ($response['status_code'] === 200) {
                return $response['data'];
            } else {
                return ['status' => 'error', 'message' => 'Check failed'];
            }
        } catch (Exception $e) {
            error_log("UserService Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}