<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Registro de Visitas</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Sistema de Registro de Visitas</h1>
            </div>
        </div>
    </header>
    
    <nav class="bg-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex space-x-4 py-2">
                <a href="index.php" class="hover:bg-blue-700 px-3 py-2 rounded">
                    <i class="fas fa-home mr-1"></i> Inicio
                </a>
                <a href="index.php?page=registro" class="hover:bg-blue-700 px-3 py-2 rounded">
                    <i class="fas fa-user-plus mr-1"></i> Nuevo Registro
                </a>
                <a href="index.php?page=listado" class="hover:bg-blue-700 px-3 py-2 rounded">
                    <i class="fas fa-clipboard-list mr-1"></i> Visitas Activas
                </a>
                <a href="index.php?page=empleados" class="hover:bg-blue-700 px-3 py-2 rounded">
                    <i class="fas fa-users mr-1"></i> Gestionar Empleados
                </a>
            </div>
        </div>
    </nav>
    
    <main class="container mx-auto px-4 py-8"><?php if(isset($_SESSION['mensaje'])): ?>
        <div class="bg-<?php echo $_SESSION['tipo_mensaje']; ?>-100 border-l-4 border-<?php echo $_SESSION['tipo_mensaje']; ?>-500 text-<?php echo $_SESSION['tipo_mensaje']; ?>-700 p-4 mb-6" role="alert">
            <p><?php echo $_SESSION['mensaje']; ?></p>
        </div>
        <?php 
            // Limpiar el mensaje después de mostrarlo
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        endif; ?>