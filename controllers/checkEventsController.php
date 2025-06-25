<?php
session_start();
header('Content-Type: application/json');
require_once('../models/EventModel.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];

    $eventModel = new EventModel();
    $eventModel->checkEventStatus();
    $result = $eventModel->checkEventAtLocation($lat, $lon);

    if ($result) {
        echo json_encode(['success' => true, 'marker' => 'green']);
    } else {
        echo json_encode(['success' => false, 'marker' => 'gray']);
    }
}
?>