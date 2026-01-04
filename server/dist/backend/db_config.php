<?php
// db_config.php

// Verbindungsinformationen zur Datenbank
$host = 'sql497.your-server.de'; // Server
$dbname = 'strdwn'; // Datenbankname
$username = 'strdwn'; // Benutzername
$password = 'p85czggiV1dV9MzT'; // Passwort

try {
    // Verbindung zur Datenbank herstellen
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fehler bei der Verbindung zur Datenbank: " . $e->getMessage());
}
?>
