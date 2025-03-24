<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';

// Eliminar venta si se recibe el ID
if (isset($_GET['eliminar'])) {
  $id = $_GET['eliminar'];
  
  // Preparar la consulta SQL para eliminar
  $sql = "DELETE FROM ventas WHERE ID_venta = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  
  // Ejecutar la consulta
  if ($stmt->execute()) {
      $_SESSION['mensaje'] = "Venta eliminada correctamente";
      $_SESSION['tipo_mensaje'] = "exito";
  } else {
      $_SESSION['mensaje'] = "Error al eliminar la venta: " . $conn->error;
      $_SESSION['tipo_mensaje'] = "error";
  }
  
  // Cerrar la conexión
  $stmt->close();
  
  // Redirigir para evitar reenvío del formulario
  header("Location: mostrar-ventas.php");
  exit();
}

// Consulta para obtener todas las ventas
// Modificada para no usar ID_cliente que no existe en la tabla ventas
$sql = "SELECT * FROM ventas ORDER BY Fecha_venta DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Mostrar Ventas</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/tables.css">
</head>
<body>
  <div class="main-container">
      <div class="logo-container">
          <img src="Logo.jpg" alt="Logo Papelería Lupita" class="logo">
      </div>
      <div class="title-container">
          <h1>Mostrar Ventas</h1>
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
                      <th>Fecha</th>
                      <th>ID Producto</th>
                      <th>Cantidad</th>
                      <th>Acciones</th>
                  </tr>
              </thead>
              <tbody id="ventas-table-body">
                  <?php
                  // Verificar si hay ventas
                  if ($result && $result->num_rows > 0) {
                      // Mostrar datos de cada venta
                      while($row = $result->fetch_assoc()) {
                          echo "<tr data-id='" . $row["ID_venta"] . "' onclick='seleccionarFila(this)'>";
                          echo "<td>" . $row["ID_venta"] . "</td>";
                          echo "<td>" . $row["Fecha_venta"] . "</td>";
                          echo "<td>" . $row["ID_producto"] . "</td>";
                          echo "<td>" . $row["Cantidad"] . "</td>";
                          echo "<td>
                                  <a href='javascript:void(0)' onclick='confirmarEliminar(" . $row["ID_venta"] . ")' class='action-link'>Eliminar</a>
                                </td>";
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='5' style='text-align: center;'>No hay ventas registradas</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
      <div class="button-row">
          <button class="table-button" onclick="window.location.href='mostrar.php'">Menu</button>
          <button class="table-button" onclick="window.location.href='registrar-venta.php'">Registrar Nueva</button>
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
          if (confirm('¿Está seguro de que desea eliminar esta venta?')) {
              window.location.href = 'mostrar-ventas.php?eliminar=' + id;
          }
      }
  </script>
</body>
</html>