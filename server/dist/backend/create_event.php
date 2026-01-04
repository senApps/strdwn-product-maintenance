<?php
header('Content-Type: application/json');
require_once 'db_config.php'; // Verbindung zur Datenbank

// Daten aus dem JSON-Body lesen
$data = json_decode(file_get_contents('php://input'), true);

// Validierung der Eingabedaten
if (!empty($data['name']) && !empty($data['austragungsort']) && !empty($data['organisation_id'])) {
    $query = "INSERT INTO Events (name, datum, uhrzeit, austragungsort, organisation_id, streaming_service, ticket_link, bild_url, created_at)
              VALUES (:name, :datum, :uhrzeit, :austragungsort, :organisation_id, :streaming_service, :ticket_link, :bild_url, NOW())";

    // Werte für leere Felder mit NULL setzen
    $data['datum'] = !empty($data['datum']) ? $data['datum'] : null;
    $data['uhrzeit'] = !empty($data['uhrzeit']) ? $data['uhrzeit'] : null;
    $data['streaming_service'] = !empty($data['streaming_service']) ? $data['streaming_service'] : null;
    $data['ticket_link'] = !empty($data['ticket_link']) ? $data['ticket_link'] : null;
    $data['bild_url'] = !empty($data['bild_url']) ? $data['bild_url'] : null;

    // Datenbank-Statement vorbereiten
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':datum', $data['datum']);
    $stmt->bindParam(':uhrzeit', $data['uhrzeit']);
    $stmt->bindParam(':austragungsort', $data['austragungsort']);
    $stmt->bindParam(':organisation_id', $data['organisation_id']);
    $stmt->bindParam(':streaming_service', $data['streaming_service']);
    $stmt->bindParam(':ticket_link', $data['ticket_link']);
    $stmt->bindParam(':bild_url', $data['bild_url']);

    // Statement ausführen und Erfolg prüfen
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Event created successfully.']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Database error.', 'error' => $errorInfo[2]]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input. Required fields: name, austragungsort, organisation_id.']);
}
?>
