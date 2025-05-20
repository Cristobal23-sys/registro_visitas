<?php
// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_visita'])) {
    $nombre = limpiarDato($_POST['nombre']);
    $rut = limpiarDato($_POST['rut']);
    $empresa = limpiarDato($_POST['empresa']);
    $motivo = limpiarDato($_POST['motivo']);
    $empleadoId = limpiarDato($_POST['empleado_id']);
    $patente = limpiarDato($_POST['patente']);
    $patente = strtoupper($patente); // Convertir patente a mayúsculas
    $patente = preg_replace('/[^A-Z0-9]/', '', $patente); // Eliminar caracteres no válidos
    $patente = substr($patente, 0, 6); // Limitar a 6 caracteres
    $patente = strtoupper($patente); // Asegurarse de que la patente esté en mayúsculas


    // Validaciones
    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre del visitante es obligatorio";
    }

    if (empty($rut)) {
        $errores[] = "El RUT es obligatorio";
    } elseif (!validarRut($rut)) {
        $errores[] = "El formato del RUT no es válido";
    }

    if (empty($empresa)) {
        $errores[] = "La empresa es obligatoria";
    }
    if (empty($patente)) {
        $errores[] = "La patente es obligatoria";
    } elseif (!preg_match('/^([A-Z]{4}[0-9]{2}|[A-Z]{2}[0-9]{4})$/', $patente)) {
        $errores[] = "El formato de la patente no es válido";
    }
    if (strlen($patente) > 6) {
        $errores[] = "La patente no puede exceder los 6 caracteres";
    }
    if (empty($motivo)) {
        $errores[] = "El motivo de la visita es obligatorio";
    }

    if (empty($empleadoId)) {
        $errores[] = "Debe seleccionar a quién visita";
    }

    // Si no hay errores, registrar la visita
    if (empty($errores)) {
        $registroId = registrarVisita($nombre, $rut, $empresa, $patente, $motivo, $empleadoId);

        if ($registroId) {
            $_SESSION['mensaje'] = "Visita registrada correctamente";
            $_SESSION['tipo_mensaje'] = "green";
            header("Location: index.php?page=listado");
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al registrar la visita";
            $_SESSION['tipo_mensaje'] = "red";
        }
    }
}

