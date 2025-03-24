<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';

// Eliminar proveedor si se recibe el ID
if (isset($_GET['eliminar'])) {
  $id = $_GET['eliminar'];
  
  // Verificar si el proveedor está en uso en la tabla Producto_Proveedor
  $sql_check = "SELECT COUNT(*) as count FROM Producto_Proveedor WHERE ID_proveedor = ?";
  $stmt_check = $conn->prepare($sql_check);
  if ($stmt_check) {
      $stmt_check->bind_param("i", $id);
      $stmt_check->execute();
      $result = $stmt_check->get_result();
      $row = $result->fetch_assoc();
      
      if ($row['count'] > 0) {
          $_SESSION['mensaje'] = "No se puede eliminar el proveedor porque está asociado a productos";
          $_SESSION['tipo_mensaje'] = "error";
          header("Location: mostrar-proveedores.php");
          exit();
      }
      $stmt_check->close();
  }
  
  // Preparar la consulta SQL para eliminar
  $sql = "DELETE FROM proveedores WHERE ID_proveedor = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  
  // Ejecutar la consulta
  if ($stmt->execute()) {
      $_SESSION['mensaje'] = "Proveedor eliminado correctamente";
      $_SESSION['tipo_mensaje'] = "exito";
  } else {
      $_SESSION['mensaje'] = "Error al eliminar el proveedor: " . $conn->error;
      $_SESSION['tipo_mensaje'] = "error";
  }
  
  // Cerrar la conexión
  $stmt->close();
  
  // Redirigir para evitar reenvío del formulario
  header("Location: mostrar-proveedores.php");
  exit();
}

// Consulta para obtener todos los proveedores
$sql = "SELECT * FROM proveedores";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Mostrar Proveedores</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/tables.css">
  <script src="js/proveedores.js" defer></script>
</head>
<body>
  <div class="main-container">
      <div class="logo-container">
          <img src="Logo.jpg" alt="Logo Papelería Lupita" class="logo">
      </div>
      <div class="title-container">
          <h1>Mostrar Proveedores</h1>
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
                      <th>Dirección</th>
                      <th>Teléfono</th>
                      <th>Acciones</th>
                  </tr>
              </thead>
              <tbody id="proveedores-table-body">
                  <?php
                  // Verificar si hay proveedores
                  if ($result && $result->num_rows > 0) {
                      // Mostrar datos de cada proveedor
                      while($row = $result->fetch_assoc()) {
                          echo "<tr data-id='" . $row["ID_proveedor"] . "' onclick='seleccionarFila(this)'>";
                          echo "<td>" . $row["ID_proveedor"] . "</td>";
                          echo "<td>" . $row["Nombre_proveedor"] . "</td>";
                          echo "<td>" . $row["Direccion"] . "</td>";
                          echo "<td>" . $row["Telefono"] . "</td>";
                          echo "<td>
                                  <a href='actualizar-proveedor.php?id=" . $row["ID_proveedor"] . "' class='action-link'>Editar</a>
                                  <a href='javascript:void(0)' onclick='confirmarEliminar(" . $row["ID_proveedor"] . ")' class='action-link'>Eliminar</a>
                                </td>";
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='5' style='text-align: center;'>No hay proveedores registrados</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
      <div class="button-row">
          <button class="table-button" onclick="window.location.href='mostrar.php'">Menu</button>
          <button class="table-button" onclick="window.location.href='registrar-proveedor.php'">Registrar Nuevo</button>
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
          if (confirm('¿Está seguro de que desea eliminar este proveedor?')) {
              window.location.href = 'mostrar-proveedores.php?eliminar=' + id;
          }
      }
  </script>
</body>
</html>