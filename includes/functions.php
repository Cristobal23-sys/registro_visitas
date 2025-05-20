<?php
// Funciones auxiliares para la aplicación

// Función para sanear entradas de formularios
function limpiarDato($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

// Función para validar RUT chileno
function validarRut($rut) {
    // Eliminar puntos y guiones
    $rut = str_replace(['.', '-'], '', $rut);
    
    // Separar número y dígito verificador
    $numero = substr($rut, 0, -1);
    $dv = strtoupper(substr($rut, -1));
    
    // Calcular dígito verificador
    $factor = 2;
    $suma = 0;
    
    for ($i = strlen($numero) - 1; $i >= 0; $i--) {
        $suma += $factor * intval($numero[$i]);
        $factor = $factor == 7 ? 2 : $factor + 1;
    }
    
    $dvCalculado = 11 - ($suma % 11);
    
    if ($dvCalculado == 11) {
        $dvCalculado = '0';
    } elseif ($dvCalculado == 10) {
        $dvCalculado = 'K';
    } else {
        $dvCalculado = strval($dvCalculado);
    }
    
    // Verificar que el dígito verificador calculado sea igual al entregado
    return $dv == $dvCalculado;
}

// Función para formatear RUT
function formatearRut($rut) {
    // Eliminar caracteres no deseados y dejar solo números y K
    $rut = preg_replace('/[^0-9kK]/', '', $rut);
    
    // Separar número y dígito verificador
    $numero = substr($rut, 0, -1);
    $dv = strtoupper(substr($rut, -1)); // Forzar mayúscula
    
    // Limitar a 8 dígitos para el número (largo máximo de un RUT)
    $numero = substr($numero, 0, 8);
    
    // Formatear con puntos y guion
    $numeroFormateado = number_format($numero, 0, '', '.');
    
    return $numeroFormateado . '-' . $dv;
}


// Función para obtener todos los empleados
function obtenerEmpleados() {
    $db = conectarDB();
    $query = "SELECT * FROM empleados WHERE activo = 1 ORDER BY nombre";
    $result = $db->query($query);
    
    $empleados = [];
    while ($row = $result->fetch_assoc()) {
        $empleados[] = $row;
    }
    
    cerrarConexion($db);
    return $empleados;
}

// Función para obtener datos de un empleado por ID
function obtenerEmpleadoPorId($id) {
    $db = conectarDB();
    $id = $db->real_escape_string($id);
    
    $query = "SELECT * FROM empleados WHERE id = '$id'";
    $result = $db->query($query);
    
    if ($result->num_rows > 0) {
        $empleado = $result->fetch_assoc();
        cerrarConexion($db);
        return $empleado;
    }
    
    cerrarConexion($db);
    return null;
}

// Función para registrar una nueva visita
function registrarVisita($nombre, $rut, $empresa, $patente, $motivo, $empleadoId) {
    $db = conectarDB();
    
    // Sanitizar datos
    $nombre = $db->real_escape_string($nombre);
    $rut = $db->real_escape_string($rut);
    $empresa = $db->real_escape_string($empresa);
    $motivo = $db->real_escape_string($motivo);
    $empleadoId = $db->real_escape_string($empleadoId);
    $patente = $db->real_escape_string($patente);
    
    // Fecha y hora actuales en formato compatible con MySQL DATETIME
    $fechaEntrada = date('Y-m-d H:i:s');
    
    // Insertar visita con fecha_entrada
    $query = "INSERT INTO visitas (nombre_visitante, rut, empresa_origen,patente, motivo, empleado_id, fecha_entrada) 
              VALUES ('$nombre', '$rut', '$empresa', '$patente','$motivo', '$empleadoId', '$fechaEntrada')";
    
    if ($db->query($query)) {
        $visitaId = $db->insert_id;
        
        // Obtener datos del empleado para enviar correo
        $empleado = obtenerEmpleadoPorId($empleadoId);
        
        if ($empleado) {
            // Enviar correo al empleado
            $asunto = "Tienes una visita programada";
            $mensaje = "
                <html>
                <head>
                    <title>Notificación de Visita</title>
                </head>
                <body>
                    <h2>Notificación de Visita</h2>
                    <p>Tienes una visita registrada en el sistema:</p>
                    <ul>
                        <li><strong>Visitante:</strong> $nombre</li>
                        <li><strong>Empresa:</strong> $empresa</li>
                        <li><strong>Motivo:</strong> $motivo</li>
                        <li><strong>Fecha/Hora:</strong> " . date('d/m/Y H:i') . "</li>
                    </ul>
                </body>
                </html>
            ";
            
            enviarCorreo($empleado['correo'], $asunto, $mensaje);
        }
        
        cerrarConexion($db);
        return $visitaId;
    }
    
    cerrarConexion($db);
    return false;
}

function generarReporteExcel() {
    $db = conectarDB();
    
    // Obtener parámetros de fecha
    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
    $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
    
    // Validar fechas
    if ($fechaInicio > $fechaFin) {
        die("Error: La fecha de inicio no puede ser mayor a la fecha final");
    }
    
    // Consulta con rango de fechas
    $query = "SELECT v.*, e.nombre as nombre_empleado, e.departamento
              FROM visitas v
              LEFT JOIN empleados e ON v.empleado_id = e.id
              WHERE DATE(v.fecha_entrada) BETWEEN ? AND ?
              ORDER BY v.fecha_entrada DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $fechaInicio, $fechaFin);
    $stmt->execute();
    $result = $stmt->get_result();
    $visitas = [];
    
    while ($row = $result->fetch_assoc()) {
        $visitas[] = $row;
    }
    
    cerrarConexion($db);
    
    // Cabeceras para descarga de Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=reporte_visitas_{$fechaInicio}_al_{$fechaFin}.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    // Salida del contenido Excel
    echo "<table border='1'>";
    echo "<tr>
            <th>Visitante</th>
            <th>Empresa</th>
            <th>Visita a</th>
            <th>Fecha Entrada</th>
            <th>Hora Entrada</th>
            <th>Hora Salida</th>
            <th>Motivo</th>
          </tr>";
    
    foreach ($visitas as $visita) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($visita['nombre_visitante']) . "</td>";
        echo "<td>" . htmlspecialchars($visita['empresa_origen']) . "</td>";
        echo "<td>" . htmlspecialchars($visita['nombre_empleado']) . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($visita['fecha_entrada'])) . "</td>";
        echo "<td>" . date('H:i', strtotime($visita['fecha_entrada'])) . "</td>";
        echo "<td>" . ($visita['fecha_salida'] ? date('H:i', strtotime($visita['fecha_salida'])) : 'Pendiente') . "</td>";
        echo "<td>" . htmlspecialchars($visita['motivo']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    exit;
}

