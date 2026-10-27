<?php
// Carga la conexión compartida con la base de datos de la liga.
require_once __DIR__ . '/../conexion-bd/conexion.php';

// Usa la sesión temporal del login para conservar el token y los mensajes.
session_name('login_session');
session_start();

// Define una redirección única para los mensajes y para evitar reenviar formularios.
function volverAlLogin(string $tipo, string $mensaje): void
{
    // Guarda el mensaje para mostrarlo una sola vez en la pantalla de login.
    $_SESSION[$tipo] = $mensaje;
    // Regresa al formulario sin exponer datos enviados en la URL.
    header('Location: ../index.php');
    // Detiene el procesamiento después de enviar la redirección.
    exit;
}

// Acepta únicamente solicitudes POST con un token CSRF válido.
$tokenPublicado = $_POST['csrf_token'] ?? '';
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !is_string($tokenPublicado)
    || !isset($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $tokenPublicado)
) {
    // Informa el rechazo sin continuar con ninguna consulta.
    volverAlLogin('error', 'La solicitud venció. Intenta nuevamente.');
}

// Abre la conexión solo después de comprobar el método y el token.
$conexion = connection();
// Lee la acción solicitada y usa el login normal como comportamiento predeterminado.
$accion = $_POST['accion'] ?? 'ingresar';

// Procesa el cambio de credenciales del administrador cuando se solicita.
if ($accion === 'cambiar_credenciales') {
    // Recupera las credenciales actuales y las nuevas, verificando el tipo recibido.
    $usuarioActual = $_POST['usuario_actual'] ?? '';
    $contrasenaActual = $_POST['contrasena_actual'] ?? '';
    $nuevoUsuario = $_POST['nuevo_usuario'] ?? '';
    $nuevaContrasena = $_POST['nueva_contrasena'] ?? '';
    $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';

    // Rechaza valores que no sean texto para evitar errores al validar y consultar.
    if (
        !is_string($usuarioActual)
        || !is_string($contrasenaActual)
        || !is_string($nuevoUsuario)
        || !is_string($nuevaContrasena)
        || !is_string($confirmarContrasena)
    ) {
        // Conserva una respuesta clara ante campos manipulados.
        volverAlLogin('error', 'Los datos enviados no son válidos.');
    }

    // Respeta los tamaños de las columnas existentes y exige una contraseña nueva más robusta.
    if (
        strlen($usuarioActual) > 20
        || strlen($contrasenaActual) > 20
        || $nuevoUsuario === ''
        || strlen($nuevoUsuario) > 20
        || strlen($nuevaContrasena) < 8
        || strlen($nuevaContrasena) > 20
        || !hash_equals($nuevaContrasena, $confirmarContrasena)
    ) {
        // Evita truncamientos y exige que la contraseña nueva coincida con su confirmación.
        volverAlLogin('error', 'Revisa el usuario y confirma una contraseña nueva de 8 a 20 caracteres.');
    }

    // Comprueba que las credenciales actuales correspondan a una cuenta de administrador.
    $consultaAdmin = $conexion->prepare(
        'SELECT idAdmin FROM administrador WHERE nombreUsuario = ? AND contraseña = ? LIMIT 1'
    );
    // Asocia los valores como texto para evitar inyección SQL.
    $consultaAdmin->bind_param('ss', $usuarioActual, $contrasenaActual);
    // Ejecuta la consulta con los parámetros ya separados del SQL.
    $consultaAdmin->execute();
    // Recupera como máximo una fila, pues la consulta limita el resultado.
    $admin = $consultaAdmin->get_result()->fetch_assoc();
    // Cierra la consulta antes de iniciar la actualización.
    $consultaAdmin->close();

    // Impide que alguien cambie credenciales sin conocer las actuales.
    if (!$admin) {
        // Usa un mensaje genérico para no revelar qué credencial era incorrecta.
        volverAlLogin('error', 'No se pudieron verificar las credenciales actuales.');
    }

    // Comprueba que el nuevo usuario no pertenezca a otro administrador.
    $consultaUsuario = $conexion->prepare(
        'SELECT idAdmin FROM administrador WHERE nombreUsuario = ? AND idAdmin <> ? LIMIT 1'
    );
    // Asocia el nombre nuevo y excluye la cuenta que se está modificando.
    $consultaUsuario->bind_param('si', $nuevoUsuario, $admin['idAdmin']);
    // Ejecuta la comprobación de unicidad antes de actualizar.
    $consultaUsuario->execute();
    // Determina si el nombre ya está ocupado.
    $usuarioExiste = $consultaUsuario->get_result()->fetch_assoc();
    // Libera los recursos de la consulta de unicidad.
    $consultaUsuario->close();

    // Evita que dos administradores terminen con el mismo nombre de usuario.
    if ($usuarioExiste) {
        // Solicita elegir un usuario diferente.
        volverAlLogin('error', 'Ese nombre de usuario ya está en uso.');
    }

    // Actualiza únicamente la cuenta autenticada con las credenciales nuevas.
    $actualizarAdmin = $conexion->prepare(
        'UPDATE administrador SET nombreUsuario = ?, contraseña = ? WHERE idAdmin = ?'
    );
    // Asocia los nuevos datos y el identificador comprobado de la cuenta.
    $actualizarAdmin->bind_param('ssi', $nuevoUsuario, $nuevaContrasena, $admin['idAdmin']);
    // Ejecuta la actualización parametrizada en la base de datos.
    $actualizarAdmin->execute();
    // Cierra la consulta de actualización para liberar recursos.
    $actualizarAdmin->close();

    // Informa el cambio y solicita autenticarse nuevamente con los nuevos datos.
    volverAlLogin('exito', 'Las credenciales se actualizaron. Inicia sesión con los nuevos datos.');
}

