<?php
class ProfileModel {
    private $db;
    private $lastError = '';
    
    public function __construct() {
        $this->db = new mysqli('localhost', 'root', '', 'login_sample_db');
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }
    
    // Get last error message
    public function getLastError() {
        return $this->lastError;
    }
    
    // Get events that user has participated in
    public function getUserParticipatedEvents($user_id) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if ($user_id === false) {
            $this->lastError = "Invalid user ID";
            error_log($this->lastError);
            return [];
        }
        
        $query = "SELECT e.event_id, e.event_name, e.event_date, e.event_type, e.location, ep.registration_date
                  FROM events e 
                  JOIN event_participants ep ON e.event_id = ep.event_id 
                  WHERE ep.user_id = ? 
                  ORDER BY e.event_date DESC";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $this->lastError = "Error preparing statement in getUserParticipatedEvents: " . $this->db->error;
            error_log($this->lastError);
            return [];
        }
        
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        
        return $events;
    }
    
    // Get events created by user
    public function getUserCreatedEvents($user_id) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if ($user_id === false) {
            $this->lastError = "Invalid user ID";
            error_log($this->lastError);
            return [];
        }
        
        $query = "SELECT e.event_id, e.event_name, e.event_date, e.event_type, e.location, e.event_date as creation_date
                  FROM events e 
                  WHERE e.created_by = ? 
                  ORDER BY e.event_date DESC";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $this->lastError = "Error preparing statement in getUserCreatedEvents: " . $this->db->error;
            error_log($this->lastError);
            return [];
        }
        
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        
        return $events;
    }
}
?>