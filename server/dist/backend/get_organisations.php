<?php
// Konfigurationsdatei einbinden
require_once 'db_config.php';

$response = array();

try {
    // Kämpferdaten abrufen
    $sql = "SELECT id, name FROM Organisations";
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
