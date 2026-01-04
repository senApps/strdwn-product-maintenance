<?php
header('Content-Type: application/json');
require_once 'db_config.php'; // Verbindung zur Datenbank

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['vorname']) && !empty($data['nachname'])) {
    $query = "INSERT INTO Fighters (vorname, nachname, geburtsdatum, nationalitaet, gewichtsklasse, disziplin_id, organisation, rekord)
              VALUES (:vorname, :nachname, :geburtsdatum, :nationalitaet, :gewichtsklasse, :disziplin_id, :organisation, :rekord)";

    if (empty($data['geburtsdatum'])) {
        $data['geburtsdatum'] = null;
    }

    if (empty($data['rekord'])) {
        $data['rekord'] = null;
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':vorname', $data['vorname']);
    $stmt->bindParam(':nachname', $data['nachname']);
    $stmt->bindParam(':geburtsdatum', $data['geburtsdatum']);
    $stmt->bindParam(':nationalitaet', $data['nationalitaet']);
    $stmt->bindParam(':gewichtsklasse', $data['gewichtsklasse']);
    $stmt->bindParam(':disziplin_id', $data['disziplin_id']);
    $stmt->bindParam(':organisation', $data['organisation']);
    $stmt->bindParam(':rekord', $data['rekord']);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Fighter created successfully.']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Database error.', 'error' => $errorInfo[2]]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}
?>
