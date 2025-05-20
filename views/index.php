<?php
// Obtener estadísticas básicas
$db = conectarDB();

// Establece la zona horaria local (por ejemplo, América/Mexico_City)
date_default_timezone_set('America/Santiago');

// Obtiene la fecha local actual en formato YYYY-MM-DD
$fechaLocalHoy = date('Y-m-d');

// Consulta con fecha local
$queryHoy = "SELECT COUNT(*) as total FROM visitas WHERE DATE(fecha_entrada) = ?";
$stmt = $db->prepare($queryHoy);
$stmt->bind_param("s", $fechaLocalHoy);
$stmt->execute();
$result = $stmt->get_result();
$visitasHoy = $result->fetch_assoc()['total'];


// Visitas activas (sin salida)
$queryActivas = "SELECT COUNT(*) as total FROM visitas WHERE fecha_salida IS NULL";
$resultActivas = $db->query($queryActivas);
$visitasActivas = $resultActivas->fetch_assoc()['total'];

// Total empleados
$queryEmpleados = "SELECT COUNT(*) as total FROM empleados WHERE activo = 1";
$resultEmpleados = $db->query($queryEmpleados);
$totalEmpleados = $resultEmpleados->fetch_assoc()['total'];

// Últimas 5 visitas
$filtros = ['limite' => 5];
$ultimasVisitas = obtenerVisitas($filtros);

cerrarConexion($db);
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Tarjeta de visitas hoy -->
    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
        <div class="rounded-full bg-blue-100 p-3 mr-4">
            <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
        </div>
        <div>
            <h3 class="text-xl font-semibold">Visitas hoy</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $visitasHoy; ?></p>
        </div>
    </div>

    <!-- Tarjeta de visitas activas -->
    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
        <div class="rounded-full bg-green-100 p-3 mr-4">
            <i class="fas fa-user-check text-green-600 text-xl"></i>
        </div>
        <div>
            <h3 class="text-xl font-semibold">Visitas activas</h3>
            <p class="text-3xl font-bold text-green-600"><?php echo $visitasActivas; ?></p>
        </div>
    </div>

    <!-- Tarjeta de total empleados -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="rounded-full bg-blue-100 p-3 mr-4">
                <i class="fas fa-clock text-blue-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-semibold">Fecha y Hora</h3>
                <p id="fecha" class="text-lg text-gray-600"></p>
                <p id="reloj" class="text-3xl font-bold text-blue-600">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Últimas visitas -->
    <div class="bg-white rounded-lg shadow-md p-6 md:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Últimas visitas</h2>
            <a href="index.php?page=listado" class="text-blue-600 hover:underline">Ver todas</a>
        </div>

        <?php if (count($ultimasVisitas) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b text-left">Visitante</th>
                            <th class="py-2 px-4 border-b text-left">Empresa</th>
                            <th class="py-2 px-4 border-b text-left">Visita a</th>
                            <th class="py-2 px-4 border-b text-left">Fecha</th>
                            <th class="py-2 px-4 border-b text-left">Entrada</th>
                            <th class="py-2 px-4 border-b text-left">Salida</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimasVisitas as $visita): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($visita['nombre_visitante']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($visita['empresa_origen']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($visita['nombre_empleado']); ?></td>
                                <td class="py-2 px-4 border-b">
                                    <?php echo date('d/m/Y', strtotime($visita['fecha_entrada'])); ?>
                                <td class="py-2 px-4 border-b">
                                    <?php echo date('H:i', strtotime($visita['fecha_entrada'])); ?>
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <?php
                                    if ($visita['fecha_salida'] === null) {
                                        echo "-";
                                    } else {
                                        echo date('H:i', strtotime($visita['fecha_salida']));
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-500 italic">No hay visitas registradas recientemente.</p>
        <?php endif; ?>
    </div>

    <!-- Accesos rápidos -->
    <div class="bg-white rounded-lg shadow-md p-6 md:col-span-1">

        <h2 class="text-xl font-bold mb-4">Accesos rápidos</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="index.php?page=registro" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-4 flex items-center">
                <i class="fas fa-user-plus text-2xl mr-3"></i>
                <div>
                    <h3 class="font-bold">Nuevo Registro</h3>
                    <p class="text-sm">Registrar una nueva visita</p>
                </div>
            </a>

            <a href="index.php?page=listado" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-4 flex items-center">
                <i class="fas fa-clipboard-list text-2xl mr-3"></i>
                <div>
                    <h3 class="font-bold">Visitas Activas</h3>
                    <p class="text-sm">Ver visitas sin registrar salida</p>
                </div>
            </a>

            <a href="index.php?page=empleados" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-4 flex items-center">
                <i class="fas fa-users text-2xl mr-3"></i>
                <div>
                    <h3 class="font-bold">Empleados</h3>
                    <p class="text-sm">Gestionar lista de empleados</p>
                </div>
            </a>

            <a href="#" onclick="openExportModal(); return false;" class="bg-gray-600 hover:bg-gray-700 text-white rounded-lg p-4 flex items-center">
                <i class="fas fa-file-excel text-2xl mr-3"></i>
                <div>
                    <h3 class="font-bold">Exportar a Excel</h3>
                    <p class="text-sm">Generar reporte por fechas</p>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- Modal para seleccionar rango de fechas -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Seleccionar rango de fechas</h3>
        <form id="exportForm" action="index.php" method="get">
            <input type="hidden" name="action" value="exportar_excel">

            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="fecha_inicio">Fecha inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="w-full px-3 py-2 border rounded-lg" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 mb-2" for="fecha_fin">Fecha fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="w-full px-3 py-2 border rounded-lg" required>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeExportModal()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Generar Reporte
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openExportModal() {
        // Establecer fecha fin como hoy y fecha inicio como una semana atrás por defecto
        const today = new Date().toISOString().split('T')[0];
        const oneWeekAgo = new Date();
        oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
        const oneWeekAgoStr = oneWeekAgo.toISOString().split('T')[0];

        document.getElementById('fecha_inicio').value = oneWeekAgoStr;
        document.getElementById('fecha_fin').value = today;
        document.getElementById('fecha_inicio').max = today;
        document.getElementById('fecha_fin').max = today;

        document.getElementById('exportModal').classList.remove('hidden');
    }

    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    // Validar que fecha inicio no sea mayor a fecha fin
    document.getElementById('fecha_inicio').addEventListener('change', function() {
        const fechaFin = document.getElementById('fecha_fin');
        if (this.value > fechaFin.value) {
            fechaFin.value = this.value;
        }
    });

    document.getElementById('fecha_fin').addEventListener('change', function() {
        const fechaInicio = document.getElementById('fecha_inicio');
        if (this.value < fechaInicio.value) {
            fechaInicio.value = this.value;
        }
    });
    // Función para actualizar fecha y hora
    function actualizarFechaHora() {
        const ahora = new Date();
        const opcionesFecha = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const fechaFormateada = ahora.toLocaleDateString('es-ES', opcionesFecha);

        const horas = ahora.getHours().toString().padStart(2, '0');
        const minutos = ahora.getMinutes().toString().padStart(2, '0');
        const segundos = ahora.getSeconds().toString().padStart(2, '0');

        document.getElementById('fecha').textContent = fechaFormateada;
        document.getElementById('reloj').textContent = `${horas}:${minutos}:${segundos}`;
    }

    // Actualizar inmediatamente y cada segundo
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
</script>