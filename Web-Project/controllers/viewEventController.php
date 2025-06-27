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
    $event_id = $_GET['id'] ?? 0;
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['register_event'])) {
        $eventModel->registerUserForEvent($event_id, $user_id);
        header("Location: ../views/view_event.php?id=$event_id");
        exit();
    } 
    elseif (isset($_POST['unregister_event'])) {
        $eventModel->unregisterUser($event_id, $user_id);
        header("Location: ../views/view_event.php?id=$event_id");
        exit();
    }
    elseif (isset($_POST['send_message']) && !empty($_POST['message'])) {
        $eventModel->sendMessage($event_id, $user_id, $_POST['message']);
        header("Location: ../views/view_event.php?id=$event_id#chat");
        exit();
    }
}

