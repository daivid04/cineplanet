    </div>
    
    <!-- Scripts -->
    <script>
        // Calcular la ruta base dinamicamente
        const pathMatch = window.location.pathname.match(/(.*?\/public_html)/);
        const API_BASE = pathMatch ? pathMatch[1] + '/api' : '/api';
        
        // Funcion para mostrar alertas
        function showAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            alertDiv.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
            alertDiv.textContent = message;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 300);
            }, 3000);
        }
        
        // Funcion para confirmar eliminacion
        function confirmDelete(id, nombre, endpoint) {
            if (confirm(`¿Esta seguro de eliminar "${nombre}"?`)) {
                fetch(`${API_BASE}/${endpoint}?id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert(data.message || 'Error al eliminar', 'error');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexion', 'error');
                    console.error(error);
                });
            }
        }
        
        // Funcion para toggle estado
        function toggleEstado(id, estadoActual, endpoint) {
            const nuevoEstado = estadoActual == 1 ? 0 : 1;
            
            fetch(`${API_BASE}/${endpoint}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, estado: nuevoEstado })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showAlert(data.message || 'Error al cambiar estado', 'error');
                }
            })
            .catch(error => {
                showAlert('Error de conexion', 'error');
                console.error(error);
            });
        }
    </script>
</body>
</html>
