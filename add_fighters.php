<?php
// Verbindungsinformationen zur Datenbank
$host = 'sql497.your-server.de'; 
$dbname = 'strdwn'; 
$username = 'strdwn'; 
$password = 'p85czggiV1dV9MzT'; 
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
            $vorname = $_POST['vorname'];
            $nachname = $_POST['nachname'];
            $geburtsdatum = $_POST['geburtsdatum'];
            $nationalitaet = $_POST['nationalitaet'];
            $gewichtsklasse = $_POST['gewichtsklasse'];
            $disziplin_id = $_POST['disziplin'];
            $organisation = $_POST['organisation'];
            $rekord = $_POST['rekord'] ?? null;  // Optional
            $image_path = null;

            if($geburtsdatum == ""){
                $geburtsdatum = NULL;
            }

            // Handle image upload
            if (isset($_FILES['fighterImage']) && $_FILES['fighterImage']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'server/dist/backend/uploads/fighters/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = basename($_FILES['fighterImage']['name']);
                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['fighterImage']['tmp_name'], $targetPath)) {
                    $image_path = $targetPath;
                }
            }

            // SQL-Query zum Hinzufügen eines Kämpfers
            $sql = "INSERT INTO Fighters (vorname, nachname, geburtsdatum, nationalitaet, gewichtsklasse, disziplin_id, organisation, rekord, image_path)
                    VALUES (:vorname, :nachname, :geburtsdatum, :nationalitaet, :gewichtsklasse, :disziplin_id, :organisation, :rekord, :image_path)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':vorname', $vorname);
            $stmt->bindParam(':nachname', $nachname);
            $stmt->bindParam(':geburtsdatum', $geburtsdatum);
            $stmt->bindParam(':nationalitaet', $nationalitaet);
            $stmt->bindParam(':gewichtsklasse', $gewichtsklasse);
            $stmt->bindParam(':disziplin_id', $disziplin_id);
            $stmt->bindParam(':organisation', $organisation);
            $stmt->bindParam(':rekord', $rekord);
            $stmt->bindParam(':image_path', $image_path);

            $stmt->execute();

            $response['success'] = true;
            $response['message'] = "Kämpfer erfolgreich hinzugefügt!";
        } elseif ($action === 'list') {
            // Kämpfer aus der Datenbank abrufen
            $sql = "SELECT * FROM Fighters ORDER BY id DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $fighters = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = $fighters; // Alle Kämpfer als Array zurückgeben
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
