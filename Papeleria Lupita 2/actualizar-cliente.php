<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';

// Verificar si se recibió el ID
if (!isset($_GET['id'])) {
  $_SESSION['mensaje'] = "ID de cliente no proporcionado";
  $_SESSION['tipo_mensaje'] = "error";
  header("Location: mostrar-clientes.php");
  exit();
}

$id = $_GET['id'];

// Buscar el cliente
$sql = "SELECT * FROM clientes WHERE ID_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
  $_SESSION['mensaje'] = "Cliente no encontrado";
  $_SESSION['tipo_mensaje'] = "error";
  header("Location: mostrar-clientes.php");
  exit();
}

$cliente = $result->fetch_assoc();

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener datos del formulario
  $nombre = $_POST['nombre'];
  $direccion = $_POST['direccion'];
  $telefono = $_POST['telefono'];
  
  // Validar datos
  if (empty($nombre)) {
      $_SESSION['mensaje'] = "El nombre del cliente es obligatorio";
      $_SESSION['tipo_mensaje'] = "error";
  } else {
      // Preparar la consulta SQL
      $sql = "UPDATE clientes SET Nombre_cliente = ?, Direccion = ?, Telefono = ? WHERE ID_cliente = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sssi", $nombre, $direccion, $telefono, $id);
      
      // Ejecutar la consulta
      if ($stmt->execute()) {
          $_SESSION['mensaje'] = "Cliente actualizado correctamente";
          $_SESSION['tipo_mensaje'] = "exito";
          header("Location: mostrar-clientes.php");
          exit();
      } else {
          $_SESSION['mensaje'] = "Error al actualizar el cliente: " . $conn->error;
          $_SESSION['tipo_mensaje'] = "error";
      }
      
      // Cerrar la conexión
      $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Actualizar Cliente</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/forms.css">
</head>
<body>
  <div class="main-container">
      <div class="logo-container">
          <img src="Logo.jpg" alt="Logo Papelería Lupita" class="logo">
      </div>
      <div class="title-container">
          <h1>ACTUALIZAR</h1>
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
          <div class="form-title">Actualizar Cliente</div>
      </div>
      <form id="cliente-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>">
          <div class="form-container">
              <div class="form-field">
                  <label for="id-cliente">Id del Cliente:</label>
                  <input type="text" id="id-cliente" name="id" value="<?php echo $cliente['ID_cliente']; ?>" readonly>
              </div>
              <div class="form-field">
                  <label for="nombre-cliente">Nombre del Cliente:</label>
                  <input type="text" id="nombre-cliente" name="nombre" value="<?php echo $cliente['Nombre_cliente']; ?>" required>
              </div>
              <div class="form-field">
                  <label for="direccion-cliente">Dirección del Cliente:</label>
                  <input type="text" id="direccion-cliente" name="direccion" value="<?php echo $cliente['Direccion']; ?>">
              </div>
              <div class="form-field">
                  <label for="telefono-cliente">Teléfono:</label>
                  <input type="text" id="telefono-cliente" name="telefono" value="<?php echo $cliente['Telefono']; ?>">
              </div>
          </div>
          <div class="button-group">
              <button type="button" class="action-button" onclick="window.location.href='mostrar-clientes.php'">Cancelar</button>
              <button type="submit" class="action-button">Guardar</button>
          </div>
      </form>
  </div>
</body>
</html>