// Función para cargar clientes desde la API
function cargarClientes() {
    fetch('api/clientes.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('clientes-table-body');
            if (!tableBody) return;
            
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="5" style="text-align: center;">No hay clientes registrados</td>';
                tableBody.appendChild(row);
            } else {
                data.forEach(cliente => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', cliente.ID_cliente);
                    row.innerHTML = `
                        <td>${cliente.ID_cliente}</td>
                        <td>${cliente.Nombre_cliente}</td>
                        <td>${cliente.Direccion}</td>
                        <td>${cliente.Telefono}</td>
                        <td>
                            <a href="actualizar-cliente.php?id=${cliente.ID_cliente}" class="action-link">Editar</a>
                            <a href="javascript:void(0)" onclick="confirmarEliminar(${cliente.ID_cliente})" class="action-link">Eliminar</a>
                            <a href="mostrar-ventas-cliente.php?id=${cliente.ID_cliente}" class="action-link">Ver ventas</a>
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
            console.error('Error al cargar clientes:', error);
            alert('Error al cargar los clientes. Por favor, intente de nuevo.');
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
    if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
        window.location.href = 'mostrar-clientes.php?eliminar=' + id;
    }
}

// Cargar clientes al iniciar la página
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('clientes-table-body')) {
        cargarClientes();
    }
});