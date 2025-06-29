<?php
// Model pentru gestionarea evenimentelor
date_default_timezone_set('Europe/Bucharest');

class EventModel {
    private $db;
    private $lastError;

    // Constructor - initializeaza conexiunea la baza de date
    public function __construct() {
        $this->db = new mysqli('localhost', 'root', '', 'login_sample_db');
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    // Creeaza un eveniment nou
    public function createEventComplete($event_name, $location, $description, $event_date, 
                                      $location_lat, $location_lon, $max_participants, 
                                      $created_by, $duration, $min_events_participated = 0) {
        // Verificam daca utilizatorul exista
        if (!$this->userExists($created_by)) {
            return false;
        }
        
        // Pregatim query-ul de inserare
        $query = "INSERT INTO events (
            event_name, location, description, event_date, 
            location_lat, location_lon, lat, lng, max_participants, 
            created_by, event_type, event_description, duration, status, min_events_participated
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Sport', ?, ?, 'pending', ?)";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $this->lastError = "Eroare la prepararea statement-ului: " . $this->db->error;
            error_log($this->lastError);
            return false;
        }
        
        // Legam parametrii
        $stmt->bind_param("ssssddddiisii", 
            $event_name, $location, $description, $event_date, 
            $location_lat, $location_lon, $location_lat, $location_lon, 
            $max_participants, $created_by, $description, $duration, $min_events_participated
        );
        
        // Executam query-ul
        $result = $stmt->execute();
        
        if ($result) {
            $event_id = $this->db->insert_id;
            error_log("Eveniment creat cu succes cu ID: " . $event_id);
        } else {
            $this->lastError = "Eroare la executare query: " . $stmt->error;
            error_log($this->lastError);
        }
        
        $stmt->close();
        return $result;
    }

    // Verifica daca un utilizator exista
    private function userExists($user_id) {
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = ?");
        if (!$stmt) return false;
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = ($result->num_rows > 0);
        $stmt->close();
        return $exists;
    }

    // Verifica daca exista evenimente care se suprapun in timp si locatie
    public function checkOverlappingEvents($lat, $lon, $start_time, $end_time) {
        $query = "SELECT * FROM events WHERE (lat = ? AND lng = ?) AND status != 'expired'
                  AND (
                      (event_date <= ? AND DATE_ADD(event_date, INTERVAL duration HOUR) >= ?)
                      OR 
                      (event_date >= ? AND DATE_ADD(event_date, INTERVAL duration HOUR) <= ?)
                  )";
                  
        $stmt = $this->db->prepare($query);
        if (!$stmt) return false;
        
        $stmt->bind_param("ddssss", $lat, $lon, $end_time, $start_time, $start_time, $end_time);
        $stmt->execute();
        $result = $stmt->get_result();
        $has_overlapping = ($result->num_rows > 0);
        
        $stmt->close();
        return $has_overlapping;
    }