// Obtener lista de empleados para el formulario
$empleados = obtenerEmpleados();
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-4xl font-bold mb-6 text-center">Registrar nueva visita</h2>

    <?php if (!empty($errores)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">Se encontraron los siguientes errores:</p>
            <ul class="list-disc list-inside">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?page=registro" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <h3 class="text-2xl font-semibold mb-4 text-center">Datos del visitante</h3>
        </div>

        <!-- Nombre del visitante -->
        <div>
            <label for="nombre" class="block text-gray-700 font-semibold mb-2">Nombre completo:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan Pérez"
                class="mayus w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>" required>
        </div>

        <!-- RUT -->
        <div>
            <label for="rut" class="block text-gray-700 font-semibold mb-2">RUT:</label>
            <input type="text" id="rut" name="rut" placeholder="Ej: 123456789"
                class="w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                value="<?php echo isset($rut) ? htmlspecialchars($rut) : ''; ?>" oninput="validarRutInput(this)"
                maxlength="12" required>
            <p id="rut-error" class="text-sm text-red-500 hidden">RUT inválido</p>
        </div>

        <!-- Empresa origen -->
        <div>
            <label for="empresa" class="block text-gray-700 font-semibold mb-2">Empresa:</label>
            <input type="text" id="empresa" name="empresa" placeholder="Ej: Empresa S.A."
                class="mayus w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                value="<?php echo isset($empresa) ? htmlspecialchars($empresa) : ''; ?>" required>
        </div>

        <div>
            <label for="patente" class="block text-gray-700 font-semibold mb-2">Patente Vehículo:</label>
            <input type="text" id="patente" name="patente" placeholder="Ej: RJZS12"
                class="mayus w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                value="<?php echo isset($patente) ? htmlspecialchars($patente) : ''; ?>" required>
        </div>


        <!-- A quién visita -->
        <div>
            <label for="empleado_id" class="block text-gray-700 font-semibold mb-2">Persona a quien visita:</label>
            <select id="empleado_id" name="empleado_id"
                class="w-full h-10 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                required>
                <option value="">Seleccione un empleado</option>
                <?php foreach ($empleados as $empleado): ?>
                    <option value="<?php echo $empleado['id']; ?>" <?php echo (isset($empleadoId) && $empleadoId == $empleado['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($empleado['nombre']); ?>
                        (<?php echo htmlspecialchars($empleado['departamento']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Motivo de la visita -->
        <div>
            <label for="motivo" class="block text-gray-700 font-semibold mb-2">Motivo de la visita:</label>
            <textarea id="motivo" name="motivo" rows="3" placeholder="Ej: Reunión de trabajo"
                class="w-full h-40 rounded-md border-2 border-gray-300 shadow-md focus:border-blue-500 focus:ring focus:ring-blue-500 transition-colors"
                required><?php echo isset($motivo) ? htmlspecialchars($motivo) : ''; ?></textarea>
        </div>

        <!-- Botones de acción -->
        <div class="md:col-span-2 flex justify-end space-x-4">
            <a href="index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md">
                Cancelar
            </a>
            <button type="submit" name="registrar_visita"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md">
                <i class="fas fa-save mr-2"></i> Registrar Visita
            </button>
        </div>
    </form>
</div>
<script>
    //convertir a mayúsculas los campos de nombre, empresa y patente
    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll(".mayus");

        inputs.forEach(function (input) {
            input.addEventListener("input", function () {
                this.value = this.value.toUpperCase();
            });
        });
    });
    
    // Función para autocompletar empresa al ingresar RUT
    document.getElementById('rut').addEventListener('blur', function() {
        // Esta función podría conectarse a una API o base de datos para buscar información
        // Por ahora es solo un ejemplo que puedes ampliar en el futuro
        const rut = this.value.trim();
        if (rut && localStorage.getItem('empresa_' + rut)) {
            document.getElementById('empresa').value = localStorage.getItem('empresa_' + rut);
        }
    });

    // Guardar empresa asociada a RUT para futuras visitas
    document.querySelector('form').addEventListener('submit', function() {
        const rut = document.getElementById('rut').value.trim();
        const empresa = document.getElementById('empresa').value.trim();

        if (rut && empresa) {
            localStorage.setItem('empresa_' + rut, empresa);
        }
    });

    function validarRutInput(input) {
        // 1. Eliminar caracteres no válidos y convertir a mayúscula
        let rut = input.value.replace(/[^0-9kK]/g, '').toUpperCase();

        // 2. Separar número (8 dígitos máx) y DV (1 carácter)
        let numero = rut.slice(0, 8); // Cortar a 8 dígitos
        let dv = rut.slice(8, 9); // Solo el primer carácter después del número

        // 3. Formatear con puntos y guion
        if (numero.length > 0) {
            // Agregar puntos cada 3 dígitos
            let numeroFormateado = numero.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            input.value = numeroFormateado + (dv ? '-' + dv : '');
        } else {
            input.value = '';
        }

        // 4. Validar (opcional: cambiar estilo si es inválido)
        const esValido = validarRutJS(input.value);
        input.classList.toggle('border-red-500', !esValido);
        input.classList.toggle('border-green-500', esValido);
    }

    // Función para validar RUT en JavaScript (similar a tu PHP)
    function validarRutJS(rut) {
        rut = rut.replace(/[^0-9kK]/g, '').toUpperCase();
        if (rut.length < 2) return false;

        const numero = rut.slice(0, -1);
        const dv = rut.slice(-1);

        // Calcular DV esperado
        let suma = 0;
        let factor = 2;
        for (let i = numero.length - 1; i >= 0; i--) {
            suma += factor * parseInt(numero[i]);
            factor = factor === 7 ? 2 : factor + 1;
        }
        const dvEsperado = 11 - (suma % 11);
        const dvCalculado = dvEsperado === 11 ? '0' : dvEsperado === 10 ? 'K' : dvEsperado.toString();

        return dv === dvCalculado.toString();
    }
</script>