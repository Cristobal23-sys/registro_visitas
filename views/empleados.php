<?php
// Procesamiento para agregar/editar empleados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = conectarDB();

    // Agregar nuevo empleado
    if (isset($_POST['agregar_empleado'])) {
        $nombre = $db->real_escape_string(limpiarDato($_POST['nombre']));
        $correo = $db->real_escape_string(limpiarDato($_POST['correo']));
        $departamento = $db->real_escape_string(limpiarDato($_POST['departamento']));

        $query = "INSERT INTO empleados (nombre, correo, departamento) VALUES ('$nombre', '$correo', '$departamento')";

        if ($db->query($query)) {
            $_SESSION['mensaje'] = "Empleado agregado correctamente";
            $_SESSION['tipo_mensaje'] = "green";
        } else {
            $_SESSION['mensaje'] = "Error al agregar empleado";
            $_SESSION['tipo_mensaje'] = "red";
        }
    }

    // Editar empleado existente
    if (isset($_POST['editar_empleado'])) {
        $id = $db->real_escape_string(limpiarDato($_POST['id']));
        $nombre = $db->real_escape_string(limpiarDato($_POST['nombre']));
        $correo = $db->real_escape_string(limpiarDato($_POST['correo']));
        $departamento = $db->real_escape_string(limpiarDato($_POST['departamento']));

        $query = "UPDATE empleados SET nombre = '$nombre', correo = '$correo', departamento = '$departamento' WHERE id = '$id'";

        if ($db->query($query)) {
            $_SESSION['mensaje'] = "Empleado actualizado correctamente";
            $_SESSION['tipo_mensaje'] = "green";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar empleado";
            $_SESSION['tipo_mensaje'] = "red";
        }
    }

    // Activar/desactivar empleado
    if (isset($_POST['cambiar_estado'])) {
        $id = $db->real_escape_string(limpiarDato($_POST['id']));
        $accion = $_POST['activo']; // 'activar' o 'desactivar'

        $activo = ($accion === 'activar') ? 1 : 0;

        $query = "UPDATE empleados SET activo = '$activo' WHERE id = '$id'";

        if ($db->query($query)) {
            $_SESSION['mensaje'] = ($activo) ? "Empleado activado correctamente" : "Empleado desactivado correctamente";
            $_SESSION['tipo_mensaje'] = "green";
        } else {
            $_SESSION['mensaje'] = "Error al cambiar estado del empleado";
            $_SESSION['tipo_mensaje'] = "red";
        }
    }

    cerrarConexion($db);

    // Redireccionar para evitar reenvío del formulario
    header("Location: index.php?page=empleados");
    exit;
}

// Obtener todos los empleados (activos e inactivos)
$db = conectarDB();
$query = "SELECT * FROM empleados ORDER BY nombre";
$result = $db->query($query);

$empleados = [];
while ($row = $result->fetch_assoc()) {
    $empleados[] = $row;
}

cerrarConexion($db);
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Formulario para agregar/editar empleado -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-6" id="form_title">Agregar nuevo empleado</h2>

        <form id="empleado_form" method="POST" action="index.php?page=empleados">
            <input type="hidden" id="id" name="id" value="">

            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 font-semibold mb-2">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre"
                    class="w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                    required>
            </div>

            <div class="mb-4">
                <label for="correo" class="block text-gray-700 font-semibold mb-2">Correo electrónico:</label>
                <input type="email" id="correo" name="correo"
                    class="w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                    required>
            </div>

            <div class="mb-6">
                <label for="departamento" class="block text-gray-700 font-semibold mb-2">Departamento:</label>
                <input type="text" id="departamento" name="departamento"
                    class="w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                    required>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" id="btn_cancelar" onclick="resetForm()" class="hidden bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md">
                    Cancelar
                </button>
                <button type="submit" id="btn_guardar" name="agregar_empleado" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md">
                    <i class="fas fa-save mr-2"></i> Guardar
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de empleados -->
    <div class="bg-white rounded-lg shadow-md p-6 md:col-span-2">
        <h2 class="text-xl font-bold mb-6">Lista de empleados</h2>

        <?php if (count($empleados) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-3 px-4 border-b text-left">Nombre</th>
                            <th class="py-3 px-4 border-b text-left">Correo</th>
                            <th class="py-3 px-4 border-b text-left">Departamento</th>
                            <th class="py-3 px-4 border-b text-center">Estado</th>
                            <th class="py-3 px-4 border-b text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleados as $empleado): ?>
                            <tr class="<?php echo $empleado['activo'] ? 'hover:bg-gray-50' : 'bg-gray-100 text-gray-500'; ?>">
                                <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($empleado['nombre']); ?></td>
                                <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($empleado['correo']); ?></td>
                                <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($empleado['departamento']); ?></td>
                                <td class="py-3 px-4 border-b text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $empleado['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $empleado['activo'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 border-b text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button onclick="editarEmpleado(<?php echo htmlspecialchars(json_encode($empleado)); ?>)"
                                            class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-2 rounded-md text-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form method="POST" action="index.php?page=empleados" class="inline">
                                            <input type="hidden" name="id" value="<?php echo $empleado['id']; ?>">
                                            <input type="hidden" name="activo" value="<?php echo $empleado['activo'] ? 'desactivar' : 'activar'; ?>">
                                            <button type="submit" name="cambiar_estado"
                                                class="<?php echo $empleado['activo'] ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600'; ?> text-white py-1 px-2 rounded-md text-sm"
                                                onclick="return confirmarAccion('¿Está seguro de <?php echo $empleado['activo'] ? 'desactivar' : 'activar'; ?> este empleado?')">
                                                <i class="fas <?php echo $empleado['activo'] ? 'fa-user-slash' : 'fa-user-check'; ?>"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 text-yellow-700">
                <p>No hay empleados registrados en el sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Función para editar un empleado
    function editarEmpleado(empleado) {
        // Cambiar el título del formulario
        document.getElementById('form_title').innerText = 'Editar empleado';

        // Completar el formulario con los datos del empleado
        document.getElementById('id').value = empleado.id;
        document.getElementById('nombre').value = empleado.nombre;
        document.getElementById('correo').value = empleado.correo;
        document.getElementById('departamento').value = empleado.departamento;

        // Cambiar el botón de envío
        const btnGuardar = document.getElementById('btn_guardar');
        btnGuardar.name = 'editar_empleado';
        btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i> Actualizar';

        // Mostrar el botón de cancelar
        document.getElementById('btn_cancelar').classList.remove('hidden');

        // Hacer scroll al formulario
        document.getElementById('empleado_form').scrollIntoView({
            behavior: 'smooth'
        });
    }

    // Función para resetear el formulario
    function resetForm() {
        // Restablecer título
        document.getElementById('form_title').innerText = 'Agregar nuevo empleado';

        // Limpiar campos
        document.getElementById('empleado_form').reset();
        document.getElementById('id').value = '';

        // Restablecer botón de envío
        const btnGuardar = document.getElementById('btn_guardar');
        btnGuardar.name = 'agregar_empleado';
        btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i> Guardar';

        // Ocultar botón de cancelar
        document.getElementById('btn_cancelar').classList.add('hidden');
    }

    // Función para confirmar acciones importantes
    function confirmarAccion(mensaje) {
        return confirm(mensaje);
    }
</script>