    // Inscrie un utilizator la un eveniment
    public function registerUserForEvent($event_id, $user_id) {
        // Obtinem detaliile evenimentului
        $eventQuery = "SELECT *, DATE_ADD(event_date, INTERVAL duration HOUR) as end_date 
                      FROM events WHERE event_id = ?";
        $stmt = $this->db->prepare($eventQuery);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $eventResult = $stmt->get_result();
        
        if ($eventResult->num_rows == 0) {
            $_SESSION['event_error'] = "Evenimentul nu exista!";
            return false;
        }
        
        $event = $eventResult->fetch_assoc();
        
        // Verificam daca utilizatorul este organizatorul
        if ($event['created_by'] == $user_id) {
            $_SESSION['event_error'] = "Nu va puteti inscrie la propriul eveniment!";
            return false;
        }
        
        // Verificam daca evenimentul a trecut deja
        $event_timestamp = strtotime($event['event_date']);
        if ($event_timestamp <= time()) {
            $_SESSION['event_error'] = "Nu va puteti inscrie la un eveniment care a trecut!";
            return false;
        }

        // Verificam cerinta de participare minima
        if ($event['min_events_participated'] > 0) {
            $userEventsQuery = "SELECT COUNT(*) as count FROM event_participants WHERE user_id = ?";
            $stmt = $this->db->prepare($userEventsQuery);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $userEventsResult = $stmt->get_result();
            $userEvents = $userEventsResult->fetch_assoc()['count'];
            
            if ($userEvents < $event['min_events_participated']) {
                $_SESSION['event_error'] = "Pentru a participa la acest eveniment, trebuie sa fi participat la minim " . 
                                        $event['min_events_participated'] . " evenimente anterioare!";
                return false;
            }
        }
        
        // Verificam conflicte de program
        $overlapQuery = "SELECT e.* FROM events e JOIN event_participants ep ON e.event_id = ep.event_id
                        WHERE ep.user_id = ? AND e.event_id != ?
                        AND (
                            (e.event_date <= ? AND DATE_ADD(e.event_date, INTERVAL e.duration HOUR) >= ?) 
                            OR 
                            (e.event_date <= ? AND DATE_ADD(e.event_date, INTERVAL e.duration HOUR) >= ?)
                        )";
        
        $stmt = $this->db->prepare($overlapQuery);
        $event_start = $event['event_date'];
        $event_end = $event['end_date'];
        $stmt->bind_param("iissss", $user_id, $event_id, $event_end, $event_start, $event_start, $event_end);
        $stmt->execute();
        $overlapResult = $stmt->get_result();
        
        if ($overlapResult->num_rows > 0) {
            $conflicting_event = $overlapResult->fetch_assoc();
            $_SESSION['event_error'] = "Nu va puteti inscrie la acest eveniment deoarece participati deja la evenimentul '" . 
                                     htmlspecialchars($conflicting_event['event_name']) . 
                                     "' in acelasi interval orar.";
            return false;
        }
        
        // Verificam daca evenimentul e plin
        $participantsQuery = "SELECT COUNT(*) as count FROM event_participants WHERE event_id = ?";
        $stmt = $this->db->prepare($participantsQuery);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $participantsResult = $stmt->get_result();
        $participantsCount = $participantsResult->fetch_assoc()['count'];
        
        if ($event['max_participants'] > 0 && $participantsCount >= $event['max_participants']) {
            $_SESSION['event_error'] = "Evenimentul a atins numarul maxim de participanti!";
            return false;
        }
        
        // Verificam daca utilizatorul e deja inscris
        $checkQuery = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($checkQuery);
        $stmt->bind_param("ii", $event_id, $user_id);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $_SESSION['event_error'] = "Sunteti deja inscris la acest eveniment!";
            return false;
        }
        
