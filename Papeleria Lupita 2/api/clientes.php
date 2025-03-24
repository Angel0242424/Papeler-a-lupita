<?php
// Incluir archivo de conexión
require_once '../config/db.php';

// Establecer encabezados para JSON
header('Content-Type: application/json');

// Función para obtener todos los clientes
function obtenerClientes() {
  global $conn;
  $clientes = array();
  
  $sql = "SELECT * FROM clientes";
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          $clientes[] = $row;
      }
  }
  
  return $clientes;
}

// Función para obtener un cliente por ID
function obtenerClientePorId($id) {
  global $conn;
  
  $sql = "SELECT * FROM clientes WHERE ID_cliente = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
      return $result->fetch_assoc();
  } else {
      return null;
  }
}

// Función para agregar un nuevo cliente
function agregarCliente($datos) {
  global $conn;
  
  $sql = "INSERT INTO clientes (ID_cliente, Nombre_cliente, Direccion, Telefono) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isss", $datos['id'], $datos['nombre'], $datos['direccion'], $datos['telefono']);
  
  if ($stmt->execute()) {
      return true;
  } else {
      return false;
  }
}

// Función para actualizar un cliente
function actualizarCliente($datos) {
  global $conn;
  
  $sql = "UPDATE clientes SET Nombre_cliente = ?, Direccion = ?, Telefono = ? WHERE ID_cliente = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssi", $datos['nombre'], $datos['direccion'], $datos['telefono'], $datos['id']);
  
  if ($stmt->execute()) {
      return true;
  } else {
      return false;
  }
}

// Función para eliminar un cliente
function eliminarCliente($id) {
  global $conn;
  
  // Verificar si el cliente está en uso en alguna venta
  $sql_check = "SELECT COUNT(*) as count FROM ventas WHERE ID_cliente = ?";
  $stmt_check = $conn->prepare($sql_check);
  $stmt_check->bind_param("i", $id);
  $stmt_check->execute();
  $result = $stmt_check->get_result();
  $row = $result->fetch_assoc();
  
  if ($row['count'] > 0) {
      return false; // No se puede eliminar porque está en uso
  }
  
  $sql = "DELETE FROM clientes WHERE ID_cliente = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  
  if ($stmt->execute()) {
      return true;
  } else {
      return false;
  }
}

// Procesar solicitudes según el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
      if (isset($_GET['id'])) {
          $cliente = obtenerClientePorId($_GET['id']);
          if ($cliente) {
              echo json_encode($cliente);
          } else {
              http_response_code(404);
              echo json_encode(array("mensaje" => "Cliente no encontrado"));
          }
      } else {
          $clientes = obtenerClientes();
          echo json_encode($clientes);
      }
      break;
      
  case 'POST':
      $datos = json_decode(file_get_contents('php://input'), true);
      
      if (!$datos) {
          // Si no hay datos JSON, intentar obtener de POST
          $datos = array(
              'id' => $_POST['id'],
              'nombre' => $_POST['nombre'],
              'direccion' => $_POST['direccion'],
              'telefono' => $_POST['telefono']
          );
      }
      
      if (agregarCliente($datos)) {
          http_response_code(201);
          echo json_encode(array("mensaje" => "Cliente agregado correctamente"));
      } else {
          http_response_code(500);
          echo json_encode(array("mensaje" => "Error al agregar cliente"));
      }
      break;
      
  case 'PUT':
      $datos = json_decode(file_get_contents('php://input'), true);
      
      if (actualizarCliente($datos)) {
          echo json_encode(array("mensaje" => "Cliente actualizado correctamente"));
      } else {
          http_response_code(500);
          echo json_encode(array("mensaje" => "Error al actualizar cliente"));
      }
      break;
      
  case 'DELETE':
      if (isset($_GET['id'])) {
          if (eliminarCliente($_GET['id'])) {
              echo json_encode(array("mensaje" => "Cliente eliminado correctamente"));
          } else {
              http_response_code(500);
              echo json_encode(array("mensaje" => "Error al eliminar cliente o está en uso en ventas"));
          }
      } else {
          http_response_code(400);
          echo json_encode(array("mensaje" => "ID de cliente no proporcionado"));
      }
      break;
      
  default:
      http_response_code(405);
      echo json_encode(array("mensaje" => "Método no permitido"));
      break;
}

$conn->close();
?>