<?php
// Punto de entrada principal de la aplicación
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Iniciar sesión
session_start();

// Determinar la página a cargar
$page = isset($_GET['page']) ? $_GET['page'] : 'index';

// Incluir el encabezado
include_once 'includes/header.php';

// Cargar la página solicitada
switch ($page) {
    case 'registro':
        include_once 'views/registro.php';
        break;
    case 'listado':
        include_once 'views/listado.php';
        break;
    case 'empleados':
        include_once 'views/empleados.php';
        break;
    default:
        include_once 'views/index.php';
}

// Incluir el pie de página

?>