<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob eine Bilddatei gesendet wurde
    if (!empty($_FILES['bild']) && isset($_POST['event_id'])) {
        $eventId = $_POST['event_id'];  // Event-ID aus der Anfrage
        $eventId = str_replace(' ', '_', $eventId);
        // Erstelle den Ordner, wenn er noch nicht existiert
        $targetDir = "uploads/events/" . $eventId . "/"; // Event-ID Ordner
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Dateiname und Zielpfad
        $fileName = basename($_FILES['bild']['name']);
        
        $targetFilePath = $targetDir . $fileName;

        // Bild hochladen
        if (move_uploaded_file($_FILES['bild']['tmp_name'], $targetFilePath)) {
            // Dynamische URL-Basis ermitteln
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $baseUrl = $protocol . $host . '/backend/';

            $response = [
                'success' => true,
                'path' => $baseUrl . $targetFilePath  // Rückgabe des vollständigen Pfades
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error uploading file'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'No file uploaded or event_id not provided'
        ];
    }

    echo json_encode($response);  // Antwort zurück an den Frontend
}
?>
