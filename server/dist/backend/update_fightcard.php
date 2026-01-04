<?php
header('Content-Type: application/json');
require_once 'db_config.php'; // Verbindung zur Datenbank

// POST-Daten einlesen
$data = json_decode(file_get_contents('php://input'), true);

// Überprüfen, ob die erforderlichen Daten vorhanden sind
if (!empty($data['id']) && !empty($data['event_id']) && !empty($data['fighter1_id']) && !empty($data['fighter2_id']) && !empty($data['fight_order'])) {
    $query = "UPDATE Fightcards 
              SET event_id = :event_id, 
                  fighter1_id = :fighter1_id, 
                  fighter2_id = :fighter2_id, 
                  fight_order = :fight_order 
              WHERE id = :id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $stmt->bindParam(':event_id', $data['event_id'], PDO::PARAM_INT);
    $stmt->bindParam(':fighter1_id', $data['fighter1_id'], PDO::PARAM_INT);
    $stmt->bindParam(':fighter2_id', $data['fighter2_id'], PDO::PARAM_INT);
    $stmt->bindParam(':fight_order', $data['fight_order'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Fightcard updated successfully.']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Database error.', 'error' => $errorInfo[2]]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input. Missing id, event_id, fighter1_id, fighter2_id, or fight_order.']);
}
?>
