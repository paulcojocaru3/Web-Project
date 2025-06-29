<?php
// filepath: c:\xampp\htdocs\services\models\eventService.php
require_once 'cURLservice.php';

// Serviciu pentru gestionarea evenimentelor
class EventService {
    private $curlClient;
    
    public function __construct() {
        $this->curlClient = new CurlClient('http://localhost/Web-Project/controllers');
    }
    
    // Obtine toate evenimentele, optional filtrate dupa locatie
    public function getAllEvents($lat = null, $lon = null, $userId = null) {
        try {
            $params = [];
            if ($lat && $lon) {
                $params['lat'] = $lat;
                $params['lon'] = $lon;
            }
            if ($userId) {
                $params['user_id'] = $userId;
            }
            
            $response = $this->curlClient->get('getEventsController.php', $params);
            
            // Verifica codul de status
            if ($response['status_code'] < 200 || $response['status_code'] >= 300) {
                return [
                    'status' => 'error',
                    'message' => 'API request failed with status code ' . $response['status_code']
                ];
            }
            
            // Extrage datele din raspuns
            $responseData = $response['data'];
            
            // Formateaza raspunsul
            if (isset($responseData['status']) && $responseData['status'] === 'success') {
                return [
                    'status' => 'success',
                    'data' => [
                        'future_events' => $responseData['future_events'] ?? [],
                        'past_events' => $responseData['past_events'] ?? []
                    ]
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Unexpected response format'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Verifica daca exista evenimente in apropierea unei locatii
    public function checkEventsAtLocation($lat, $lon) {
        try {
            $response = $this->curlClient->get('checkEventsController.php', [
                'lat' => $lat,
                'lon' => $lon
            ]);
            
            if ($response['status_code'] !== 200) {
                return ['marker' => 'gray'];
            }
            
            return isset($response['data']['success']) 
                ? ['marker' => $response['data']['success'] ? 'green' : 'gray']
                : ['marker' => 'gray'];
            
        } catch (Exception $e) {
            return ['marker' => 'gray'];
        }
    }
    
    // Creeaza un eveniment nou
    public function createEvent($eventData) {
        try {
            $response = $this->curlClient->post('createEventController.php', $eventData);
            
            // Accepta toate codurile de succes (2xx)
            if ($response['status_code'] < 200 || $response['status_code'] >= 300) {
                return [
                    'status' => 'error',
                    'message' => 'API request failed with status code ' . $response['status_code']
                ];
            }
            
            return [
                'status' => $response['data']['status'] ?? 'error',
                'message' => $response['data']['message'] ?? 'Unknown error',
                'event_id' => $response['data']['event_id'] ?? null
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // Inscrie un utilizator la un eveniment
    public function joinEvent($eventId, $userId) {
        try {
            if (!$eventId || !$userId) {
                return [
                    'status' => 'error',
                    'message' => 'Event ID si User ID sunt necesare'
                ];
            }
            
            $response = $this->curlClient->post('joinEventController.php', [
                'event_id' => $eventId,
                'user_id' => $userId
            ]);
            
            // Accepta toate codurile de succes (2xx)
            if ($response['status_code'] < 200 || $response['status_code'] >= 300) {
                return [
                    'status' => 'error',
                    'message' => 'API request failed with status code ' . $response['status_code']
                ];
            }
            
            return [
                'status' => $response['data']['status'] ?? 'error',
                'message' => $response['data']['message'] ?? 'Unknown error'
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // Sterge un eveniment
    public function deleteEvent($eventId) {
        try {
            if (!$eventId) {
                return [
                    'status' => 'error',
                    'message' => 'Event ID este necesar'
                ];
            }
            
            $response = $this->curlClient->get('deleteEventController.php', [
                'event_id' => $eventId,
                'action' => 'delete'
            ]);
            
            // Accepta toate codurile de succes (2xx)
            if ($response['status_code'] < 200 || $response['status_code'] >= 300) {
                return [
                    'status' => 'error',
                    'message' => 'API request failed with status code ' . $response['status_code']
                ];
            }
            
            return [
                'status' => $response['data']['status'] ?? 'error',
                'message' => $response['data']['message'] ?? 'Unknown error'
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}