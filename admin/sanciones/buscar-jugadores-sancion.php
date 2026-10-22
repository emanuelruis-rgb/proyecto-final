<?php
session_name('admin_session');
session_start();
if (!isset($_SESSION['nombre'])) { http_response_code(403); exit; }

require_once __DIR__ . "/../../conexion-bd/conexion.php";
$conexion = connection();

/* idClub (y idCategoria, si se eligió una) los recibe de sanciones.js, que los manda en el fetch()
con el valor de los select de club y categoría de sanciones.php. Este archivo no se abre directo */
$idClub      = (int)($_GET['idClub'] ?? 0);
$idCategoria = (int)($_GET['idCategoria'] ?? 0); // 0 = todas las categorías

/* busca ci, nombre, apellido y categoría de los jugadores del club (y de la categoría, si se eligió) */
$sql = "SELECT j.cedula, j.nombre, j.apellido, c.`año` AS categoria
        FROM jugador j
        JOIN categoria c ON c.idCategoria = j.idCategoria
        WHERE j.idClub = ?"
     . ($idCategoria > 0 ? " AND j.idCategoria = ?" : "")
     . " ORDER BY j.apellido, j.nombre";

$stmt = mysqli_prepare($conexion, $sql);
if ($idCategoria > 0) {
    mysqli_stmt_bind_param($stmt, "ii", $idClub, $idCategoria);
} else {
    mysqli_stmt_bind_param($stmt, "i", $idClub);
}
mysqli_stmt_execute($stmt);
$jugadores = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

/* primera opción: obliga a elegir un jugador en vez de dejar marcado el primero de la lista */
echo '<option value="" disabled selected>'
   . ($jugadores ? 'Seleccionar jugador sancionado' : 'No hay jugadores para esa selección')
   . '</option>';

/* arma un option de html por cada jugador */
foreach ($jugadores as $jugador) {
    echo '<option value="' . htmlspecialchars($jugador['cedula']) . '">'
        . htmlspecialchars($jugador['nombre'] . ' ' . $jugador['apellido'])
        . ' - CI ' . htmlspecialchars($jugador['cedula'])
        . ' (' . htmlspecialchars($jugador['categoria']) . ')'
        . '</option>';
}