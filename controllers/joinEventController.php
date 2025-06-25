<?php
require_once('../models/EventModel.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

$eventModel = new EventModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_event'])) {
        $event_id = $_POST['event_id'];
        $user_id = $_SESSION['user_id'];
        
        $eventModel->registerUserForEvent($event_id, $user_id);
        header("Location: ../views/evenimente.php");
        exit();
    }
}