<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';

// Eliminar producto si se recibe el ID
if (isset($_GET['eliminar'])) {
  $id = $_GET['eliminar'];
  
  // Verificar si el producto está en uso en alguna venta
  $sql_check = "SELECT COUNT(*) as count FROM DetalleVenta WHERE ID_producto = ?";
  $stmt_check = $conn->prepare($sql_check);
  $stmt_check->bind_param("i", $id);
  $stmt_check->execute();
  $result = $stmt_check->get_result();
  $row = $result->fetch_assoc();
  
  if ($row['count'] > 0) {
      $_SESSION['mensaje'] = "No se puede eliminar el producto porque está en uso en ventas";
      $_SESSION['tipo_mensaje'] = "error";
  } else {
      // Preparar la consulta SQL para eliminar
      $sql = "DELETE FROM producto WHERE ID_producto = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $id);
      
      // Ejecutar la consulta
      if ($stmt->execute()) {
          $_SESSION['mensaje'] = "Producto eliminado correctamente";
          $_SESSION['tipo_mensaje'] = "exito";
      } else {
          $_SESSION['mensaje'] = "Error al eliminar el producto: " . $conn->error;
          $_SESSION['tipo_mensaje'] = "error";
      }
      
      // Cerrar la conexión
      $stmt->close();
  }
  
  // Redirigir para evitar reenvío del formulario
  header("Location: mostrar-productos.php");
  exit();
}

// Consulta para obtener todos los productos
$sql = "SELECT * FROM producto";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Mostrar Productos</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/tables.css">
</head>
<body>
  <div class="main-container">
      <div class="logo-container">
          <img src="Logo.jpg" alt="Logo Papelería Lupita" class="logo">
      </div>
      <div class="title-container">
          <h1>Mostrar Productos</h1>
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
      
      <div class="table-container">
          <table>
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Descripcion</th>
                      <th>Precio</th>
                      <th>Acciones</th>
                  </tr>
              </thead>
              <tbody id="productos-table-body">
                  <?php
                  // Verificar si hay productos
                  if ($result && $result->num_rows > 0) {
                      // Mostrar datos de cada producto
                      while($row = $result->fetch_assoc()) {
                          echo "<tr data-id='" . $row["ID_producto"] . "' onclick='seleccionarFila(this)'>";
                          echo "<td>" . $row["ID_producto"] . "</td>";
                          echo "<td>" . $row["Nombre_producto"] . "</td>";
                          echo "<td>" . $row["Descripcion"] . "</td>";
                          echo "<td>" . $row["Precio"] . "</td>";
                          echo "<td>
                                  <a href='actualizar-producto.php?id=" . $row["ID_producto"] . "' class='action-link'>Editar</a>
                                  <a href='javascript:void(0)' onclick='confirmarEliminar(" . $row["ID_producto"] . ")' class='action-link'>Eliminar</a>
                                </td>";
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='5' style='text-align: center;'>No hay productos registrados</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
      <div class="button-row">
          <button class="table-button" onclick="window.location.href='mostrar.php'">Menu</button>
          <button class="table-button" onclick="window.location.href='registrar-producto.php'">Registrar Nuevo</button>
      </div>
  </div>
  
  <script>
      // Variable para almacenar la fila seleccionada
      let filaSeleccionada = null;
      
      // Función para seleccionar una fila
      function seleccionarFila(fila) {
          // Deseleccionar la fila anteriormente seleccionada
          if (filaSeleccionada) {
              filaSeleccionada.classList.remove('selected-row');
          }
          
          // Seleccionar la nueva fila
          fila.classList.add('selected-row');
          filaSeleccionada = fila;
      }
      
      // Función para confirmar eliminación
      function confirmarEliminar(id) {
          if (confirm('¿Está seguro de que desea eliminar este producto?')) {
              window.location.href = 'mostrar-productos.php?eliminar=' + id;
          }
      }
  </script>
</body>
</html>