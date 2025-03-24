<?php
// filepath: c:\Users\AngelSC\OneDrive - Universidad Politecnica de Atalutla\Escritorio\Papeleria Lupita 2\procesar\procesar_venta.php

// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once '../config/db.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $id_cliente = !empty($_POST['id_cliente']) ? $_POST['id_cliente'] : null;
    $total = $_POST['total'];

    // Obtener productos, cantidades y precios
    $productos = $_POST['productos'];
    $cantidades = $_POST['cantidades'];
    $precios = $_POST['precios'];

    // Validar datos
    if (empty($id) || empty($fecha) || empty($total) || empty($productos) || empty($cantidades) || empty($precios)) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: ../registrar-venta.php");
        exit();
    }

    // Validar que los arrays de productos, cantidades y precios tengan el mismo tamaño
    if (count($productos) !== count($cantidades) || count($productos) !== count($precios)) {
        $_SESSION['mensaje'] = "Los datos de los productos no son consistentes.";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: ../registrar-venta.php");
        exit();
    }

    // Verificar si el ID ya existe
    $sql_check = "SELECT COUNT(*) as count FROM ventas WHERE ID_venta = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $_SESSION['mensaje'] = "Ya existe una venta con ese ID";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: ../registrar-venta.php");
        exit();
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Insertar la venta
        $sql_venta = "INSERT INTO ventas (ID_venta, Fecha_venta, ID_cliente, Total) VALUES (?, ?, ?, ?)";
        $stmt_venta = $conn->prepare($sql_venta);
        $stmt_venta->bind_param("isid", $id, $fecha, $id_cliente, $total);
        $stmt_venta->execute();

        // Insertar los detalles de la venta
        $sql_detalle = "INSERT INTO DetalleVenta (ID_venta, ID_producto, Cantidad, Precio_unitario, Subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt_detalle = $conn->prepare($sql_detalle);

        for ($i = 0; $i < count($productos); $i++) {
            $producto_id = $productos[$i];
            $cantidad = (int)$cantidades[$i];
            $precio = (float)$precios[$i];
            $subtotal = $cantidad * $precio;

            $stmt_detalle->bind_param("iidd", $id, $producto_id, $cantidad, $precio, $subtotal);
            $stmt_detalle->execute();
        }

        // Confirmar la transacción
        $conn->commit();

        $_SESSION['mensaje'] = "Venta registrada correctamente";
        $_SESSION['tipo_mensaje'] = "exito";
        header("Location: ../mostrar-ventas.php");
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();

        $_SESSION['mensaje'] = "Error al registrar la venta: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: ../registrar-venta.php");
        exit();
    } finally {
        // Cerrar las conexiones
        if (isset($stmt_venta)) {
            $stmt_venta->close();
        }
        if (isset($stmt_detalle)) {
            $stmt_detalle->close();
        }
        $conn->close();
    }
} else {
    // Si no se recibieron datos por POST, redirigir al formulario
    header("Location: ../registrar-venta.php");
    exit();
}
?>