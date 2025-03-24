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
  $direccion = $_POST['direccion'];
  $telefono = $_POST['telefono'];
  
  // Validar datos
  if (empty($id) || empty($nombre)) {
      $_SESSION['mensaje'] = "El ID y el nombre del proveedor son obligatorios";
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../registrar-proveedor.php");
      exit();
  }
  
  // Verificar si el ID ya existe
  $sql_check = "SELECT COUNT(*) as count FROM proveedores WHERE ID_proveedor = ?";
  $stmt_check = $conn->prepare($sql_check);
  $stmt_check->bind_param("i", $id);
  $stmt_check->execute();
  $result = $stmt_check->get_result();
  $row = $result->fetch_assoc();
  
  if ($row['count'] > 0) {
      $_SESSION['mensaje'] = "Ya existe un proveedor con ese ID";
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../registrar-proveedor.php");
      exit();
  }
  
  // Preparar la consulta SQL
  $sql = "INSERT INTO proveedores (ID_proveedor, Nombre_proveedor, Direccion, Telefono) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isss", $id, $nombre, $direccion, $telefono);
  
  // Ejecutar la consulta
  if ($stmt->execute()) {
      $_SESSION['mensaje'] = "Proveedor registrado correctamente";
      $_SESSION['tipo_mensaje'] = "exito";
      header("Location: ../mostrar-proveedores.php");
      exit();
  } else {
      $_SESSION['mensaje'] = "Error al registrar el proveedor: " . $conn->error;
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../registrar-proveedor.php");
      exit();
  }
  
  // Cerrar la conexión
  $stmt->close();
  $conn->close();
} else {
  // Si no se recibieron datos por POST, redirigir al formulario
  header("Location: ../registrar-proveedor.php");
  exit();
}
?>