<?php
// Inicia la sesión antes de generar HTML para poder enviar correctamente la cookie.
session_name('login_session');
session_start();
// Crea un token aleatorio por sesión para validar los formularios enviados.
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
// Lee los mensajes temporales que dejó el procesador del formulario.
$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
// Borra los mensajes después de leerlos para mostrarlos una sola vez.
unset($_SESSION['error'], $_SESSION['exito']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/proyecto-final/css/style.css">
    <link rel="icon" type="image/png" href="img/logo-liga/log-liga-b.png">
</head>
<body>
    <!-- Agrupa los elementos visibles del formulario de inicio de sesión. -->
    <div class="login-container">
        <img src="/proyecto-final/img/logo-liga/log-liga-d.png" alt="Logo" class="logos"> 
        <h1><b>Iniciar sesión</b></h1>
        <p class="nombre">Atrivia Enterprise</p>

        <?php if ($error !== ''): ?>
            <!-- Escapa el aviso antes de imprimirlo en el HTML. -->
            <p class="mensaje-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if ($exito !== ''): ?>
            <!-- Escapa el mensaje de confirmación antes de imprimirlo. -->
            <p class="mensaje-exito"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <!-- Envía lo que puso el usuario, a login.php -->
        <form action="logueo/login.php" method="post">
            <!-- Indica al procesador que esta solicitud corresponde al inicio de sesión. -->
            <input type="hidden" name="accion" value="ingresar">
            <!-- Incluye el token de sesión para evitar solicitudes falsificadas. -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <!-- Recibe el usuario sin superar el tamaño de la columna actual. -->
            <input class="formulario" type="text" name="nombre" placeholder="Usuario" maxlength="20" autocomplete="username" required>
            <!-- Recibe la contraseña con el autocompletado apropiado para login. -->
            <input class="formulario" type="password" name="contraseña" placeholder="Contraseña" maxlength="20" autocomplete="current-password" required>
            <!-- Envía las credenciales de acceso. -->
            <button class="botonlogin" type="submit">Ingresar</button>
        </form>

        <!-- Expone el cambio de credenciales solo después de comprobar las actuales. -->
        <details class="cambio-credenciales">
            <!-- Permite mostrar u ocultar el formulario de actualización. -->
            <summary>Cambiar usuario o contraseña de administrador</summary>
            <!-- Envía la actualización al mismo procesador seguro del login. -->
            <form action="logueo/login.php" method="post">
                <!-- Indica la acción que debe procesar el servidor. -->
                <input type="hidden" name="accion" value="cambiar_credenciales">
                <!-- Reutiliza el token de sesión para proteger este formulario. -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <!-- Solicita el usuario actual para autenticar al administrador. -->
                <input class="formulario" type="text" name="usuario_actual" placeholder="Usuario actual" maxlength="20" autocomplete="username" required>
                <!-- Solicita la contraseña actual antes de permitir cambios. -->
                <input class="formulario" type="password" name="contrasena_actual" placeholder="Contraseña actual" maxlength="20" autocomplete="current-password" required>
                <!-- Recibe el nombre de usuario nuevo dentro del límite de la base de datos. -->
                <input class="formulario" type="text" name="nuevo_usuario" placeholder="Nuevo usuario" maxlength="20" autocomplete="username" required>
                <!-- Solicita una contraseña nueva de al menos ocho caracteres. -->
                <input class="formulario" type="password" name="nueva_contrasena" placeholder="Nueva contraseña (8 a 20 caracteres)" minlength="8" maxlength="20" autocomplete="new-password" required>
                <!-- Confirma la contraseña nueva para evitar errores de escritura. -->
                <input class="formulario" type="password" name="confirmar_contrasena" placeholder="Confirmar nueva contraseña" minlength="8" maxlength="20" autocomplete="new-password" required>
                <!-- Envía la solicitud de cambio de credenciales. -->
                <button class="botonlogin" type="submit">Actualizar credenciales</button>
            </form>
        </details>
    </div>    
</body>
</html>