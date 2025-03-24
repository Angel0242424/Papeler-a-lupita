// Función para cargar productos desde la API
function cargarProductos() {
    fetch('api/productos.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('productos-table-body');
            if (!tableBody) return;
            
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="5" style="text-align: center;">No hay productos registrados</td>';
                tableBody.appendChild(row);
            } else {
                data.forEach(producto => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', producto.ID_producto);
                    row.innerHTML = `
                        <td>${producto.ID_producto}</td>
                        <td>${producto.Nombre_producto}</td>
                        <td>${producto.Descripcion}</td>
                        <td>${producto.Precio}</td>
                        <td>
                            <a href="actualizar-producto.php?id=${producto.ID_producto}" class="action-link">Editar</a>
                            <a href="javascript:void(0)" onclick="confirmarEliminar(${producto.ID_producto})" class="action-link">Eliminar</a>
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
            console.error('Error al cargar productos:', error);
            alert('Error al cargar los productos. Por favor, intente de nuevo.');
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
    if (confirm('¿Está seguro de que desea eliminar este producto?')) {
        window.location.href = 'mostrar-productos.php?eliminar=' + id;
    }
}

// Cargar productos al iniciar la página
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('productos-table-body')) {
        cargarProductos();
    }
});