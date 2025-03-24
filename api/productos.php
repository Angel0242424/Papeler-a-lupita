<?php
// Incluir archivo de conexión
require_once '../config/db.php';

// Establecer encabezados para JSON
header('Content-Type: application/json');

// Función para obtener todos los productos
function obtenerProductos() {
  global $conn;
  $productos = array();
  
  $sql = "SELECT * FROM producto";
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          $productos[] = $row;
      }
  }
  
  return $productos;
}

// Función para obtener un producto por ID
function obtenerProductoPorId($id) {
  global $conn;
  
  $sql = "SELECT * FROM producto WHERE ID_producto = ?";
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

// Función para agregar un nuevo producto
function agregarProducto($datos) {
  global $conn;
  
  $sql = "INSERT INTO producto (ID_producto, Nombre_producto, Descripcion, Precio) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("issd", $datos['id'], $datos['nombre'], $datos['descripcion'], $datos['precio']);
  
  if ($stmt->execute()) {
      return true;
  } else {
      return false;
  }
}

// Función para actualizar un producto
function actualizarProducto($datos) {
  global $conn;
  
  $sql = "UPDATE producto SET Nombre_producto = ?, Descripcion = ?, Precio = ? WHERE ID_producto = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssdi", $datos['nombre'], $datos['descripcion'], $datos['precio'], $datos['id']);
  
  if ($stmt->execute()) {
      return true;
  } else {
      return false;
  }
}

// Función para eliminar un producto
function eliminarProducto($id) {
  global $conn;
  
  // Verificar si el producto está en uso en alguna venta
  $sql_check = "SELECT COUNT(*) as count FROM DetalleVenta WHERE ID_producto = ?";
  $stmt_check = $conn->prepare($sql_check);
  $stmt_check->bind_param("i", $id);
  $stmt_check->execute();
  $result = $stmt_check->get_result();
  $row = $result->fetch_assoc();
  
  if ($row['count'] > 0) {
      return false; // No se puede eliminar porque está en uso
  }
  
  $sql = "DELETE FROM producto WHERE ID_producto = ?";
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
          $producto = obtenerProductoPorId($_GET['id']);
          if ($producto) {
              echo json_encode($producto);
          } else {
              http_response_code(404);
              echo json_encode(array("mensaje" => "Producto no encontrado"));
          }
      } else {
          $productos = obtenerProductos();
          echo json_encode($productos);
      }
      break;
      
  case 'POST':
      $datos = json_decode(file_get_contents('php://input'), true);
      
      if (!$datos) {
          // Si no hay datos JSON, intentar obtener de POST
          $datos = array(
              'id' => $_POST['id'],
              'nombre' => $_POST['nombre'],
              'descripcion' => $_POST['descripcion'],
              'precio' => $_POST['precio']
          );
      }
      
      if (agregarProducto($datos)) {
          http_response_code(201);
          echo json_encode(array("mensaje" => "Producto agregado correctamente"));
      } else {
          http_response_code(500);
          echo json_encode(array("mensaje" => "Error al agregar producto"));
      }
      break;
      
  case 'PUT':
      $datos = json_decode(file_get_contents('php://input'), true);
      
      if (actualizarProducto($datos)) {
          echo json_encode(array("mensaje" => "Producto actualizado correctamente"));
      } else {
          http_response_code(500);
          echo json_encode(array("mensaje" => "Error al actualizar producto"));
      }
      break;
      
  case 'DELETE':
      if (isset($_GET['id'])) {
          if (eliminarProducto($_GET['id'])) {
              echo json_encode(array("mensaje" => "Producto eliminado correctamente"));
          } else {
              http_response_code(500);
              echo json_encode(array("mensaje" => "Error al eliminar producto o está en uso en ventas"));
          }
      } else {
          http_response_code(400);
          echo json_encode(array("mensaje" => "ID de producto no proporcionado"));
      }
      break;
      
  default:
      http_response_code(405);
      echo json_encode(array("mensaje" => "Método no permitido"));
      break;
}

$conn->close();
?>