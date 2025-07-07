<?php
session_start();
require_once("../db/connection.php");

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
    echo json_encode([]);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

if ($rol === 'tecnico') {
    // Técnico: solo sus comentarios
    $stmt = $conn->prepare("SELECT * FROM comentarios WHERE tecnico_id = ?");
    $stmt->bind_param("i", $usuario_id);
} elseif ($rol === 'administrador') {
    // Administrador: todos los comentarios
    $stmt = $conn->prepare("SELECT * FROM comentarios");
} else {
    echo json_encode([]);
    exit(); // Cliente no puede ver comentarios
}

$stmt->execute();
$result = $stmt->get_result();

$comentarios = [];
while ($row = $result->fetch_assoc()) {
    $comentarios[] = [
        'id' => $row['id'],
        'ticket_id' => $row['ticket_id'],
        'comment_text' => obtenerNombreTecnico($conn, $row['tecnico_id']),
        'comment_description' => $row['comentario']
    ];
}

echo json_encode($comentarios);

// Obtener nombre del técnico
function obtenerNombreTecnico($conn, $id) {
    $stmt = $conn->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nombre);
    $stmt->fetch();
    $stmt->close();
    return $nombre;
}
?>
