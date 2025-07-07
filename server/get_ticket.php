<?php
session_start();
require_once("../db/connection.php");

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
    echo json_encode([]);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

if ($rol === 'cliente') {
    $stmt = $conn->prepare("SELECT t.id, u.nombre AS user_name, t.fecha, t.hora FROM tickets t JOIN usuarios u ON t.usuario_id = u.id WHERE t.usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
} elseif ($rol === 'tecnico') {
    $stmt = $conn->prepare("SELECT t.id, u.nombre AS user_name, t.fecha, t.hora FROM tickets t JOIN usuarios u ON t.usuario_id = u.id WHERE t.tecnico_id = ?");
    $stmt->bind_param("i", $usuario_id);
} else {
    $stmt = $conn->prepare("SELECT t.id, u.nombre AS user_name, t.fecha, t.hora FROM tickets t JOIN usuarios u ON t.usuario_id = u.id");
}

$stmt->execute();
$result = $stmt->get_result();

$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = [
        'id' => $row['id'],
        'user_name' => $row['user_name'],
        'fecha' => $row['fecha'],
        'hora' => $row['hora']
    ];
}

echo json_encode($tickets);
?>
