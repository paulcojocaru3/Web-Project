<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli('localhost', 'root', '', 'login_sample_db');
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

   public function getUserByCredentials($username, $password) {
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    else if($user && $password === $user['password']) {
        return $user;
    }
    return null;
}

   public function createUser($username, $password, $email, $firstname, $lastname, $birth_date, $gender) {
    $query = "INSERT INTO users (username, password, email, firstname, lastname, birth_date, gender) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("sssssss", 
        $username, 
        $password, 
        $email, 
        $firstname, 
        $lastname, 
        $birth_date, 
        $gender
    );
    
    return $stmt->execute();
}

public function checkIfEmailExists($email) {
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
// public function getUserById($id) {
//     $query = "SELECT id, username, created_at FROM users WHERE id = ?";
//     $stmt = mysqli_prepare($this->db, $query);
//     mysqli_stmt_bind_param($stmt, "i", $id);
//     mysqli_stmt_execute($stmt);
//     return mysqli_stmt_get_result($stmt)->fetch_assoc();
// }

    public function checkIfUserExists($username) {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0; 
    }

    public function getUserById($user_id) {
        $query = "SELECT user_id, username, email, firstname, lastname, events_participated, date as created_at, role, birth_date, gender FROM users WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function updateUserInfo($user_id, $first_name, $last_name, $email) {
        $query = "UPDATE users SET firstname = ?, lastname = ?, email = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssi', $first_name, $last_name, $email, $user_id);
        return $stmt->execute();
    }

    public function isAdmin($userId) {
        $query = "SELECT role FROM users WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['role'] === 'admin';
        }
        return false;
    }
}
?>