<?php
session_start();
require_once("../db/connection.php");

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

if (!in_array($rol, ['tecnico', 'administrador'])) {
    echo json_encode(['status' => 'error', 'message' => 'Permiso denegado']);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID no recibido']);
    exit();
}

$ticket_id = $input['id'];

// Técnicos solo pueden eliminar sus propios tickets
if ($rol === 'tecnico') {
    $stmt = $conn->prepare("SELECT tecnico_id FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->bind_result($asignado);
    $stmt->fetch();
    $stmt->close();

    if ($asignado != $usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Este ticket no te pertenece']);
        exit();
    }
}

$stmt = $conn->prepare("DELETE FROM tickets WHERE id = ?");
$stmt->bind_param("i", $ticket_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el ticket']);
}
$stmt->close();
?>
