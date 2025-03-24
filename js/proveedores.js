// Función para cargar proveedores desde la API
function cargarProveedores() {
    fetch('api/proveedores.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('proveedores-table-body');
            if (!tableBody) return;
            
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="5" style="text-align: center;">No hay proveedores registrados</td>';
                tableBody.appendChild(row);
            } else {
                data.forEach(proveedor => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', proveedor.ID_proveedor);
                    row.innerHTML = `
                        <td>${proveedor.ID_proveedor}</td>
                        <td>${proveedor.Nombre_proveedor}</td>
                        <td>${proveedor.Direccion}</td>
                        <td>${proveedor.Telefono}</td>
                        <td>
                            <a href="actualizar-proveedor.php?id=${proveedor.ID_proveedor}" class="action-link">Editar</a>
                            <a href="javascript:void(0)" onclick="confirmarEliminar(${proveedor.ID_proveedor})" class="action-link">Eliminar</a>
                        </td>
                    `;
                    
                    row.addEventListener('click', function() {
                        seleccionarFila(this);
                    });
                    
                    tableBody.appendChild(row);
                });
            }
        })
        .catch(error => {
            console.error('Error al cargar proveedores:', error);
            alert('Error al cargar los proveedores. Por favor, intente de nuevo.');
        });
}

// Función para seleccionar una fila
function seleccionarFila(fila) {
    // Deseleccionar la fila anteriormente seleccionada
    const filasSeleccionadas = document.querySelectorAll('.selected-row');
    filasSeleccionadas.forEach(f => f.classList.remove('selected-row'));
    
    // Seleccionar la nueva fila
    fila.classList.add('selected-row');
}

// Función para confirmar eliminación
function confirmarEliminar(id) {
    if (confirm('¿Está seguro de que desea eliminar este proveedor?')) {
        window.location.href = 'mostrar-proveedores.php?eliminar=' + id;
    }
}

// Cargar proveedores al iniciar la página
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('proveedores-table-body')) {
        cargarProveedores();
    }
});