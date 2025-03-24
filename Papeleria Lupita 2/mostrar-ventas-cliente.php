<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';

// Verificar si se recibió el ID del cliente
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = "ID de cliente no proporcionado";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: mostrar-clientes.php");
    exit();
}

$id_cliente = $_GET['id'];

// Obtener información del cliente
$sql_cliente = "SELECT * FROM clientes WHERE ID_cliente = ?";
$stmt_cliente = $conn->prepare($sql_cliente);
$stmt_cliente->bind_param("i", $id_cliente);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();

if ($result_cliente->num_rows == 0) {
    $_SESSION['mensaje'] = "Cliente no encontrado";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: mostrar-clientes.php");
    exit();
}

$cliente = $result_cliente->fetch_assoc();

// Obtener ventas del cliente
$sql_ventas = "SELECT * FROM ventas WHERE ID_cliente = ? ORDER BY Fecha_venta DESC";
$stmt_ventas = $conn->prepare($sql_ventas);
$stmt_ventas->bind_param("i", $id_cliente);
$stmt_ventas->execute();
$result_ventas = $stmt_ventas->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Ventas del Cliente</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/tables.css">
</head>
<body>
  <div class="main-container">
      <div class="logo-container">
          <img src="Logo.jpg" alt="Logo Papelería Lupita" class="logo">
      </div>
      <div class="title-container">
          <h1>Ventas del Cliente</h1>
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
      
      <div class="info-cliente" style="background-color: #f5f5f5; padding: 15px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;">
          <h2>Información del Cliente</h2>
          <p><strong>ID:</strong> <?php echo $cliente['ID_cliente']; ?></p>
          <p><strong>Nombre:</strong> <?php echo $cliente['Nombre_cliente']; ?></p>
          <p><strong>Dirección:</strong> <?php echo $cliente['Direccion']; ?></p>
          <p><strong>Teléfono:</strong> <?php echo $cliente['Telefono']; ?></p>
      </div>
      
      <div class="table-container">
          <h2>Ventas del Cliente</h2>
          <table>
              <thead>
                  <tr>
                      <th>ID Venta</th>
                      <th>Fecha</th>
                      <th>Total</th>
                      <th>Acciones</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  // Verificar si hay ventas
                  if ($result_ventas && $result_ventas->num_rows > 0) {
                      // Mostrar datos de cada venta
                      while($venta = $result_ventas->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>" . $venta['ID_venta'] . "</td>";
                          echo "<td>" . date('d/m/Y', strtotime($venta['Fecha_venta'])) . "</td>";
                          echo "<td>$" . number_format($venta['Total'], 2) . "</td>";
                          echo "<td>
                                  <a href='ver-detalle-venta.php?id=" . $venta['ID_venta'] . "' class='action-link'>Ver detalle</a>
                                  <a href='javascript:void(0)' onclick='confirmarEliminar(" . $venta['ID_venta'] . ")' class='action-link'>Eliminar</a>
                                </td>";
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='4' style='text-align: center;'>Este cliente no tiene ventas registradas</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
      
      <div class="button-row">
          <button class="table-button" onclick="window.location.href='mostrar-clientes.php'">Volver a Clientes</button>
          <button class="table-button" onclick="window.location.href='registrar-venta.php'">Registrar Nueva Venta</button>
      </div>
  </div>
  
  <script>
      // Función para confirmar eliminación
      function confirmarEliminar(id) {
          if (confirm('¿Está seguro de que desea eliminar esta venta?')) {
              window.location.href = 'mostrar-ventas.php?eliminar=' + id;
          }
      }
  </script>
</body>
</html>