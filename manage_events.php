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
            $name = $_POST['name'];
            $datum = $_POST['datum'];
            if ($datum == "") {
                $datum = NULL;
            }
            $uhrzeit = $_POST['uhrzeit'];
            $austragungsort = $_POST['austragungsort'];
            $organisation_id = $_POST['organisation_id']; // Neu hinzugefügt
            $streaming_service = $_POST['streaming_service'] ?? null;
            $ticket_link = $_POST['ticket_link'] ?? null;
            $image_path = null;

            // Handle image upload
            if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'server/dist/backend/uploads/events/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = basename($_FILES['eventImage']['name']);
                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['eventImage']['tmp_name'], $targetPath)) {
                    $image_path = $targetPath;
                }
            }

            // SQL-Query zum Hinzufügen eines Events
            $sql = "INSERT INTO Events (name, datum, uhrzeit, austragungsort, organisation_id, streaming_service, ticket_link, image_path, created_at)
                    VALUES (:name, :datum, :uhrzeit, :austragungsort, :organisation_id, :streaming_service, :ticket_link, :image_path, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':datum', $datum);
            $stmt->bindParam(':uhrzeit', $uhrzeit);
            $stmt->bindParam(':austragungsort', $austragungsort);
            $stmt->bindParam(':organisation_id', $organisation_id); // Neu hinzugefügt
            $stmt->bindParam(':streaming_service', $streaming_service);
            $stmt->bindParam(':ticket_link', $ticket_link);
            $stmt->bindParam(':image_path', $image_path);

            $stmt->execute();

            $response['success'] = true;
            $response['message'] = "Event erfolgreich hinzugefügt!";

        } elseif ($action === 'update') {
            // Update an existing event
            $id = $_POST['id'];
            $name = $_POST['name'];
            $datum = $_POST['datum'];
            if ($datum == "") {
                $datum = NULL;
            }
            $uhrzeit = $_POST['uhrzeit'];
            $austragungsort = $_POST['austragungsort'];
            $organisation_id = $_POST['organisation_id'];
            $streaming_service = $_POST['streaming_service'] ?? null;
            $ticket_link = $_POST['ticket_link'] ?? null;
            $image_path = null;

            // Get current image_path
            $sql = "SELECT image_path FROM Events WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            $image_path = $current ? $current['image_path'] : null;

            // Handle new image upload (replace old image)
            if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'server/dist/backend/uploads/events/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = basename($_FILES['eventImage']['name']);
                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['eventImage']['tmp_name'], $targetPath)) {
                    // Delete old image if exists and is different
                    if ($image_path && file_exists($image_path) && $image_path !== $targetPath) {
                        unlink($image_path);
                    }
                    $image_path = $targetPath;
                }
            }

            $sql = "UPDATE Events SET name=:name, datum=:datum, uhrzeit=:uhrzeit, austragungsort=:austragungsort, organisation_id=:organisation_id, streaming_service=:streaming_service, ticket_link=:ticket_link, image_path=:image_path WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':datum', $datum);
            $stmt->bindParam(':uhrzeit', $uhrzeit);
            $stmt->bindParam(':austragungsort', $austragungsort);
            $stmt->bindParam(':organisation_id', $organisation_id);
            $stmt->bindParam(':streaming_service', $streaming_service);
            $stmt->bindParam(':ticket_link', $ticket_link);
            $stmt->bindParam(':image_path', $image_path);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $response['success'] = true;
            $response['message'] = "Event erfolgreich aktualisiert!";

        } elseif ($action === 'delete') {
            // Delete an event by ID
            $id = $_POST['id'];
            // Get image path to delete file
            $sql = "SELECT image_path FROM Events WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($event && $event['image_path'] && file_exists($event['image_path'])) {
                unlink($event['image_path']);
            }
            // Delete from DB
            $sql = "DELETE FROM Events WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $response['success'] = true;
            $response['message'] = "Event erfolgreich gelöscht!";

        } elseif ($action === 'list') {
            // Events aus der Datenbank abrufen
            $sql = "SELECT * FROM Events ORDER BY datum DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = $events; // Alle Events als Array zurückgeben
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
