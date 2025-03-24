// Funciones comunes para toda la aplicación

// Función para mostrar mensajes temporales
function mostrarMensaje(mensaje, tipo) {
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `mensaje ${tipo}`;
    mensajeDiv.textContent = mensaje;
    
    const mainContainer = document.querySelector('.main-container');
    if (mainContainer) {
        // Insertar al principio del contenedor principal
        mainContainer.insertBefore(mensajeDiv, mainContainer.firstChild);
        
        // Eliminar el mensaje después de 5 segundos
        setTimeout(() => {
            mensajeDiv.remove();
        }, 5000);
    }
}

// Función para validar formularios
function validarFormulario(form) {
    const campos = form.querySelectorAll('input[required], select[required], textarea[required]');
    let valido = true;
    
    campos.forEach(campo => {
        if (!campo.value.trim()) {
            campo.classList.add('campo-error');
            valido = false;
        } else {
            campo.classList.remove('campo-error');
        }
    });
    
    if (!valido) {
        mostrarMensaje('Por favor, complete todos los campos obligatorios', 'error');
    }
    
    return valido;
}

// Agregar validación a todos los formularios
document.addEventListener('DOMContentLoaded', function() {
    const formularios = document.querySelectorAll('form');
    
    formularios.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!validarFormulario(this)) {
                event.preventDefault();
            }
        });
        
        // Quitar clase de error al escribir
        const campos = form.querySelectorAll('input, select, textarea');
        campos.forEach(campo => {
            campo.addEventListener('input', function() {
                this.classList.remove('campo-error');
            });
        });
    });
});