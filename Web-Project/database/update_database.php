<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "login_sample_db";

$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$con) {
    die("Conexiune eșuată: " . mysqli_connect_error());
}

echo "<h2>Actualizare baza de date pentru integrarea funcționalităților</h2>";

$sql = "CREATE TABLE IF NOT EXISTS user_profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    phone VARCHAR(20),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $sql)) {
    echo "✓ Tabela user_profile creată/actualizată cu succes!<br>";
} else {
    echo "✗ Eroare la crearea tabelei user_profile: " . mysqli_error($con) . "<br>";
}

$sql = "CREATE TABLE IF NOT EXISTS event_chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $sql)) {
    echo "✓ Tabela event_chat creată cu succes!<br>";
} else {
    echo "✗ Eroare la crearea tabelei event_chat: " . mysqli_error($con) . "<br>";
}

$columns_to_add = [
    "event_type VARCHAR(50) DEFAULT 'Sport'",
    "event_description TEXT",
    "lat DECIMAL(10,8)",
    "lng DECIMAL(11,8)",
    "participation_policy VARCHAR(20) DEFAULT 'first-come'",
    "min_participations INT DEFAULT 0"
];

foreach ($columns_to_add as $column) {
    $column_name = explode(' ', $column)[0];
    
    $check_sql = "SHOW COLUMNS FROM events LIKE '$column_name'";
    $result = mysqli_query($con, $check_sql);
    
    if (mysqli_num_rows($result) == 0) {
        $sql = "ALTER TABLE events ADD COLUMN $column";
        if (mysqli_query($con, $sql)) {
            echo "✓ Coloana $column_name adăugată în tabela events<br>";
        } else {
            echo "✗ Eroare la adăugarea coloanei $column_name: " . mysqli_error($con) . "<br>";
        }
    } else {
        echo "- Coloana $column_name există deja în tabela events<br>";
    }
}

$sql = "UPDATE events SET 
    event_description = description,
    lat = location_lat,
    lng = location_lon,
    participation_policy = 'first-come'
WHERE event_description IS NULL OR lat IS NULL";

if (mysqli_query($con, $sql)) {
    echo "✓ Datele existente au fost actualizate<br>";
} else {
    echo "✗ Eroare la actualizarea datelor: " . mysqli_error($con) . "<br>";
}

$check_sql = "SHOW COLUMNS FROM users LIKE 'user_id'";
$result = mysqli_query($con, $check_sql);

if (mysqli_num_rows($result) == 0) {
    $sql = "ALTER TABLE users ADD COLUMN user_id VARCHAR(20)";
    if (mysqli_query($con, $sql)) {
        echo "✓ Coloana user_id adăugată în tabela users<br>";
        
        $sql = "UPDATE users SET user_id = CAST(id AS CHAR) WHERE user_id IS NULL";
        if (mysqli_query($con, $sql)) {
            echo "✓ Valorile user_id au fost actualizate<br>";
        }
    } else {
        echo "✗ Eroare la adăugarea coloanei user_id: " . mysqli_error($con) . "<br>";
    }
} else {
    echo "- Coloana user_id există deja în tabela users<br>";
}

$check_sql = "SHOW COLUMNS FROM event_participants LIKE 'status'";
$result = mysqli_query($con, $check_sql);

if (mysqli_num_rows($result) == 0) {
    $sql = "ALTER TABLE event_participants ADD COLUMN status VARCHAR(20) DEFAULT 'registered'";
    if (mysqli_query($con, $sql)) {
        echo "✓ Coloana status adăugată în tabela event_participants<br>";
    } else {
        echo "✗ Eroare la adăugarea coloanei status: " . mysqli_error($con) . "<br>";
    }
} else {
    echo "- Coloana status există deja în tabela event_participants<br>";
}

$check_sql = "SHOW COLUMNS FROM event_participants LIKE 'registration_date'";
$result = mysqli_query($con, $check_sql);

if (mysqli_num_rows($result) == 0) {
    $sql = "ALTER TABLE event_participants ADD COLUMN registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    if (mysqli_query($con, $sql)) {
        echo "✓ Coloana registration_date adăugată în tabela event_participants<br>";
        
        $sql = "UPDATE event_participants SET registration_date = join_date WHERE registration_date IS NULL AND join_date IS NOT NULL";
        mysqli_query($con, $sql);
        echo "✓ Datele registration_date au fost actualizate<br>";
    } else {
        echo "✗ Eroare la adăugarea coloanei registration_date: " . mysqli_error($con) . "<br>";
    }
} else {
    echo "- Coloana registration_date există deja în tabela event_participants<br>";
}

$columns_to_add = [
    "duration INT DEFAULT 1",
    "status VARCHAR(20) DEFAULT 'active'"
];

foreach ($columns_to_add as $column) {
    $column_name = explode(' ', $column)[0];
    $check_sql = "SHOW COLUMNS FROM events LIKE '$column_name'";
    $result = mysqli_query($con, $check_sql);
    
    if (mysqli_num_rows($result) == 0) {
        $sql = "ALTER TABLE events ADD COLUMN $column";
        if (mysqli_query($con, $sql)) {
            echo "✓ Coloana $column_name adăugată în tabela events<br>";
        }
    }
}

$check_sql = "SHOW COLUMNS FROM users LIKE 'sport_tags'";
$result = mysqli_query($con, $check_sql);

if (mysqli_num_rows($result) == 0) {
    $sql = "ALTER TABLE users ADD COLUMN sport_tags TEXT DEFAULT NULL";
    if (mysqli_query($con, $sql)) {
        echo "✓ Coloana sport_tags adăugată în tabela users<br>";
    } else {
        echo "✗ Eroare la adăugarea coloanei sport_tags: " . mysqli_error($con) . "<br>";
    }
} else {
    echo "- Coloana sport_tags există deja în tabela users<br>";
}

$indices = [
    "CREATE INDEX IF NOT EXISTS idx_user_profile_user_id ON user_profile(user_id)",
    "CREATE INDEX IF NOT EXISTS idx_chat_event ON event_chat(event_id)",
    "CREATE INDEX IF NOT EXISTS idx_chat_user ON event_chat(user_id)",
    "CREATE INDEX IF NOT EXISTS idx_events_date ON events(event_date)",
    "CREATE INDEX IF NOT EXISTS idx_events_type ON events(event_type)"
];

foreach ($indices as $index_sql) {
    if (mysqli_query($con, $index_sql)) {
        echo "✓ Index creat cu succes<br>";
    } else {
        echo "✗ Eroare la crearea indexului: " . mysqli_error($con) . "<br>";
    }
}

mysqli_close($con);

echo "<br><h3>🎉 Actualizarea bazei de date a fost finalizată!</h3>";
echo "<p>Acum poți accesa:</p>";
echo "<ul>";
echo "<li><a href='../views/dashboard.php'>Dashboard</a></li>";
echo "<li><a href='../views/profile.php'>Profil utilizator</a></li>";
echo "<li><a href='../views/evenimente.php'>Evenimente</a></li>";
echo "<li><a href='../views/harta.php'>Harta terenurilor</a></li>";
echo "</ul>";
?>