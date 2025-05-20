<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');      // Host de la base de datos
define('DB_USER', 'root');        // Usuario de la base de datos (cambiar según tu configuración)
define('DB_PASS', '');     // Contraseña (cambiar según tu configuración)
define('DB_NAME', 'registro_visitas'); // Nombre de la base de datos

// Conexión a la base de datos
function conectarDB() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
    
    // Establecer juego de caracteres
    $conexion->set_charset("utf8");
    
    return $conexion;
}

// Cerrar conexión
function cerrarConexion($conexion) {
    $conexion->close();
}
?>