<?php
session_start();
require_once '../models/UserModel.php';

header('Content-Type: application/json');

// Verifică dacă există user_id în request (pentru apeluri API)
$userId = null;

// Prioritizează user_id din session pentru cereri web normale
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} 
// Acceptă user_id din GET pentru API-uri
else if (isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
}

if (!$userId) {
    echo json_encode(['isAdmin' => false]);
    exit();
}

$userModel = new UserModel();
$isAdmin = $userModel->isAdmin($userId);

echo json_encode(['isAdmin' => $isAdmin]);
?>