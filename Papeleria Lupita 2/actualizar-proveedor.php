<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';

// Verificar si se recibió el ID
if (!isset($_GET['id'])) {
  $_SESSION['mensaje'] = "ID de proveedor no proporcionado";
  $_SESSION['tipo_mensaje'] = "error";
  header("Location: mostrar-proveedores.php");
  exit();
}

$id = $_GET['id'];

// Buscar el proveedor
$sql = "SELECT * FROM proveedores WHERE ID_proveedor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
  $_SESSION['mensaje'] = "Proveedor no encontrado";
  $_SESSION['tipo_mensaje'] = "error";
  header("Location: mostrar-proveedores.php");
  exit();
}

$proveedor = $result->fetch_assoc();

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener datos del formulario
  $nombre = $_POST['nombre'];
  $direccion = $_POST['direccion'];
  $telefono = $_POST['telefono'];
  
  // Validar datos
  if (empty($nombre)) {
      $_SESSION['mensaje'] = "El nombre del proveedor es obligatorio";
      $_SESSION['tipo_mensaje'] = "error";
  } else {
      // Preparar la consulta SQL
      $sql = "UPDATE proveedores SET Nombre_proveedor = ?, Direccion = ?, Telefono = ? WHERE ID_proveedor = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sssi", $nombre, $direccion, $telefono, $id);
      
      // Ejecutar la consulta
      if ($stmt->execute()) {
          $_SESSION['mensaje'] = "Proveedor actualizado correctamente";
          $_SESSION['tipo_mensaje'] = "exito";
          header("Location: mostrar-proveedores.php");
          exit();
      } else {
          $_SESSION['mensaje'] = "Error al actualizar el proveedor: " . $conn->error;
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
  <title>Papelería Lupita - Actualizar Proveedor</title>
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
          <div class="form-title">Actualizar Proveedor</div>
      </div>
      <form id="proveedor-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>">
          <div class="form-container">
              <div class="form-field">
                  <label for="id-proveedor">Id del Proveedor:</label>
                  <input type="text" id="id-proveedor" name="id" value="<?php echo $proveedor['ID_proveedor']; ?>" readonly>
              </div>
              <div class="form-field">
                  <label for="nombre-proveedor">Nombre del Proveedor:</label>
                  <input type="text" id="nombre-proveedor" name="nombre" value="<?php echo $proveedor['Nombre_proveedor']; ?>" required>
              </div>
              <div class="form-field">
                  <label for="direccion-proveedor">Dirección del Proveedor:</label>
                  <input type="text" id="direccion-proveedor" name="direccion" value="<?php echo $proveedor['Direccion']; ?>">
              </div>
              <div class="form-field">
                  <label for="telefono-proveedor">Teléfono:</label>
                  <input type="text" id="telefono-proveedor" name="telefono" value="<?php echo $proveedor['Telefono']; ?>">
              </div>
          </div>
          <div class="button-group">
              <button type="button" class="action-button" onclick="window.location.href='mostrar-proveedores.php'">Cancelar</button>
              <button type="submit" class="action-button">Guardar</button>
          </div>
      </form>
  </div>
</body>
</html>