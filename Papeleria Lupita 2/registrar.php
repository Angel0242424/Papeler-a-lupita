<?php
// Iniciar sesión para mensajes
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Registrar</title>
  <link rel="stylesheet" href="css/styles.css">
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
          <h1>REGISTRAR</h1>
      </div>
      <div class="buttons-container">
          <button class="menu-button" onclick="window.location.href='registrar-producto.php'">Producto</button>
          <button class="menu-button" onclick="window.location.href='registrar-cliente.php'">Cliente</button>
          <button class="menu-button" onclick="window.location.href='registrar-proveedor.php'">Proveedor</button>
          <button class="menu-button" onclick="window.location.href='registrar-venta.php'">Venta</button>
          <button class="menu-button" onclick="window.location.href='index.php'">Regresar al menú</button>
      </div>
  </div>
</body>
</html>