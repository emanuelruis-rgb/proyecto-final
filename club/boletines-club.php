<?php
require_once __DIR__ . '/../conexion-bd/conexion.php';
require_once __DIR__ . '/boletines-data.php';

// Carga todos los boletines para la vista completa.
$conexion = connection();
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
	<link rel="stylesheet" href="pagina-principal-club.css?v=5">
	<link rel="icon" type="image/png" href="../img/copa.png">
</head>
<body>
	<header>
		<nav class="nav-izquierda-container">
			<a href="indexclub.php">
				<img src="/proyecto-final/img/logo-empresa/logo-empresa-blanco.png" alt="Logo" class="logo-empresa">
			</a>
			<div class="nav-izquierda-botones-container">
				<a href="jugadores-club.php" class="nav-izquierda-botones">Jugadores</a>
				<a href="boletines-club.php" class="nav-izquierda-botones active" aria-current="page">Boletines</a>
			</div>
		</nav>

		<div class="nav-derecha-container">
			<a href="#" class="nav-derecha-item" aria-label="Notificaciones">
				<i class="bi bi-bell" aria-hidden="true"></i>
			</a>
			<a href="#" class="nav-derecha-item" aria-label="Perfil">
				<i class="bi bi-person-circle" aria-hidden="true"></i>
			</a>
			<a href="/proyecto-final/index.php" class="nav-derecha-item">
				<i class="bi bi-box-arrow-right" aria-hidden="true"></i>
				<span>Cerrar sesión</span>
			</a>
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
