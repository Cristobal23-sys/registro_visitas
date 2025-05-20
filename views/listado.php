<?php
// Procesar registro de salida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_salida'])) {
    $visitaId = limpiarDato($_POST['visita_id']);

    if (registrarSalida($visitaId, date('Y-m-d H:i:s'))) {
        $_SESSION['mensaje'] = "Salida registrada correctamente";
        $_SESSION['tipo_mensaje'] = "green";
    } else {
        $_SESSION['mensaje'] = "Error al registrar la salida";
        $_SESSION['tipo_mensaje'] = "red";
    }

    // Redireccionar para evitar reenvío de formulario
    header("Location: index.php?page=listado");
    exit;
}

// Obtener lista de visitas activas
$visitasActivas = obtenerVisitasActivas();

// Obtener lista de empleados para filtro
$empleados = obtenerEmpleados();
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Visitas Activas</h2>
        <a href="index.php?page=registro" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md">
            <i class="fas fa-plus mr-2"></i> Nueva Visita
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="bg-gray-50 p-4 rounded-md mb-6">
        <form action="" method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="hidden" name="page" value="listado">

            <div>
                <label for="buscar" class="block text-gray-700 font-semibold mb-2">Buscar:</label>
                <input type="text" id="buscar" name="buscar" placeholder="Nombre, RUT o empresa..."
                    class="w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                    value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
            </div>

            <div>
                <label for="empleado_filtro" class="block text-gray-700 font-semibold mb-2">Empleado visitado:</label>
                <select id="empleado_filtro" name="empleado_filtro"
                    class="w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors">
                    <option value="">Todos los empleados</option>
                    <?php foreach ($empleados as $empleado): ?>
                        <option value="<?php echo $empleado['id']; ?>"
                            <?php echo (isset($_GET['empleado_filtro']) && $_GET['empleado_filtro'] == $empleado['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($empleado['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
                <a href="index.php?page=listado" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <?php if (!empty($visitasActivas)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-3 px-4 border-b text-left">Visitante</th>
                        <th class="py-3 px-4 border-b text-left">RUT</th>
                        <th class="py-3 px-4 border-b text-left">Empresa</th>
                        <th class="py-3 px-4 border-b text-left">Patente</th>
                        <th class="py-3 px-4 border-b text-left">Visita a</th>
                        <th class="py-3 px-4 border-b text-left">Motivo</th>
                        <th class="py-3 px-4 border-b text-left">Entrada</th>
                        <th class="py-3 px-4 border-b text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($visitasActivas as $visita):
                        // Aplicar filtros de búsqueda si existen
                        if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
                            $busqueda = strtolower($_GET['buscar']);
                            $nombre = strtolower($visita['nombre_visitante']);
                            $rut = strtolower($visita['rut']);
                            $empresa = strtolower($visita['empresa_origen']);

                            if (
                                strpos($nombre, $busqueda) === false &&
                                strpos($rut, $busqueda) === false &&
                                strpos($empresa, $busqueda) === false
                            ) {
                                continue;
                            }
                        }

                        // Filtrar por empleado si está seleccionado
                        if (isset($_GET['empleado_filtro']) && !empty($_GET['empleado_filtro'])) {
                            if ($visita['empleado_id'] != $_GET['empleado_filtro']) {
                                continue;
                            }
                        }
                    ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($visita['nombre_visitante']); ?></td>
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($visita['rut']); ?></td>
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($visita['empresa_origen']); ?></td>
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($visita['patente']); ?></td>
                            <td class="py-3 px-4 border-b">
                                <?php echo htmlspecialchars($visita['nombre_empleado']); ?>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($visita['departamento']); ?></div>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <?php
                                // Mostrar sólo los primeros 30 caracteres del motivo
                                $motivoCorto = strlen($visita['motivo']) > 30 ?
                                    substr($visita['motivo'], 0, 30) . '...' :
                                    $visita['motivo'];
                                echo htmlspecialchars($motivoCorto);
                                ?>
                                <?php if (strlen($visita['motivo']) > 30): ?>
                                    <div class="relative" x-data="{ tooltip: false }">
                                        <button @mouseenter="tooltip = true" @mouseleave="tooltip = false" class="text-blue-600 text-xs">
                                            Ver más
                                        </button>
                                        <div x-show="tooltip" class="absolute z-10 w-64 p-2 mt-2 text-sm bg-gray-800 text-white rounded-md shadow-lg" x-cloak>
                                            <?php echo htmlspecialchars($visita['motivo']); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <?php echo date('d/m/Y H:i', strtotime($visita['fecha_entrada'])); ?>
                                <div class="text-xs text-gray-500">
                                    <?php
                                    // Calcular tiempo transcurrido
                                    $entrada = new DateTime($visita['fecha_entrada']);
                                    $ahora = new DateTime();
                                    $intervalo = $entrada->diff($ahora);

                                    if ($intervalo->days > 0) {
                                        echo $intervalo->format('%d días %h horas');
                                    } else {
                                        echo $intervalo->format('%h horas %i min');
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="py-3 px-4 border-b text-center">
                                <form method="POST" action="index.php?page=listado">
                                    <input type="hidden" name="visita_id" value="<?php echo $visita['id']; ?>">
                                    <button type="submit" name="registrar_salida"
                                        onclick="return confirmarAccion('¿Está seguro de registrar la salida?')"
                                        class="bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded-md text-sm">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Registrar Salida
                                    </button>
                                </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
            <p>No hay visitas activas en este momento.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Alpine.js para tooltips -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
<script>
function confirmarAccion(mensaje) {
    return confirm(mensaje);
}
</script>
