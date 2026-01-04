<?php
header('Content-Type: application/json');
require_once 'db_config.php'; // Verbindung zur Datenbank

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['id'])) {
    $query = "UPDATE Fighters SET 
              vorname = :vorname,
              nachname = :nachname,
              geburtsdatum = :geburtsdatum,
              nationalitaet = :nationalitaet,
              gewichtsklasse = :gewichtsklasse,
              disziplin_id = :disziplin_id,
              organisation = :organisation,
              rekord = :rekord
              WHERE id = :id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $data['id']);
    $stmt->bindParam(':vorname', $data['vorname']);
    $stmt->bindParam(':nachname', $data['nachname']);
    $stmt->bindParam(':geburtsdatum', $data['geburtsdatum']);
    $stmt->bindParam(':nationalitaet', $data['nationalitaet']);
    $stmt->bindParam(':gewichtsklasse', $data['gewichtsklasse']);
    $stmt->bindParam(':disziplin_id', $data['disziplin_id']);
    $stmt->bindParam(':organisation', $data['organisation']);
    $stmt->bindParam(':rekord', $data['rekord']);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Fighter updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}
?>
