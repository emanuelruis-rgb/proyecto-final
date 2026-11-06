
<?php
$hash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contrasena = $_POST['contrasena'] ?? '';

    if (is_string($contrasena) && $contrasena !== '') {
        // esta línea es la que genera el hash de la contraseña. usa método password_default.
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generador de hashes</title>
</head>
<body>
    <h1>Generar hash de contraseña</h1>

    <p>Esto es de uso interno para nosotros los devs. <br>
    acá lo que hay que hacer es poner la contraseña y ahí genera el hash. ese hash se inserta manualmente en la bd.
    <br>
    todos los clubes y usuarios van manualmente, los otros datos van manualmente, acá solo se hace el hash de la contraseña.
    </p>

    <form method="post">
        <label>Contraseña:</label>
        <input type="password" name="contrasena" required>
        <button type="submit">Generar hash</button>
    </form>

    <?php if ($hash !== ''): ?>
        <h3>Hash generado:</h3>
        <input type="text" value="<?= htmlspecialchars($hash, ENT_QUOTES, 'UTF-8') ?>" readonly>
    <?php endif; ?>
</body>
</html>
