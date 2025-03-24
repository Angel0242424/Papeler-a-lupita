<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';

// Eliminar cliente si se recibe el ID
if (isset($_GET['eliminar'])) {
  $id = $_GET['eliminar'];
  
  // Verificar si el cliente está en uso en alguna venta
  $sql_check = "SELECT COUNT(*) as count FROM ventas WHERE ID_cliente = ?";
  $stmt_check = $conn->prepare($sql_check);
  $stmt_check->bind_param("i", $id);
  $stmt_check->execute();
  $result = $stmt_check->get_result();
  $row = $result->fetch_assoc();
  
  if ($row['count'] > 0) {
      $_SESSION['mensaje'] = "No se puede eliminar el cliente porque tiene ventas asociadas. Vea las ventas del cliente primero.";
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: mostrar-ventas-cliente.php?id=" . $id);
      exit();
  }
  
  // Preparar la consulta SQL para eliminar
  $sql = "DELETE FROM clientes WHERE ID_cliente = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  
  // Ejecutar la consulta
  if ($stmt->execute()) {
      $_SESSION['mensaje'] = "Cliente eliminado correctamente";
      $_SESSION['tipo_mensaje'] = "exito";
  } else {
      $_SESSION['mensaje'] = "Error al eliminar el cliente: " . $conn->error;
      $_SESSION['tipo_mensaje'] = "error";
  }
  
  // Cerrar la conexión
  $stmt->close();
  
  // Redirigir para evitar reenvío del formulario
  header("Location: mostrar-clientes.php");
  exit();
}

// Consulta para obtener todos los clientes
$sql = "SELECT * FROM clientes";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Mostrar Clientes</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/tables.css">
</head>
<body>
  <div class="main-container">
      <div class="logo-container">
          <img src="Logo.jpg" alt="Logo Papelería Lupita" class="logo">
      </div>
      <div class="title-container">
          <h1>Mostrar Clientes</h1>
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
              <tbody id="clientes-table-body">
                  <?php
                  // Verificar si hay clientes
                  if ($result && $result->num_rows > 0) {
                      // Mostrar datos de cada cliente
                      while($row = $result->fetch_assoc()) {
                          echo "<tr data-id='" . $row["ID_cliente"] . "' onclick='seleccionarFila(this)'>";
                          echo "<td>" . $row["ID_cliente"] . "</td>";
                          echo "<td>" . $row["Nombre_cliente"] . "</td>";
                          echo "<td>" . $row["Direccion"] . "</td>";
                          echo "<td>" . $row["Telefono"] . "</td>";
                          echo "<td>
                                  <a href='actualizar-cliente.php?id=" . $row["ID_cliente"] . "' class='action-link'>Editar</a>
                                  <a href='javascript:void(0)' onclick='confirmarEliminar(" . $row["ID_cliente"] . ")' class='action-link'>Eliminar</a>
                                  <a href='mostrar-ventas-cliente.php?id=" . $row["ID_cliente"] . "' class='action-link'>Ver ventas</a>
                                </td>";
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='5' style='text-align: center;'>No hay clientes registrados</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
      <div class="button-row">
          <button class="table-button" onclick="window.location.href='mostrar.php'">Menu</button>
          <button class="table-button" onclick="window.location.href='registrar-cliente.php'">Registrar Nuevo</button>
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
          if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
              window.location.href = 'mostrar-clientes.php?eliminar=' + id;
          }
      }
  </script>
</body>
</html>