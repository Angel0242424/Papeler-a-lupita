<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once '../config/db.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener datos del formulario
  $id = $_POST['id'];
  $nombre = $_POST['nombre'];
  $descripcion = $_POST['descripcion'];
  $precio = $_POST['precio'];
  
  // Validar datos
  if (empty($id) || empty($nombre) || empty($descripcion) || empty($precio)) {
      $_SESSION['mensaje'] = "Todos los campos son obligatorios";
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../registrar-producto.php");
      exit();
  }
  
  // Verificar si el ID ya existe
  $sql_check = "SELECT COUNT(*) as count FROM producto WHERE ID_producto = ?";
  $stmt_check = $conn->prepare($sql_check);
  $stmt_check->bind_param("i", $id);
  $stmt_check->execute();
  $result = $stmt_check->get_result();
  $row = $result->fetch_assoc();
  
  if ($row['count'] > 0) {
      $_SESSION['mensaje'] = "Ya existe un producto con ese ID";
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../registrar-producto.php");
      exit();
  }
  
  // Preparar la consulta SQL
  $sql = "INSERT INTO producto (ID_producto, Nombre_producto, Descripcion, Precio) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("issd", $id, $nombre, $descripcion, $precio);
  
  // Ejecutar la consulta
  if ($stmt->execute()) {
      $_SESSION['mensaje'] = "Producto registrado correctamente";
      $_SESSION['tipo_mensaje'] = "exito";
      header("Location: ../mostrar-productos.php");
      exit();
  } else {
      $_SESSION['mensaje'] = "Error al registrar el producto: " . $conn->error;
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../registrar-producto.php");
      exit();
  }
  
  // Cerrar la conexión
  $stmt->close();
  $conn->close();
} else {
  // Si no se recibieron datos por POST, redirigir al formulario
  header("Location: ../registrar-producto.php");
  exit();
}
?>