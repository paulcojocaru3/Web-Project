<?php
// filepath: c:\xampp\htdocs\services\models\eventService.php
require_once 'cURLservice.php';

class EventService {
    private $curlClient;
    private $baseUrl;
    
    public function __construct() {
        $this->baseUrl = 'http://localhost/Web-Project/controllers';
        $this->curlClient = new CurlClient($this->baseUrl);
    }
    
    public function getAllEvents($lat = null, $lon = null) {
        try {
            $data = [];
            if ($lat && $lon) {
                $data = ['lat' => $lat, 'lon' => $lon];
            }
            
            // Apelează controller-ul tău existent
            $response = $this->curlClient->post('/getEventsController.php', $data);
            
            if ($response['status_code'] === 200) {
                return $response['data'];
            } else {
                throw new Exception('Failed to fetch events');
            }
        } catch (Exception $e) {
            error_log("EventService Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function createEvent($eventData) {
        try {
            // Apelează controller-ul tău existent pentru creare
            $response = $this->curlClient->post('/createEventController.php', $eventData);
            
            if ($response['status_code'] === 200) {
                return $response['data'];
            } else {
                throw new Exception('Failed to create event');
            }
        } catch (Exception $e) {
            error_log("EventService Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function joinEvent($eventId, $userId) {
        try {
            $data = ['event_id' => $eventId, 'user_id' => $userId];
            
            // Apelează controller-ul tău existent pentru join
            $response = $this->curlClient->post('/joinEventController.php', $data);
            
            if ($response['status_code'] === 200) {
                return $response['data'];
            } else {
                throw new Exception('Failed to join event');
            }
        } catch (Exception $e) {
            error_log("EventService Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function getEventDetails($eventId, $userId) {
        try {
            $data = ['event_id' => $eventId, 'user_id' => $userId];
            
            // Apelează controller-ul tău existent pentru detalii
            $response = $this->curlClient->post('/viewEventController.php', $data);
            
            if ($response['status_code'] === 200) {
                return $response['data'];
            } else {
                throw new Exception('Failed to get event details');
            }
        } catch (Exception $e) {
            error_log("EventService Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function checkEventsAtLocation($lat, $lon) {
        try {
            // Apelează controller-ul tău existent pentru verificare
            $response = $this->curlClient->get("/checkEventsController.php?lat={$lat}&lon={$lon}");
            
            if ($response['status_code'] === 200) {
                return $response['data'];
            } else {
                throw new Exception('Failed to check events');
            }
        } catch (Exception $e) {
            error_log("EventService Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}