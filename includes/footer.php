</main>
    
    <footer class="bg-gray-800 text-white py-4 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> Sistema de Registro de Visitas</p>
        </div>
    </footer>
    
    <script>
        // Script para manejar la validación de RUT
        function validarRutInput(input) {
            // Eliminar caracteres no deseados y formatear
            let rut = input.value.replace(/[^0-9kK-]/g, '');
            
            // Añadir guión si no lo tiene y tiene al menos 2 caracteres
            if (rut.length > 1 && rut.indexOf('-') === -1) {
                rut = rut.slice(0, -1) + '-' + rut.slice(-1);
            }
            
            input.value = rut;
        }
        
        // Script para confirmar acciones importantes
        function confirmarAccion(mensaje) {
            return confirm(mensaje);
        }
    </script>
</body>
</html>