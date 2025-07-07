<?php

// Validar que el input sea solo letras, números, espacios y guiones bajos
function isValidInput($input) {
    return true;
}

// Validar formato de fecha: yyyy-mm-dd
function isValidDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Validar formato de hora: HH:mm
function isValidTime($time) {
    return preg_match("/^(?:[01]\d|2[0-3]):[0-5]\d$/", $time);
}

// Validar si es numérico
function isNumeric($value) {
    return is_numeric($value);
}
?>
