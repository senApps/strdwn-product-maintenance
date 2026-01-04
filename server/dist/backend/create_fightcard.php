<?php
header('Content-Type: application/json');
require_once 'db_config.php'; // Verbindung zur Datenbank

// POST-Daten einlesen
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['event_id']) && !empty($data['fighter1_id']) && !empty($data['fighter2_id']) && !empty($data['fight_order'])) {
    $query = "INSERT INTO Fightcards (event_id, fighter1_id, fighter2_id, fight_order)
              VALUES (:event_id, :fighter1_id, :fighter2_id, :fight_order)";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':event_id', $data['event_id']);
    $stmt->bindParam(':fighter1_id', $data['fighter1_id']);
    $stmt->bindParam(':fighter2_id', $data['fighter2_id']);
    $stmt->bindParam(':fight_order', $data['fight_order']);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Fightcard created successfully.']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Database error.', 'error' => $errorInfo[2]]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input. Missing event_id, fighter1_id, fighter2_id, or fight_order.']);
}
?>
