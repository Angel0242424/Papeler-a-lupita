<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';

// Obtener el ID del producto desde la URL
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Si no hay un ID válido, redirigir
if ($id_producto <= 0) {
    $_SESSION['mensaje'] = "ID de producto no válido.";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: registrar.php");
    exit();
}

// Obtener datos del producto para prellenar el formulario
$sql = "SELECT * FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

// Verificar si el producto existe
if (!$producto) {
    $_SESSION['mensaje'] = "Producto no encontrado.";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: registrar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Papelería Lupita - Actualizar Producto</title>
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
          <div class="form-title">Actualizar Producto</div>
      </div>

      <form id="producto-form" action="procesar/actualizar_proceso.php" method="POST">
          <div class="form-container">
              <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

              <div class="form-field">
                  <label for="nombre-producto">Nombre del producto:</label>
                  <input type="text" id="nombre-producto" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
              </div>

              <div class="form-field">
                  <label for="descripcion-producto">Descripción del producto:</label>
                  <input type="text" id="descripcion-producto" name="descripcion" value="<?php echo htmlspecialchars($producto['descripcion']); ?>" required>
              </div>

              <div class="form-field">
                  <label for="precio-producto">Precio del producto:</label>
                  <input type="number" id="precio-producto" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
              </div>
          </div>

          <div class="button-group">
              <button type="button" class="action-button" onclick="window.location.href='registrar.php'">Regresar al menú</button>
              <button type="submit" class="action-button">Actualizar</button>
          </div>
      </form>
  </div>
</body>
</html>
