<?php
$host = 'sql497.your-server.de';
$dbname = 'strdwn';
$username = 'strdwn';
$password = 'p85czggiV1dV9MzT';
$response = array();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT id, name FROM Organisations";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $organisations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = $organisations;
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = "Fehler: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
