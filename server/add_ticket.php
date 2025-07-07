<?php
session_start();
require_once("../db/connection.php");
include 'security.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
        echo "Acceso denegado.";
        exit();
    }

    $cliente_id = $_SESSION['usuario_id'];
    $tecnico_id = $_POST['tecnico_id'];
    $categoria = $_POST['categoria'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $descripcion = $_POST['descripcion'];

    if (
        isNumeric($tecnico_id) &&
        isValidInput($categoria) &&
        isValidDate($fecha) &&
        isValidTime($hora) &&
        isValidInput($descripcion)
    ) {
        $stmt = $conn->prepare("INSERT INTO tickets (usuario_id, tecnico_id, categoria, fecha, hora, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $cliente_id, $tecnico_id, $categoria, $fecha, $hora, $descripcion);

        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=ticket_creado");
        } else {
            echo "Error al insertar el ticket.";
        }

        $stmt->close();
    } else {
        echo "Datos inválidos.";
    }
}
?>
