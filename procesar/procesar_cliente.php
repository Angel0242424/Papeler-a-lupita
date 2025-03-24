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
      $_SESSION['mensaje'] = "El ID y el nombre del cliente son obligatorios";
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../registrar-cliente.php");
      exit();
  }
  
  // Verificar si el ID ya existe
  $sql_check = "SELECT COUNT(*) as count FROM clientes WHERE ID_cliente = ?";
  $stmt_check = $conn->prepare($sql_check);
  $stmt_check->bind_param("i", $id);
  $stmt_check->execute();
  $result = $stmt_check->get_result();
  $row = $result->fetch_assoc();
  
  if ($row['count'] > 0) {
      $_SESSION['mensaje'] = "Ya existe un cliente con ese ID";
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../registrar-cliente.php");
      exit();
  }
  
  // Preparar la consulta SQL
  $sql = "INSERT INTO clientes (ID_cliente, Nombre_cliente, Direccion, Telefono) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isss", $id, $nombre, $direccion, $telefono);
  
  // Ejecutar la consulta
  if ($stmt->execute()) {
      $_SESSION['mensaje'] = "Cliente registrado correctamente";
      $_SESSION['tipo_mensaje'] = "exito";
      header("Location: ../mostrar-clientes.php");
      exit();
  } else {
      $_SESSION['mensaje'] = "Error al registrar el cliente: " . $conn->error;
      $_SESSION['tipo_mensaje'] = "error";
      header("Location: ../registrar-cliente.php");
      exit();
  }
  
  // Cerrar la conexión
  $stmt->close();
  $conn->close();
} else {
  // Si no se recibieron datos por POST, redirigir al formulario
  header("Location: ../registrar-cliente.php");
  exit();
}
?>