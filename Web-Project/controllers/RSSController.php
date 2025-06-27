<?php
require_once('../models/EventModel.php');
require_once('../models/RSSModel.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

$eventModel = new EventModel();
$events = $eventModel->getUpcomingEventsForUser();

$rssProcessor = new RSSModel();
$rssContent = $rssProcessor->processEventsToRSS($events);

header('Content-Type: application/xml; charset=utf-8');
echo $rssContent;
?>