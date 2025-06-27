<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>
    <link href="../resources/css/loginStyle.css" rel="stylesheet">
    <!-- Atasez scriptul JS pentru login, cu defer ca sa se incarce dupa ce e gata pagina -->
    <script src="../resources/js/login.js" defer></script>
</head>
<body>
    <div class="login-container">
        <!-- Formular care va fi procesat prin AJAX, fara action si method -->
        <form id="loginForm">
            <h2 class="form-title">Login</h2>
            
            <!-- Container pentru afisarea erorilor, initial ascuns -->
            <div class="error-message" style="display: none;"></div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="submit-btn">Login</button>

            <a href="register.php" class="form-link">Click to Register!</a>
        </form>
    </div> 
</body>
</html>