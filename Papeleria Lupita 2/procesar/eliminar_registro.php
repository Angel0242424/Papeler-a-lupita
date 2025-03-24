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
        header("Location: ../eliminar.php");
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
            $redireccion = "../mostrar-productos.php";
            
            // Verificar si el producto está en uso en alguna venta
            $sql_check = "SELECT COUNT(*) as count FROM DetalleVenta WHERE ID_producto = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                $_SESSION['mensaje'] = "No se puede eliminar el producto porque está en uso en ventas";
                $_SESSION['tipo_mensaje'] = "error";
                header("Location: $redireccion");
                exit();
            }
            break;
            
        case 'clientes':
            $tabla = "clientes";
            $campo_id = "ID_cliente";
            $redireccion = "../mostrar-clientes.php";
            
            // Verificar si el cliente está en uso en alguna venta
            $sql_check = "SELECT COUNT(*) as count FROM ventas WHERE ID_cliente = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                $_SESSION['mensaje'] = "No se puede eliminar el cliente porque tiene ventas asociadas";
                $_SESSION['tipo_mensaje'] = "error";
                header("Location: $redireccion");
                exit();
            }
            break;
            
        case 'proveedores':
            $tabla = "proveedores";
            $campo_id = "ID_proveedor";
            $redireccion = "../mostrar-proveedores.php";
            
            // Verificar si el proveedor está en uso en la tabla Producto_Proveedor
            $sql_check = "SELECT COUNT(*) as count FROM Producto_Proveedor WHERE ID_proveedor = ?";
            $stmt_check = $conn->prepare($sql_check);
            if ($stmt_check) {
                $stmt_check->bind_param("i", $id);
                $stmt_check->execute();
                $result = $stmt_check->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    // Primero eliminar las relaciones en Producto_Proveedor
                    $sql_rel = "DELETE FROM Producto_Proveedor WHERE ID_proveedor = ?";
                    $stmt_rel = $conn->prepare($sql_rel);
                    $stmt_rel->bind_param("i", $id);
                    $stmt_rel->execute();
                    $stmt_rel->close();
                }
                $stmt_check->close();
            }
            break;
            
        case 'ventas':
            $tabla = "ventas";
            $campo_id = "ID_venta";
            $redireccion = "../mostrar-ventas.php";
            
            // Iniciar transacción
            $conn->begin_transaction();
            
            try {
                // Primero eliminar los detalles de la venta
                $sql_detalle = "DELETE FROM DetalleVenta WHERE ID_venta = ?";
                $stmt_detalle = $conn->prepare($sql_detalle);
                $stmt_detalle->bind_param("i", $id);
                $stmt_detalle->execute();
                $stmt_detalle->close();
                
                // Luego eliminar la venta
                $sql = "DELETE FROM $tabla WHERE $campo_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $conn->commit();
                    $_SESSION['mensaje'] = "Venta eliminada correctamente";
                    $_SESSION['tipo_mensaje'] = "exito";
                } else {
                    $conn->rollback();
                    $_SESSION['mensaje'] = "Error al eliminar la venta: " . $conn->error;
                    $_SESSION['tipo_mensaje'] = "error";
                }
                
                $stmt->close();
                header("Location: $redireccion");
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['mensaje'] = "Error al eliminar la venta: " . $e->getMessage();
                $_SESSION['tipo_mensaje'] = "error";
                header("Location: $redireccion");
                exit();
            }
            break;
            
        default:
            $_SESSION['mensaje'] = "Tipo de registro no válido";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: ../eliminar.php");
            exit();
    }
    
    // Eliminar el registro (para todos los tipos excepto ventas que ya se manejó)
    if ($tipo != 'ventas') {
        $sql = "DELETE FROM $tabla WHERE $campo_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Registro eliminado correctamente";
            $_SESSION['tipo_mensaje'] = "exito";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar el registro: " . $conn->error;
            $_SESSION['tipo_mensaje'] = "error";
        }
        
        $stmt->close();
        
        // Redirigir según el tipo
        if (empty($redireccion)) {
            $redireccion = "../eliminar.php";
        }
        
        header("Location: $redireccion");
        exit();
    }
} else {
    // Si no se recibieron datos por POST, redirigir al formulario
    header("Location: ../eliminar.php");
    exit();
}
?>