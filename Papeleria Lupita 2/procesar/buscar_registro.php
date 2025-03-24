<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once '../config/db.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener datos del formulario
  $tipo = $_POST['tipo'];
  $id = $_POST['id'];
  
  // Validar datos
  if (empty($id)) {
      $_SESSION['mensaje'] = "El ID es obligatorio";
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../actualizar.php");
      exit();
  }
  
  // Preparar la consulta SQL según el tipo
  $tabla = "";
  $campo_id = "";
  $redireccion = "";
  
  switch ($tipo) {
      case 'productos':
          $tabla = "producto";
          $campo_id = "ID_producto";
          $redireccion = "../actualizar-producto.php?id=$id";
          break;
      case 'clientes':
          $tabla = "clientes";
          $campo_id = "ID_cliente";
          $redireccion = "../actualizar-cliente.php?id=$id";
          break;
      case 'proveedores':
          $tabla = "proveedores";
          $campo_id = "ID_proveedor";
          $redireccion = "../actualizar-proveedor.php?id=$id";
          break;
      case 'ventas':
          $tabla = "ventas";
          $campo_id = "ID_venta";
          $redireccion = "../actualizar-venta.php?id=$id";
          break;
      default:
          $_SESSION['mensaje'] = "Tipo de registro no válido";
          $_SESSION['tipo_mensaje'] = "error";
          header("Location: ../actualizar.php");
          exit();
  }
  
  // Buscar el registro
  $sql = "SELECT * FROM $tabla WHERE $campo_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
      // Registro encontrado, redirigir a la página de actualización correspondiente
      header("Location: $redireccion");
      exit();
  } else {
      // Registro no encontrado
      $_SESSION['mensaje'] = "No se encontró ningún registro con el ID proporcionado";
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../actualizar.php");
      exit();
  }
  
  // Cerrar la conexión
  $stmt->close();
  $conn->close();
} else {
  // Si no se recibieron datos por POST, redirigir al formulario
  header("Location: ../actualizar.php");
  exit();
}
?>