<?php
session_start();
require_once("../db/connection.php");
include 'security.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['tecnico', 'administrador'])) {
    exit("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];
    $tecnico_id = $_POST['tecnico_id'];
    $categoria = $_POST['categoria'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $descripcion = $_POST['descripcion'];

    if (
        isNumeric($ticket_id) &&
        isNumeric($tecnico_id) &&
        isValidInput($categoria) &&
        isValidDate($fecha) &&
        isValidTime($hora) &&
        isValidInput($descripcion)
    ) {
        if ($_SESSION['rol'] === 'tecnico') {
            $stmt_check = $conn->prepare("SELECT tecnico_id FROM tickets WHERE id = ?");
            $stmt_check->bind_param("i", $ticket_id);
            $stmt_check->execute();
            $stmt_check->bind_result($asignado);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($asignado != $_SESSION['usuario_id']) {
                exit("No puedes editar este ticket.");
            }
        }

        $stmt = $conn->prepare("UPDATE tickets SET tecnico_id = ?, categoria = ?, fecha = ?, hora = ?, descripcion = ? WHERE id = ?");
        $stmt->bind_param("issssi", $tecnico_id, $categoria, $fecha, $hora, $descripcion, $ticket_id);

        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=ticket_editado");
        } else {
            echo "Error al editar.";
        }
        $stmt->close();
    } else {
        echo "Datos inválidos.";
    }
}
?>
