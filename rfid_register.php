<?php
include_once "functions.php";

// Verificar si el parámetro "serial" se envía en la solicitud GET
if (!isset($_GET["serial"])) {
    echo "Error: 'serial' parameter is not present in the GET request.";
    exit;
}

// Obtener el valor del parámetro "serial"
$serial = $_GET["serial"];
echo "Serial received: " . $serial;

// Llamar a la función onRfidSerialRead con el valor del parámetro "serial"
onRfidSerialRead($serial);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if ($data && isset($data->action)) {
        $action = $data->action;

        if ($action === 'turn_on_led') {
            // Lógica para encender el LED aquí
            // Por ejemplo, puedes usar digitalWrite(PIN_LED, HIGH);
            echo json_encode(['message' => 'LED encendido']);
        } elseif ($action === 'turn_off_led') {
            // Lógica para apagar el LED aquí
            // Por ejemplo, puedes usar digitalWrite(PIN_LED, LOW);
            echo json_encode(['message' => 'LED apagado']);
        } else {
            echo json_encode(['message' => 'Acción no válida']);
        }
    } else {
        echo json_encode(['message' => 'Acción no especificada']);
    }
} else {
    echo json_encode(['message' => 'Método de solicitud no admitido']);
}
?>
