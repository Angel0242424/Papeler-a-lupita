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
  <title>Papelería Lupita - Registrar Proveedor</title>
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
          <div class="form-title">Registrar Proveedor</div>
      </div>
      <form id="proveedor-form" action="procesar/procesar_proveedor.php" method="POST">
          <div class="form-container">
              <div class="form-field">
                  <label for="id-proveedor">Id del Proveedor:</label>
                  <input type="text" id="id-proveedor" name="id" required>
              </div>
              <div class="form-field">
                  <label for="nombre-proveedor">Nombre del Proveedor:</label>
                  <input type="text" id="nombre-proveedor" name="nombre" required>
              </div>
              <div class="form-field">
                  <label for="direccion-proveedor">Direccion del Proveedor:</label>
                  <input type="text" id="direccion-proveedor" name="direccion" required>
              </div>
              <div class="form-field">
                  <label for="telefono-proveedor">Telefono:</label>
                  <input type="text" id="telefono-proveedor" name="telefono" required>
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