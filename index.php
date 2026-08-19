<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Carga Bootstrap, metadatos, estilos e iconos de la pantalla de acceso. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/proyecto-final/css/style.css">
    <link rel="icon" type="image/png" href="/proyecto-final/img/copa.png">
</head>

<body>
    <!-- Agrupa los elementos visibles del formulario de inicio de sesión. -->
    <div class="login-container">
        <img src="/proyecto-final/img/logo.png" alt="Logo" class="logos">
        <img src="/proyecto-final/img/log.png" alt="Logo" class="logos">
        <h1><b>Login al sistema de control de la liga</b></h1>
        <p class="nombre">Atrivia Enterprises</p>

        <?php
        // Inicia la sesión para consultar y limpiar los errores de acceso.
        session_start();
        if (isset($_SESSION['error'])) {
            // Muestra un aviso y evita que reaparezca en futuras visitas.
            echo "<p style='color:red; text-align:center; font-weight:bold;'>Usuario o contraseña incorrectos.</p>";
            unset($_SESSION['error']);
        }
        ?>

        <!-- Envía las credenciales al controlador de autenticación. -->
        <form action="logueo/login.php" method="post">
            <input class="formulario" type="text" name="nombre" placeholder="Usuario" required>
            <input class="formulario" type="password" name="contraseña" placeholder="Contraseña" required>
            <button class="botonlogin" type="submit">Login</button>
        </form>
    </div>    
</body>
</html>