        // Inscriem utilizatorul
        $insertQuery = "INSERT INTO event_participants (event_id, user_id, registration_date, status) 
                        VALUES (?, ?, NOW(), 'registered')";
        $stmt = $this->db->prepare($insertQuery);
        $stmt->bind_param("ii", $event_id, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['event_success'] = "V-ati inscris cu succes la eveniment!";
            return true;
        } else {
            $_SESSION['event_error'] = "A aparut o eroare la inscriere!";
            return false;
        }
    }
    
    // Verifica daca exista evenimente la o anumita locatie
    public function checkEventAtLocation($lat, $lon) {
        $query = "SELECT event_id FROM events 
                 WHERE ((lat = ? AND lng = ?) OR (location_lat = ? AND location_lon = ?)) 
                 AND (status != 'expired')";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) return false;
        
        $stmt->bind_param("dddd", $lat, $lon, $lat, $lon);
        $stmt->execute();
        $result = $stmt->get_result();
        $has_events = ($result->num_rows > 0);
        
        $stmt->close();
        return $has_events;
    }
    
    // Actualizeaza statusurile evenimentelor (expirate/in desfasurare)
    public function checkEventStatus() {
        $query = "SELECT event_id, event_date, duration FROM events WHERE status != 'expired'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $current_timestamp = time();
        
        while ($event = $result->fetch_assoc()) {
            $event_start = strtotime($event['event_date']);
            $event_end = $event_start + ($event['duration'] * 3600);
            
            // Marcam evenimentele trecute ca expirate
            if ($current_timestamp > $event_end) {
                $update = "UPDATE events SET status = 'expired' WHERE event_id = ?";
                $update_stmt = $this->db->prepare($update);
                $update_stmt->bind_param("i", $event['event_id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
            // Marcam evenimentele in desfasurare
            else if ($current_timestamp >= $event_start && $current_timestamp <= $event_end) {
                $update = "UPDATE events SET status = 'running' WHERE event_id = ?";
                $update_stmt = $this->db->prepare($update);
                $update_stmt->bind_param("i", $event['event_id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
        
        $stmt->close();
    }

    // Obtine toate evenimentele
    public function getAllEvents() {
        $query = "SELECT * FROM events ORDER BY event_date DESC";

        $result = $this->db->query($query);
        if (!$result) {
            error_log("Eroare baza de date: " . $this->db->error);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Obtine evenimente grupate pe viitoare si trecute, cu detalii suplimentare
    public function getEvenimente($lat = null, $lon = null, $user_id = null) {
        $future_events = [];
        $past_events = [];
        
        // Pregatim query-ul
        $query = "SELECT * FROM events ";
        $params = [];
        $types = "";
        
        // Filtram dupa locatie daca e specificata
        if ($lat !== null && $lon !== null) {
            $query .= "WHERE (lat = ? AND lng = ?) ";
            $params = [$lat, $lon];
            $types = "dd";
        }
        
        $query .= "ORDER BY event_date DESC";
        
        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $events_result = $stmt->get_result();
        
        // Procesam rezultatele
        $current_timestamp = time();
        
        while ($row = $events_result->fetch_assoc()) {
            $eventId = $row['event_id'];
            $event_start = strtotime($row['event_date']);
            $event_end = $event_start + ($row['duration'] * 3600);
            
            // Adaugam flaguri pentru starea evenimentului
            $row['is_past'] = ($current_timestamp > $event_end);
            $row['is_running'] = ($current_timestamp >= $event_start && $current_timestamp <= $event_end);
            
            // Obtinem numarul de participanti
            $participantsQuery = "SELECT COUNT(*) as count FROM event_participants WHERE event_id = ?";
            $stmt2 = $this->db->prepare($participantsQuery);
            $stmt2->bind_param("i", $eventId);
            $stmt2->execute();
            $participantsResult = $stmt2->get_result();
            
            $row['current_participants'] = $participantsResult->fetch_assoc()['count'] ?? 0;
            
            // Verificam daca utilizatorul curent e inscris
            $current_user_id = $user_id ?? ($_SESSION['user_id'] ?? null);
            
            if ($current_user_id) {
                $isRegisteredQuery = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
                $stmt3 = $this->db->prepare($isRegisteredQuery);
                $stmt3->bind_param("ii", $eventId, $current_user_id);
                $stmt3->execute();
                $isRegisteredResult = $stmt3->get_result();
                $row['is_registered'] = ($isRegisteredResult->num_rows > 0);
            } else {
                $row['is_registered'] = false;
            }
            
            // Verificam daca evenimentul e plin
            $row['is_full'] = false;
            if ($row['max_participants'] > 0 && $row['current_participants'] >= $row['max_participants']) {
                $row['is_full'] = true;
            }
            
            // Adaugam in lista corespunzatoare
            if ($row['is_past']) {
                $past_events[] = $row;
            } else {
                $future_events[] = $row;
            }
        }
        
        return [
            'future_events' => $future_events,
            'past_events' => $past_events
        ];
    }
    
    // Sterge un eveniment si toate informatiile asociate
    public function deleteEvent($event_id) {
        // Stergem mesajele din chat
        $delete_chat = "DELETE FROM event_chat WHERE event_id = ?";
        $stmt = $this->db->prepare($delete_chat);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        
        // Stergem participantii
        $delete_participants = "DELETE FROM event_participants WHERE event_id = ?";
        $stmt = $this->db->prepare($delete_participants);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        
        // Stergem evenimentul
        $delete_event = "DELETE FROM events WHERE event_id = ?";
        $stmt = $this->db->prepare($delete_event);
        $stmt->bind_param("i", $event_id);
        
        return $stmt->execute();
    }

    // Dezaboneaza un utilizator de la un eveniment
    public function unregisterUser($event_id, $user_id) {
        // Verificam daca utilizatorul e inscris
        $check_query = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($check_query);
        $stmt->bind_param("ii", $event_id, $user_id);
        $stmt->execute();
        $check_result = $stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            $_SESSION['error'] = "Nu sunteti inscris la acest eveniment!";
            return false;
        }
        
        // Stergem inregistrarea
        $delete_query = "DELETE FROM event_participants WHERE event_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($delete_query);
        $stmt->bind_param("ii", $event_id, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "V-ati dezabonat de la eveniment!";
            return true;
        } else {
            $_SESSION['error'] = "A aparut o eroare la dezabonare!";
            return false;
        }
    }

    // Obtine detaliile unui eveniment, inclusiv participanti si mesaje
    public function getEventDetails($event_id, $user_id) {
        $event_query = "SELECT *, CASE WHEN DATE_ADD(event_date, INTERVAL duration HOUR) < NOW() 
                        THEN 'expired' ELSE status END as current_status 
                        FROM events WHERE event_id = ?";
        $stmt = $this->db->prepare($event_query);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $event_result = $stmt->get_result();
        
        if ($event_result->num_rows == 0) {
            return null;
        }
        
        $event = $event_result->fetch_assoc();
        
        // Verificam permisiunile si statusurile
        $is_creator = ($event['created_by'] == $user_id);
        $event['can_edit'] = $is_creator;
        $is_past = (strtotime($event['event_date']) < time());
        
        // Verificam daca utilizatorul e inscris
        $is_registered_query = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($is_registered_query);
        $stmt->bind_param("ii", $event_id, $user_id);
        $stmt->execute();
        $is_registered_result = $stmt->get_result();
        $is_registered = ($is_registered_result->num_rows > 0);
        
        // Obtinem participantii
        $participants_query = "SELECT ep.*, u.username FROM event_participants ep 
                              JOIN users u ON ep.user_id = u.user_id 
                              WHERE ep.event_id = ? ORDER BY ep.registration_date ASC";
        $stmt = $this->db->prepare($participants_query);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $participants_result = $stmt->get_result();
        
        $participants = [];
        while ($row = $participants_result->fetch_assoc()) {
            $participants[] = $row;
        }
        
        // Verificam daca evenimentul e plin
        $is_full = false;
        if ($event['max_participants'] > 0 && count($participants) >= $event['max_participants']) {
            $is_full = true;
        }
        
        // Obtinem mesajele de chat
        $chat_query = "SELECT ec.*, u.username FROM event_chat ec 
                      JOIN users u ON ec.user_id = u.user_id 
                      WHERE ec.event_id = ? ORDER BY ec.sent_at ASC";
        $stmt = $this->db->prepare($chat_query);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $chat_result = $stmt->get_result();
        
        $chat_messages = [];
        while ($row = $chat_result->fetch_assoc()) {
            $chat_messages[] = $row;
        }
        
        // Actualizam statusul daca e expirat
        if ($event['current_status'] === 'expired' && $event['status'] !== 'expired') {
            $update = "UPDATE events SET status = 'expired' WHERE event_id = ?";
            $stmt = $this->db->prepare($update);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
        }
        
        return [
            'event' => $event,
            'is_past' => $is_past,
            'is_registered' => $is_registered,
            'is_creator' => $is_creator,
            'participants' => $participants,
            'is_full' => $is_full,
            'chat_messages' => $chat_messages
        ];
    }

    // Trimite un mesaj in chat-ul evenimentului
    public function sendMessage($event_id, $user_id, $message) {
        // Verificam daca utilizatorul e organizatorul
        $is_creator_query = "SELECT created_by FROM events WHERE event_id = ? AND created_by = ?";
        $stmt = $this->db->prepare($is_creator_query);
        $stmt->bind_param("ii", $event_id, $user_id);
        $stmt->execute();
        $is_creator_result = $stmt->get_result();
        $is_creator = ($is_creator_result->num_rows > 0);

        // Daca nu e organizatorul, verificam daca e inscris
        if (!$is_creator) {
            $is_registered_query = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($is_registered_query);
            $stmt->bind_param("ii", $event_id, $user_id);
            $stmt->execute();
            $is_registered_result = $stmt->get_result();
            
            if ($is_registered_result->num_rows == 0) {
                $_SESSION['error'] = "Trebuie sa fiti inscris la eveniment pentru a trimite mesaje!";
                return false;
            }
        }

        // Validam mesajul
        $message = trim($message);
        if (empty($message)) {
            return false;
        }
        
        // Inseram mesajul
        $insert_message_query = "INSERT INTO event_chat (event_id, user_id, message, sent_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($insert_message_query);
        $stmt->bind_param("iis", $event_id, $user_id, $message);
        
        if ($stmt->execute()) {
            return true;
        } else {
            $_SESSION['error'] = "A aparut o eroare la trimiterea mesajului!";
            return false;
        }
    }
    
    // Obtine evenimentele viitoare
    public function getUpcomingEventsForUser() {
        $query = "SELECT e.*, COUNT(ep.user_id) as current_participants, 
                         u.username as organizer_name
                  FROM events e 
                  LEFT JOIN event_participants ep ON e.event_id = ep.event_id
                  LEFT JOIN users u ON e.created_by = u.user_id 
                  WHERE e.event_date > NOW() AND e.status != 'expired'
                  GROUP BY e.event_id 
                  ORDER BY e.event_date ASC 
                  LIMIT 10";
                  
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            error_log("Eroare la prepararea statement-ului: " . $this->db->error);
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Inchide conexiunea la baza de date
    public function closeConnection() {
        if ($this->db) {
            $this->db->close();
        }
    }
    
    // Destructor - inchide conexiunea automat
    public function __destruct() {
        $this->closeConnection();
    }
}
?>