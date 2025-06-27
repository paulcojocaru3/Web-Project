<?php
include_once '../models/UserModel.php';
include_once '../models/ProfileModel.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

class ProfileController {
    private $userModel;
    private $profileModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->profileModel = new ProfileModel();
    }
    
    public function updateProfile() {
        $user_id = $_SESSION['user_id'];
        $first_name = trim(htmlspecialchars($_POST['first_name']));
        $last_name = trim(htmlspecialchars($_POST['last_name']));
        $email = trim(htmlspecialchars($_POST['email']));
        
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Adresa de email nu este valida!";
            header("Location: ../views/edit_profile.php");
            exit();
        }
        
        $this->userModel->updateUserInfo($user_id, $first_name, $last_name, $email);
        
        $_SESSION['success'] = "Profilul a fost actualizat cu succes!";
        header("Location: ../views/profile.php");
        exit();
    }
    
    public function getProfileData($user_id) {
        $user = $this->userModel->getUserById($user_id);
        
        return [
            'user' => $user,
            'profile' => []
        ];
    }
    
    public function getUserEvents($user_id) {
        return $this->profileModel->getUserParticipatedEvents($user_id);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new ProfileController();
    
    if (isset($_POST['update_profile'])) {
        $controller->updateProfile();
    }
}
?>