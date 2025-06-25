<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";

$con = mysqli_connect($dbhost, $dbuser, $dbpass);
if (!$con) {
    die("Conexiune eșuată: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS login_sample_db";
if (mysqli_query($con, $sql)) {
    echo "Baza de date a fost creată sau există deja.<br>";
} else {
    echo "Eroare la crearea bazei de date: " . mysqli_error($con) . "<br>";
    die();
}

mysqli_select_db($con, "login_sample_db");

$sql_file = 'login_sample_db.sql';

if (file_exists($sql_file)) {
    $sql_content = file_get_contents($sql_file);
    
    if (mysqli_multi_query($con, $sql_content)) {
        echo "Import SQL realizat cu succes!<br>";
        do {
            if ($result = mysqli_store_result($con)) {
                mysqli_free_result($result);
            }
        } while (mysqli_next_result($con));
    } else {
        echo "Eroare la importul SQL: " . mysqli_error($con) . "<br>";
    }
} else {
    echo "Fișierul SQL nu a fost gasit!<br>";
}

echo "<br>Configurare finalizata! Acum poti accesa aplicatia.";

mysqli_close($con);
?>