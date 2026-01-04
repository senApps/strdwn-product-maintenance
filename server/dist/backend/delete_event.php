<?php
header('Content-Type: application/json');
require_once 'db_config.php'; // Verbindung zur Datenbank

// Daten aus der POST-Anfrage lesen
$data = json_decode(file_get_contents('php://input'), true);

// Überprüfen, ob die ID vorhanden ist
if (!empty($data['id'])) {
    try {
        // SQL-Query zum Löschen eines Events
        $query = "DELETE FROM Events WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

        // Ausführen der Query
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Event successfully deleted.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete event.']);
        }
    } catch (PDOException $e) {
        // Fehler beim Löschen
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    // Fehler, wenn keine ID übergeben wurde
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}
?>