// Rechaza acciones desconocidas para que no se interpreten como otro tipo de operación.
if ($accion !== 'ingresar') {
    // Devuelve al usuario al inicio con un mensaje explícito.
    volverAlLogin('error', 'La acción solicitada no es válida.');
}

// Obtiene las credenciales del formulario de acceso.
$nombre = $_POST['nombre'] ?? '';
$contrasena = $_POST['contraseña'] ?? '';

// Valida el tipo y el contenido antes de consultar la base de datos.
if (
    !is_string($nombre)
    || !is_string($contrasena)
    || $nombre === ''
    || $contrasena === ''
) {
    // Reutiliza el mismo mensaje para no distinguir usuarios de contraseñas.
    volverAlLogin('error', 'Usuario o contraseña incorrectos.');
}

// Busca una cuenta de administrador con una consulta preparada.
$consultaAdmin = $conexion->prepare(
    'SELECT idAdmin FROM administrador WHERE nombreUsuario = ? AND contraseña = ? LIMIT 1'
);
// Asocia las credenciales como texto en lugar de concatenarlas en el SQL.
$consultaAdmin->bind_param('ss', $nombre, $contrasena);
// Ejecuta la búsqueda del administrador.
$consultaAdmin->execute();
// Recupera el identificador si las credenciales coinciden.
$admin = $consultaAdmin->get_result()->fetch_assoc();
// Libera los recursos de la consulta de administrador.
$consultaAdmin->close();

// Inicia una sesión administrativa si existe una coincidencia.
if ($admin) {
    // Guarda y cierra la sesión temporal antes de cambiar al nombre de sesión administrativa.
    session_write_close();
    // Selecciona la cookie de sesión propia del rol administrador.
    session_name('admin_session');
    // Inicia la sesión del administrador.
    session_start();
    // Renueva el identificador para reducir el riesgo de fijación de sesión.
    session_regenerate_id(true);
    // Guarda el nombre de usuario autenticado.
    $_SESSION['nombre'] = $nombre;
    // Guarda el identificador usado por las operaciones administrativas.
    $_SESSION['idAdmin'] = $admin['idAdmin'];
    // Envía al panel de administración.
    header('Location: ../admin/indexadmin.php');
    // Detiene la ejecución después de iniciar sesión.
    exit();
}

// Busca una cuenta de club sin afectar el flujo de autenticación existente.
$consultaClub = $conexion->prepare(
    'SELECT idClub, nombreClub FROM club WHERE nombreClub = ? AND contraseñaClub = ? LIMIT 1'
);
// Asocia las credenciales con parámetros para evitar inyección SQL.
$consultaClub->bind_param('ss', $nombre, $contrasena);
// Ejecuta la búsqueda del club.
$consultaClub->execute();
// Recupera el identificador y nombre del club autenticado.
$club = $consultaClub->get_result()->fetch_assoc();
// Libera los recursos de la consulta del club.
$consultaClub->close();

// Inicia una sesión de club si existe una coincidencia.
if ($club) {
    // Guarda y cierra la sesión temporal antes de cambiar al nombre de sesión del club.
    session_write_close();
    // Selecciona la cookie de sesión correspondiente al rol club.
    session_name('club_session');
    // Inicia la sesión del club.
    session_start();
    // Renueva el identificador para reducir el riesgo de fijación de sesión.
    session_regenerate_id(true);
    // Guarda el nombre del club para el resto de la aplicación.
    $_SESSION['nombre'] = $club['nombreClub'];
    // Guarda el identificador para las operaciones del club.
    $_SESSION['idClub'] = $club['idClub'];
    // Envía al panel del club.
    header('Location: ../club/indexclub.php');
    // Detiene la ejecución después de iniciar sesión.
    exit();
}

// Informa el fallo de autenticación sin revelar qué cuenta no coincidió.
volverAlLogin('error', 'Usuario o contraseña incorrectos.');
