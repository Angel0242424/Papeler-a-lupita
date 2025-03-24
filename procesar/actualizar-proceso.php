<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once '../config/db.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);

    // Validar campos no vacíos
    if (empty($nombre) || empty($descripcion) || $precio <= 0) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios y el precio debe ser mayor que 0.";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: ../actualizar_producto.php?id=$id");
        exit();
    }

    // Actualizar datos del producto
    $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Producto actualizado exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el producto.";
        $_SESSION['tipo_mensaje'] = "error";
    }

    // Redirigir de regreso al menú o lista
    header("Location: ../registrar.php");
    exit();
} else {
    $_SESSION['mensaje'] = "Acceso no autorizado.";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: ../registrar.php");
    exit();
}
?>
