<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Registrar Producto</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/forms.css">
</head>
<body>
  <div class="main-container">
      <div class="logo-container">
          <img src="Logo.jpg" alt="Logo Papelería Lupita" class="logo">
      </div>
      <div class="title-container">
          <h1>REGISTRAR</h1>
      </div>
      
      <?php
      // Mostrar mensajes de éxito o error si existen
      if (isset($_SESSION['mensaje'])) {
          echo '<div class="mensaje ' . $_SESSION['tipo_mensaje'] . '">' . $_SESSION['mensaje'] . '</div>';
          // Limpiar mensaje después de mostrarlo
          unset($_SESSION['mensaje']);
          unset($_SESSION['tipo_mensaje']);
      }
      ?>
      
      <div class="form-header">
          <div class="form-title">Registrar Producto</div>
      </div>
      <form id="producto-form" action="procesar/procesar_producto.php" method="POST">
          <div class="form-container">
              <div class="form-field">
                  <label for="id-producto">Id del Producto:</label>
                  <input type="text" id="id-producto" name="id" required>
              </div>
              <div class="form-field">
                  <label for="nombre-producto">Nombre del producto:</label>
                  <input type="text" id="nombre-producto" name="nombre" required>
              </div>
              <div class="form-field">
                  <label for="descripcion-producto">Descripcion del producto:</label>
                  <input type="text" id="descripcion-producto" name="descripcion" required>
              </div>
              <div class="form-field">
                  <label for="precio-producto">Precio del producto:</label>
                  <input type="number" id="precio-producto" name="precio" step="0.01" required>
              </div>
          </div>
          <div class="button-group">
              <button type="button" class="action-button" onclick="window.location.href='registrar.php'">Regresar al menu</button>
              <button type="submit" class="action-button">Registrar</button>
          </div>
      </form>
  </div>
</body>
</html>