// Al inicio de tu script, verifica si se solicitó el reporte
if (isset($_GET['action']) && $_GET['action'] == 'exportar_excel') {
    generarReporteExcel();
}
// Función para registrar la salida de un visitante
function registrarSalida($visitaId, $fechaSalida) {
    $db = conectarDB();
    $visitaId = $db->real_escape_string($visitaId);
    $fechaSalida = $db->real_escape_string($fechaSalida);  // Escapar la fecha para evitar inyecciones SQL
    
    $query = "UPDATE visitas SET fecha_salida = '$fechaSalida' WHERE id = '$visitaId'";
    $result = $db->query($query);
    
    cerrarConexion($db);
    return $result;
}


// Función para obtener visitas activas (sin fecha de salida)
function obtenerVisitasActivas() {
    $db = conectarDB();
    
    $query = "SELECT v.*, e.nombre as nombre_empleado, e.departamento 
              FROM visitas v 
              JOIN empleados e ON v.empleado_id = e.id 
              WHERE v.fecha_salida IS NULL 
              ORDER BY v.fecha_entrada DESC";
    
    $result = $db->query($query);
    
    $visitas = [];
    while ($row = $result->fetch_assoc()) {
        $visitas[] = $row;
    }
    
    cerrarConexion($db);
    return $visitas;
}

// Función para obtener todas las visitas (con filtros opcionales)
function obtenerVisitas($filtros = []) {
    $db = conectarDB();
    
    $query = "SELECT v.*, e.nombre as nombre_empleado, e.departamento 
              FROM visitas v 
              JOIN empleados e ON v.empleado_id = e.id 
              WHERE 1=1";
    
    // Agregar filtros si existen
    if (!empty($filtros['fecha_inicio'])) {
        $fechaInicio = $db->real_escape_string($filtros['fecha_inicio']);
        $query .= " AND DATE(v.fecha_entrada) >= '$fechaInicio'";
    }
    
    if (!empty($filtros['fecha_fin'])) {
        $fechaFin = $db->real_escape_string($filtros['fecha_fin']);
        $query .= " AND DATE(v.fecha_entrada) <= '$fechaFin'";
    }
    
    if (!empty($filtros['empleado_id'])) {
        $empleadoId = $db->real_escape_string($filtros['empleado_id']);
        $query .= " AND v.empleado_id = '$empleadoId'";
    }
    
    // Ordenar por fecha de entrada descendente
    $query .= " ORDER BY v.fecha_entrada DESC";
    
    // Limitar resultados si se especifica
    if (!empty($filtros['limite'])) {
        $limite = intval($filtros['limite']);
        $query .= " LIMIT $limite";
    }
    
    $result = $db->query($query);
    
    $visitas = [];
    while ($row = $result->fetch_assoc()) {
        $visitas[] = $row;
    }
    
    cerrarConexion($db);
    return $visitas;
}
?>