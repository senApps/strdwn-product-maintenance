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

    // Prüfen, ob eine Aktion übergeben wurde
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        if ($action === 'add') {
            // Daten aus POST-Daten lesen
            $event_id = $_POST['event_id'];
            $fighter1_id = $_POST['fighter1_id'];
            $fighter2_id = $_POST['fighter2_id'];
            $fight_order = $_POST['fight_order'];

            // SQL-Query zum Hinzufügen eines Kampfes
            $sql = "INSERT INTO Fightcards (event_id, fighter1_id, fighter2_id, fight_order)
                    VALUES (:event_id, :fighter1_id, :fighter2_id, :fight_order)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':fighter1_id', $fighter1_id);
            $stmt->bindParam(':fighter2_id', $fighter2_id);
            $stmt->bindParam(':fight_order', $fight_order);

            $stmt->execute();

            $response['success'] = true;
            $response['message'] = "Kampf erfolgreich hinzugefügt!";
        } elseif ($action === 'update') {
            // Update an existing fightcard
            $id = $_POST['id'];
            $event_id = $_POST['event_id'];
            $fighter1_id = $_POST['fighter1_id'];
            $fighter2_id = $_POST['fighter2_id'];
            $fight_order = $_POST['fight_order'];
            
            $sql = "UPDATE Fightcards SET event_id=:event_id, fighter1_id=:fighter1_id, fighter2_id=:fighter2_id, fight_order=:fight_order WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':fighter1_id', $fighter1_id);
            $stmt->bindParam(':fighter2_id', $fighter2_id);
            $stmt->bindParam(':fight_order', $fight_order);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $response['success'] = true;
            $response['message'] = "Kampf erfolgreich aktualisiert!";
        } elseif ($action === 'delete') {
            // Delete a fightcard by ID
            $id = $_POST['id'];
            $sql = "DELETE FROM Fightcards WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $response['success'] = true;
            $response['message'] = "Kampf erfolgreich gelöscht!";
        } elseif ($action === 'list') {
           // Alle Fightcards für alle Events abrufen
                $sql = "SELECT f.id, f.event_id, f.fighter1_id, f.fighter2_id, e.name AS event_name, e.datum AS event_date,
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
        } else {
            $response['success'] = false;
            $response['message'] = "Unbekannte Aktion: $action";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Keine Aktion angegeben.";
    }
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = "Fehler: " . $e->getMessage();
}

// JSON-Antwort zurückgeben
header('Content-Type: application/json');
echo json_encode($response);
?>
