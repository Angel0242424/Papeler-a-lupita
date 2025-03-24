// Función para cargar ventas desde la API
function cargarVentas() {
    fetch('api/ventas.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('ventas-table-body');
            if (!tableBody) return;
            
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="5" style="text-align: center;">No hay ventas registradas</td>';
                tableBody.appendChild(row);
            } else {
                data.forEach(venta => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', venta.ID_venta);
                    
                    // Formatear la fecha
                    const fecha = new Date(venta.Fecha_venta);
                    const fechaFormateada = fecha.toLocaleDateString();
                    
                    // Obtener nombre del cliente si existe
                    let nombreCliente = 'No asignado';
                    if (venta.ID_cliente) {
                        fetch(`api/clientes.php?id=${venta.ID_cliente}`)
                            .then(response => response.json())
                            .then(cliente => {
                                if (cliente && cliente.Nombre_cliente) {
                                    nombreCliente = cliente.Nombre_cliente;
                                    const clienteCell = row.querySelector('.cliente-nombre');
                                    if (clienteCell) {
                                        clienteCell.textContent = nombreCliente;
                                    }
                                }
                            })
                            .catch(error => console.error('Error al obtener cliente:', error));
                    }
                    
                    row.innerHTML = `
                        <td>${venta.ID_venta}</td>
                        <td>${fechaFormateada}</td>
                        <td class="cliente-nombre">${nombreCliente}</td>
                        <td>${venta.Total}</td>
                        <td>
                            <a href="ver-detalle-venta.php?id=${venta.ID_venta}" class="action-link">Ver detalle</a>
                            <a href="javascript:void(0)" onclick="confirmarEliminar(${venta.ID_venta})" class="action-link">Eliminar</a>
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
            console.error('Error al cargar ventas:', error);
            alert('Error al cargar las ventas. Por favor, intente de nuevo.');
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
    if (confirm('¿Está seguro de que desea eliminar esta venta?')) {
        window.location.href = 'mostrar-ventas.php?eliminar=' + id;
    }
}

// Cargar ventas al iniciar la página
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('ventas-table-body')) {
        cargarVentas();
    }
});