<?php
include_once '../models/UserModel.php';

// Accept doar cereri POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Metoda HTTP nepermisa']);
    exit();
}

// Setez header pentru raspuns JSON
header('Content-Type: application/json');

// Preiau datele din cererea JSON
$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$first_name = trim($data['first_name'] ?? '');
$last_name = trim($data['last_name'] ?? '');
$birth_date = $data['birth_date'] ?? '';
$gender = $data['gender'] ?? '';
$password = $data['password'] ?? '';

$errors = [];

// Validez toate campurile obligatorii
if (empty($username) || empty($email) || empty($password) ||
    empty($first_name) || empty($last_name) ||
    empty($birth_date) || empty($gender)) {
    $errors[] = "Toate campurile sunt obligatorii! Va rugam sa completati toate informatiile.";
}

// Validez formatul email-ului
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Adresa de email introdusa nu este valida!";
}

// Validez complexitatea parolei
if (strlen($password) < 7) {
    $errors[] = "Parola trebuie sa aiba cel putin 7 caractere!";
}

if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = "Parola trebuie sa contina cel putin o litera mare!";
}

if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
    $errors[] = "Parola trebuie sa contina cel putin un simbol special!";
}

if (!preg_match('/[0-9]/', $password)) {
    $errors[] = "Parola trebuie sa contina cel putin o cifra!";
}

// Verific daca username-ul sau email-ul exista deja
$userModel = new UserModel();

if ($userModel->checkIfUserExists($username)) {
    $errors[] = "Numele de utilizator introdus exista deja! Va rugam sa alegeti alt username.";
}

if ($userModel->checkIfEmailExists($email)) {
    $errors[] = "Adresa de email este deja inregistrata! Folositi alta adresa sau recuperati parola.";
}

// Daca am erori, le returnez catre client
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'errors' => $errors
    ]);
    exit();
}

// Creez cont nou
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
if ($userModel->createUser($username, $hashedPassword, $email,
                        $first_name, lastname: $last_name, birth_date: $birth_date, gender: $gender)) {
    http_response_code(201); // Created
    echo json_encode([
        'status' => 'success',
        'message' => 'Cont creat cu succes!'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'A aparut o eroare la inregistrare.'
    ]);
}
?>