<?php

/**
 * Obtiene los boletines más recientes publicados por la administración.
 */
function obtenerBoletines(mysqli $conexion): array
{
    // Devuelve los comunicados más nuevos primero para mostrarlos en el panel.
    $boletines = [];
    $consulta = mysqli_query(
        $conexion,
        'SELECT idBoletin, titulo, fechaSubida FROM boletin ORDER BY fechaSubida DESC, idBoletin DESC'
    );

    // Si la consulta falla, las páginas muestran el estado vacío sin romperse.
    if (!$consulta) {
        return $boletines;
    }

    while ($boletin = mysqli_fetch_assoc($consulta)) {
        $boletines[] = $boletin;
    }

    return $boletines;
}
