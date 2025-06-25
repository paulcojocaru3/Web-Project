<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Page</title>
    <!-- Foaia de stil pentru pagina de inregistrare -->
    <link href="../resources/css/loginStyle.css" rel="stylesheet">
    <!-- Atasez scriptul JS pentru register, cu defer ca sa se incarce dupa ce e gata pagina -->
    <script src="../resources/js/register.js" defer></script>
</head>
<body>
    <div class="login-container">
        <!-- Formular care va fi procesat prin AJAX, fara action si method -->
        <form id="registerForm">
            <h2 class="form-title">Register</h2>
            
            <!-- Container pentru afisarea erorilor, initial ascuns -->
            <div class="error-message" style="display: none;"></div>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>

            <div class="form-group">
                <label for="birth_date">Date of Birth:</label>
                <input type="date" id="birth_date" name="birth_date" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       title="Parola trebuie sa aiba minim 8 caractere, o litera mare si un simbol special!"
                       required>
            </div>

            <button type="submit" class="submit-btn">Register</button>

            <a href="login.php" class="form-link">Click to Login!</a>
        </form>
    </div>   
</body>
</html>