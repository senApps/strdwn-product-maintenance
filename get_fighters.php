<?php
// Verbindungsinformationen zur Datenbank
$host = 'sql497.your-server.de'; // Server
$dbname = 'strdwn'; // Deine Datenbank
$username = 'strdwn'; // Dein DB-Benutzername
$password = 'p85czggiV1dV9MzT'; // Dein DB-Passwort
$response = array();

try {
    // Verbindung zur Datenbank herstellen
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kämpferdaten abrufen
    $sql = "SELECT id, CONCAT(vorname, ' ', nachname) AS name FROM Fighters ORDER BY name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $fighters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = $fighters;
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = "Fehler: " . $e->getMessage();
}

// JSON-Antwort zurückgeben
header('Content-Type: application/json');
echo json_encode($response);
?>
