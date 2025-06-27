<?php
session_start();
require_once '../models/UserModel.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['isAdmin' => false]);
    exit();
}

$userModel = new UserModel();
$isAdmin = $userModel->isAdmin($_SESSION['user_id']);

echo json_encode(['isAdmin' => $isAdmin]);
?>