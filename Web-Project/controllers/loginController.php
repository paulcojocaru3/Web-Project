<?php 
session_start();
include("../models/UserModel.php");

// Accept doar cereri POST
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    http_response_code(405); // Method Not Allowed
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Metoda HTTP nepermisa']);
    exit();
}

// Setez header pentru raspuns JSON
header('Content-Type: application/json');

// Preiau datele din cererea JSON
$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

// Validez datele de intrare
if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Toate campurile sunt obligatorii!']);
    exit();
}

// Incerc sa autentific utilizatorul
$userModel = new UserModel();
$user = $userModel->getUserByCredentials($username, $password);

if ($user) {
    // Salvez ID-ul utilizatorului in sesiune pentru a-l tine autentificat
    $_SESSION['user_id'] = $user['user_id'];
    
    // Returnez raspuns de succes
    echo json_encode([
        'status' => 'success', 
        'message' => 'Autentificare reusita', 
        'user_id' => $user['user_id'],
        'username' => $user['username']
    ]);
} else {
    // Returnez eroare daca autentificarea esueaza
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Username sau parola incorecte!']);
}
?>