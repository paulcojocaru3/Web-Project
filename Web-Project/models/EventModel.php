<?php
date_default_timezone_set('Europe/Bucharest');
class EventModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli('localhost', 'root', '', 'login_sample_db');
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    // createEventController.php
    public function createEventComplete($event_name, $location, $description, $event_date, $location_lat, $location_lon, $max_participants, $created_by, $duration, $min_events_participated = 0) {

    if ($this->db->connect_error) {
        $this->lastError = "Eroare conexiune la baza de date: " . $this->db->connect_error;
        error_log($this->lastError);
        return false;
    }
    
    $checkUserQuery = "SELECT user_id FROM users WHERE user_id = ?";
    $stmt = $this->db->prepare($checkUserQuery);
    if (!$stmt) {
        $this->lastError = "Eroare prepare statement pentru verificare utilizator: " . $this->db->error;
        error_log($this->lastError);
        return false;
    }
    
    $stmt->bind_param("i", $created_by);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $this->lastError = "Utilizatorul cu ID-ul $created_by nu există!";
        error_log($this->lastError);
        $stmt->close();
        return false;
    }
    $stmt->close();
    
    $structureQuery = "DESCRIBE events";
    $structureResult = $this->db->query($structureQuery);
    if (!$structureResult) {
        $this->lastError = "Eroare la verificarea structurii tabelului events: " . $this->db->error;
        error_log($this->lastError);
        return false;
    }
    
    $columns = [];
    while ($row = $structureResult->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    error_log("Coloane în tabelul events: " . implode(", ", $columns));
    
    $query = "INSERT INTO events (
        event_name, 
        location, 
        description, 
        event_date, 
        location_lat, 
        location_lon, 
        lat, 
        lng, 
        max_participants, 
        created_by,
        event_type,
        event_description,
        duration,
        status,
        min_events_participated
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Sport', ?, ?, 'pending', ?)";
    
    error_log("Query SQL: " . $query);
    
    $stmt = $this->db->prepare($query);
    if (!$stmt) {
        $this->lastError = "Eroare prepare statement: " . $this->db->error;
        error_log($this->lastError);
        return false;
    }
    
    $stmt->bind_param("ssssddddiisii", 
        $event_name, 
        $location, 
        $description, 
        $event_date, 
        $location_lat, 
        $location_lon,
        $location_lat,  
        $location_lon,  
        $max_participants, 
        $created_by,
        $description,
        $duration,
        $min_events_participated
    );
    
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
    public function checkOverlappingEvents($lat, $lon, $start_time, $end_time) {
    $query = "SELECT * FROM events WHERE (lat = ? AND lng = ?) AND status != 'expired'
              AND (
                  (event_date <= ? AND DATE_ADD(event_date, INTERVAL duration HOUR) >= ?)
                  OR 
                  (event_date >= ? AND DATE_ADD(event_date, INTERVAL duration HOUR) <= ?)
              )";
              
    $stmt = $this->db->prepare($query);
    if (!$stmt) {
        $this->lastError = "Eroare" . $this->db->error;
        error_log($this->lastError);
        return false;
    }
    
    $stmt->bind_param("ddssss",$lat, 
     $lon,       
     $end_time,  
     $start_time, 
     $start_time, 
     $end_time
    );
    
    $stmt->execute();
    $result = $stmt->get_result();
    $has_overlapping = ($result->num_rows > 0);
    
    if ($has_overlapping) {
        error_log("found overlapping");
    }
    
    $stmt->close();
    return $has_overlapping;
}

// registerEventController.php
    public function registerUserForEvent($event_id, $user_id) {
        $eventQuery = "SELECT *, 
                   DATE_ADD(event_date, INTERVAL duration HOUR) as end_date 
                   FROM events WHERE event_id = ?";
    $stmt = $this->db->prepare($eventQuery);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $eventResult = $stmt->get_result();
    
    if ($eventResult->num_rows > 0) {
        $event = $eventResult->fetch_assoc();
        
        if ($event['created_by'] == $user_id) {
            $_SESSION['event_error'] = "Nu va puteti inscrie la propriul eveniment!";
            return false;
        }
        
        $event_timestamp = strtotime($event['event_date']);
        if ($event_timestamp <= time()) {
            $_SESSION['event_error'] = "Nu va puteti înscrie la un eveniment care a trecut!";
            return false;
        }

        if ($event['min_events_participated'] > 0) {
            $userEventsQuery = "SELECT COUNT(*) as count FROM event_participants WHERE user_id = ?";
            $stmt = $this->db->prepare($userEventsQuery);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $userEventsResult = $stmt->get_result();
            $userEvents = $userEventsResult->fetch_assoc()['count'];
            
            if ($userEvents < $event['min_events_participated']) {
                $_SESSION['event_error'] = "Pentru a participa la acest eveniment, trebuie să fi participat la minim " . $event['min_events_participated'] . " evenimente anterioare!";
                return false;
            }
        }
        
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
        $stmt->bind_param("iissss", 
            $user_id, 
            $event_id, 
            $event_end, 
            $event_start, 
            $event_start, 
            $event_end
        );
        $stmt->execute();
        $overlapResult = $stmt->get_result();
        
        if ($overlapResult->num_rows > 0) {
            $conflicting_event = $overlapResult->fetch_assoc();
            $_SESSION['event_error'] = "Nu vă puteți înscrie la acest eveniment deoarece participați deja la evenimentul '" . 
                                     htmlspecialchars($conflicting_event['event_name']) . 
                                     "' în același interval orar.";
            return false;
        }
            
            $participantsQuery = "SELECT COUNT(*) as count FROM event_participants WHERE event_id = $event_id";
            $participantsResult = $this->db->query($participantsQuery);
            $participantsCount = $participantsResult->fetch_assoc()['count'];
            
            if ($event['max_participants'] > 0 && $participantsCount >= $event['max_participants']) {
                $_SESSION['event_error'] = "Evenimentul a atins numarul maxim de participanti!";
                return false;
            }
            
            $checkQuery = "SELECT * FROM event_participants WHERE event_id = $event_id AND user_id = $user_id";
            $checkResult = $this->db->query($checkQuery);
            
            if ($checkResult->num_rows == 0) {
                $insertQuery = "INSERT INTO event_participants (event_id, user_id, registration_date, status) VALUES ($event_id, $user_id, NOW(), 'registered')";
                if ($this->db->query($insertQuery)) {
                    $_SESSION['event_success'] = "V-ati inscris cu succes la eveniment!";
                    return true;
                } else {
                    $_SESSION['event_error'] = "A aparut o eroare la inscriere!";
                    return false;
                }
            } else {
                $_SESSION['event_error'] = "Sunteti deja inscris la acest eveniment!";
                return false;
            }
        } else {
            $_SESSION['event_error'] = "Evenimentul nu exista!";
            return false;
        }
    }
    
   // map.js checks
    public function checkEventAtLocation($lat, $lon) {
    $query = "SELECT event_id FROM events 
          WHERE ((lat = ? AND lng = ?) OR (location_lat = ? AND location_lon = ?)) 
          AND (status != 'expired')";
          
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            error_log("Eroare prepare statement in checkEventAtLocation: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param("dddd", $lat, $lon, $lat, $lon);
        $stmt->execute();
        $result = $stmt->get_result();
        $has_events = $result->num_rows > 0;
        
        $stmt->close();
        return $has_events;
    }
    

    public function checkEventStatus() {
        $ok = 0;
        $query = "SELECT event_id, event_date, duration, created_by FROM events";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $updated_count = 0;
        
        while ($event = $result->fetch_assoc()) {
            $end_time = strtotime($event['event_date']) + ($event['duration'] * 3600);
            if (time() > $end_time) {
                $update = "UPDATE events SET status = 'expired' WHERE event_id = ?";
                $update_stmt = $this->db->prepare($update);
                $update_stmt->bind_param("i", $event['event_id']);
                $update_stmt->execute();
                $updated_count++;
                $update_stmt->close();
                $ok = 1;
            }
        $current_timestamp = time();
        $event_start = strtotime($event['event_date']);
        $event_end = $event_start + ($event['duration'] * 3600);
        $is_running = ($current_timestamp >= $event_start && $current_timestamp <= $event_end);
        if( $is_running ) {
            $update = "UPDATE events SET status = 'running' WHERE event_id = ?";
            $update_stmt = $this->db->prepare($update);
            $update_stmt->bind_param("i", $event['event_id']);
            $update_stmt->execute();
            $updated_count++;
            $update_stmt->close();
        }
    }
    $stmt->close();

}
// show_events

public function getAllEvents() {
    $query = "SELECT 
        event_id,
        event_name,
        location,
        description,
        event_date,
        location_lat,
        location_lon,
        max_participants,
        created_by,
        status,
        created_at,
        min_age,
        required_gender,
        min_events_participated,
        event_type,
        event_description,
        lat,
        lng,
        participation_policy,
        min_participations,
        duration
    FROM events 
    ORDER BY event_date DESC";

    try {
        $result = $this->db->query($query);
        
        if (!$result) {
            error_log("Database error: " . $this->db->error);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching events: " . $e->getMessage());
        return [];
    }
}

public function getEvenimente($lat = null, $lon = null) {
    $future_events = [];
    $past_events = [];
    
    $query = "SELECT * FROM events ";
    $params = [];
    $types = "";
    
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
    
    $current_timestamp = time();
    
    while ($row = $events_result->fetch_assoc()) {
        $eventId = $row['event_id'];
        $event_start = strtotime($row['event_date']);
        $event_end = $event_start + ($row['duration'] * 3600);
        $row['is_past'] = ($current_timestamp > $event_end);
        $row['is_running'] = ($current_timestamp >= $event_start && $current_timestamp <= $event_end);
        $participantsQuery = "SELECT COUNT(*) as count FROM event_participants WHERE event_id = ?";
        $stmt2 = $this->db->prepare($participantsQuery);
        $stmt2->bind_param("i", $eventId);
        $stmt2->execute();
        $participantsResult = $stmt2->get_result();
        
        if ($participantsResult) {
            $row['current_participants'] = $participantsResult->fetch_assoc()['count'];
        } else {
            $row['current_participants'] = 0;
        }
        
        $user_id = $_SESSION['user_id'];
        $isRegisteredQuery = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
        $stmt3 = $this->db->prepare($isRegisteredQuery);
        $stmt3->bind_param("ii", $eventId, $user_id);
        $stmt3->execute();
        $isRegisteredResult = $stmt3->get_result();
        $row['is_registered'] = ($isRegisteredResult->num_rows > 0);
        
        $row['is_full'] = false;
        if ($row['max_participants'] > 0 && $row['current_participants'] >= $row['max_participants']) {
            $row['is_full'] = true;
        }
        
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
    
    
    public function deleteEvent($event_id) {
        // Șterg mai întâi mesajele din chat
        $delete_chat = "DELETE FROM event_chat WHERE event_id = ?";
        $stmt1 = $this->db->prepare($delete_chat);
        $stmt1->bind_param("i", $event_id);
        $stmt1->execute();
        
        // Șterg participanții
        $delete_participants = "DELETE FROM event_participants WHERE event_id = ?";
        $stmt2 = $this->db->prepare($delete_participants);
        $stmt2->bind_param("i", $event_id);
        $stmt2->execute();
        
        // Șterg evenimentul
        $delete_event = "DELETE FROM events WHERE event_id = ?";
        $stmt3 = $this->db->prepare($delete_event);
        $stmt3->bind_param("i", $event_id);
        
        return $stmt3->execute();
    }


    // viewEventController.php
    
    public function unregisterUser($event_id, $user_id) {
        $check_query = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($check_query);
        $stmt->bind_param("ii", $event_id, $user_id);
        $stmt->execute();
        $check_result = $stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            $_SESSION['error'] = "Nu sunteti inscris la acest eveniment!";
            return false;
        }
        
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

     public function getEventDetails($event_id, $user_id) {
        $event_query = "SELECT *,  CASE WHEN DATE_ADD(event_date, INTERVAL duration HOUR) < NOW()  THEN 'expired' 
                        ELSE status END as current_status FROM events WHERE event_id = ?";
        $stmt = $this->db->prepare($event_query);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $event_result = $stmt->get_result();
        
        if ($event_result->num_rows == 0) {
            return null;
        }
        
        $event = $event_result->fetch_assoc();
        $is_creator = ($event['created_by'] == $user_id);  
        $event['can_edit'] = $is_creator;  
        $is_past = (strtotime($event['event_date']) < time());
        
        $is_registered_query = "SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($is_registered_query);
        $stmt->bind_param("ii", $event_id, $user_id);
        $stmt->execute();
        $is_registered_result = $stmt->get_result();
        $is_registered = ($is_registered_result->num_rows > 0);
        
        $participants_query = "SELECT ep.*, u.username FROM event_participants ep 
         JOIN users u ON ep.user_id = u.user_id  WHERE ep.event_id = ?  ORDER BY ep.registration_date ASC";
        $stmt = $this->db->prepare($participants_query);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $participants_result = $stmt->get_result();
        
        $participants = [];
        while ($row = $participants_result->fetch_assoc()) {
            $participants[] = $row;
        }
        
        $is_full = false;
        if ($event['max_participants'] > 0 && count($participants) >= $event['max_participants']) {
            $is_full = true;
        }
        
        $chat_query = "SELECT ec.*, u.username  FROM event_chat ec  JOIN users u ON ec.user_id = u.user_id 
                      WHERE ec.event_id = ? ORDER BY ec.sent_at ASC";
        $stmt = $this->db->prepare($chat_query);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $chat_result = $stmt->get_result();
        
        $chat_messages = [];
        while ($row = $chat_result->fetch_assoc()) {
            $chat_messages[] = $row;
        }
        
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

    public function sendMessage($event_id, $user_id, $message) {
        $is_creator_query = "SELECT created_by FROM events WHERE event_id = ? AND created_by = ?";
        $stmt = $this->db->prepare($is_creator_query);
        $stmt->bind_param("ii", $event_id, $user_id);
        $stmt->execute();
        $is_creator_result = $stmt->get_result();
        $is_creator = ($is_creator_result->num_rows > 0);

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

        $message = trim($message);
        if (empty($message)) {
            return false;
        }
        
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
    
    public function getUpcomingEventsForUser() {
    $query = "SELECT e.*, 
                     COUNT(ep.user_id) as current_participants, e.created_at, u.username as organizer_name
              FROM events e LEFT JOIN event_participants ep ON e.event_id = ep.event_id
              LEFT JOIN users u ON e.created_by = u.user_id WHERE e.event_date > NOW() AND e.status != 'expired'
              GROUP BY e.event_id ORDER BY e.event_date ASC LIMIT 10";
              
    $stmt = $this->db->prepare($query);
    if (!$stmt) {
        error_log("Error preparing statement: " . $this->db->error);
        return [];
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        error_log("Error getting results: " . $this->db->error);
        return [];
    }
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

 public function closeConnection() {
        if ($this->db) {
            $this->db->close();
        }
    }
    
    public function __destruct() {
        $this->closeConnection();
    }
}
?>