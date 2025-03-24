<?php
// Iniciar sesión para mensajes
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Actualizar</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/forms.css">
</head>
<body>
  <div class="main-container">
      <?php
      // Mostrar mensajes de éxito o error si existen
      if (isset($_SESSION['mensaje'])) {
          echo '<div class="mensaje ' . $_SESSION['tipo_mensaje'] . '">' . $_SESSION['mensaje'] . '</div>';
          // Limpiar mensaje después de mostrarlo
          unset($_SESSION['mensaje']);
          unset($_SESSION['tipo_mensaje']);
      }
      ?>
      
      <div class="logo-container">
          <img src="Logo.jpg" alt="Logo Papelería Lupita" class="logo">
      </div>
      <div class="title-container">
          <h1>ACTUALIZAR</h1>
      </div>
      <div class="form-header">
          <div class="form-title">Seleccionar tipo de registro</div>
      </div>
      <form id="actualizar-form" action="procesar/buscar_registro.php" method="POST">
          <div class="select-container">
              <select name="tipo" id="registro-tipo" required>
                  <option value="">Registro de:</option>
                  <option value="productos">Productos</option>
                  <option value="clientes">Clientes</option>
                  <option value="proveedores">Proveedores</option>
                  <option value="ventas">Ventas</option>
              </select>
          </div>
          <div class="input-container">
              <input type="text" id="id-registro" name="id" placeholder="Id del Registro:" required>
          </div>
          <div class="button-group">
              <button type="button" class="action-button" onclick="window.location.href='index.php'">Regresar al menu</button>
              <button type="submit" class="action-button">Buscar</button>
          </div>
      </form>
  </div>
</body>
</html>