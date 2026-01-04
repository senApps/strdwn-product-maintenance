<?php
// Konfigurationsdatei einbinden
require_once 'db_config.php';

$response = array();

try {
    // Verbindung zur Datenbank herstellen
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Alle Fightcards für alle Events abrufen
    $sql = "SELECT f.id, e.name AS event_name, e.datum AS event_date,
            f.fight_order, 
            CONCAT(f1.vorname, ' ', f1.nachname) AS fighter1_name, 
            CONCAT(f2.vorname, ' ', f2.nachname) AS fighter2_name
            FROM Fightcards f
            JOIN Fighters f1 ON f.fighter1_id = f1.id
            JOIN Fighters f2 ON f.fighter2_id = f2.id
            JOIN Events e ON f.event_id = e.id
            ORDER BY e.name ASC, f.fight_order DESC"; // Sortiere nach Datum des Events und der Fight-Reihenfolge
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $fightcards = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = $fightcards; // Fightcard als Array zurückgeben
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = "Datenbankfehler: " . $e->getMessage();
}

// JSON-Antwort zurückgeben
header('Content-Type: application/json');
echo json_encode($response);
?>
