<?php
session_name('club_session');
session_start();

require_once __DIR__ . '/../conexion-bd/conexion.php';
require_once __DIR__ . '/boletines-data.php';

$conexion = connection();

// Obtiene el nombre del club para reutilizar el menú del inicio.
$nombreSesionClub = $_SESSION['nombre'] ?? '';
$nombreMostrarClub = $nombreSesionClub !== '' ? $nombreSesionClub : 'Club';

if ($nombreSesionClub !== '') {
	$stmt = mysqli_prepare($conexion, 'SELECT nombreClub FROM club WHERE nombreClub = ?');
	mysqli_stmt_bind_param($stmt, 's', $nombreSesionClub);
	mysqli_stmt_execute($stmt);
	$resultado = mysqli_stmt_get_result($stmt);

	if ($fila = mysqli_fetch_assoc($resultado)) {
		$nombreMostrarClub = $fila['nombreClub'];
	}
}

// Carga todos los boletines para la vista completa.
$boletines = $conexion ? obtenerBoletines($conexion) : [];
if ($conexion) {
	// Libera la conexión antes de generar la respuesta HTML.
	mysqli_close($conexion);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Boletines | Club</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="pagina-principal-club.css?v=6">
	<script src="menu-club.js" defer></script>
	<link rel="icon" type="image/png" href="../img/logo-liga/log-liga-b.png">
</head>
<body>
	<header>
		<nav class="nav-izquierda-container">
			<a href="/proyecto-final/club/indexclub.php">
				<img src="/proyecto-final/img/logo-liga/log-liga-d.png" alt="Logo" class="logo-empresa">
			</a>
			<div class="nav-izquierda-botones-container">
				<a href="jugadores-club.php" class="nav-izquierda-botones">Jugadores</a>
			</div>
		</nav>

		<!-- Mantiene la misma identidad visual que la página principal. -->
		<div class="club-header-context" aria-label="Sección actual">
			<span>Portal del club</span>
			<small>Gestión y novedades de la liga</small>
		</div>

		<div class="user-menu">
			<button class="user-menu-toggle" onclick="toggleUserMenu()">
				<i class="bi bi-person-circle"></i>
				<span class="user-menu-name"><?php echo htmlspecialchars($nombreMostrarClub); ?></span>
			</button>

			<div class="user-menu-dropdown" id="userDropdown">
				<a href="../uploads/documentos/reglamento.pdf" class="user-menu-item" download>
					<i class="bi bi-file-earmark-text"></i> Descargar documento
				</a>
				<a href="/proyecto-final/logout.php" class="user-menu-item">
					<i class="bi bi-box-arrow-right"></i>
					<span>Cerrar sesión</span>
				</a>
			</div>
		</div>
	</header>

	<main class="boletines-pagina">
		<div class="boletines-pagina-encabezado">
			<div>
				<p class="boletines-etiqueta">Comunicaciones oficiales</p>
				<h1>Boletines de la liga</h1>
				<p class="boletines-descripcion">Consulta las novedades y comunicados publicados por la administración.</p>
			</div>
			<a href="indexclub.php" class="boletines-volver">
				<i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al inicio
			</a>
		</div>

		<?php if (empty($boletines)): ?>
			<!-- Informa al club cuando administración aún no publicó comunicados. -->
			<section class="boletines-vacio boletines-vacio-grande">
				<i class="bi bi-newspaper" aria-hidden="true"></i>
				<div>
					<h2>No hay boletines publicados</h2>
					<p>Cuando la administración publique un comunicado, aparecerá aquí.</p>
				</div>
			</section>
		<?php else: ?>
			<!-- La vista completa muestra todos los boletines disponibles. -->
			<section class="boletines-lista" aria-label="Lista de boletines">
				<?php foreach ($boletines as $boletin): ?>
					<article class="boletin-completo">
						<div class="boletin-completo-icono">
							<i class="bi bi-megaphone" aria-hidden="true"></i>
						</div>
						<div class="boletin-completo-contenido">
							<p class="boletin-fecha">
								Publicado el <?php echo htmlspecialchars(date('d/m/Y', strtotime($boletin['fechaSubida']))); ?>
							</p>
							<h2><?php echo htmlspecialchars($boletin['titulo']); ?></h2>
						</div>
					</article>
				<?php endforeach; ?>
			</section>
		<?php endif; ?>
	</main>
</body>
</html>
