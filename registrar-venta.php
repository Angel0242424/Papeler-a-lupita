<?php
// Iniciar sesión para mensajes
session_start();

// Incluir archivo de conexión
require_once 'config/db.php';

// Obtener lista de productos para el select
$sql_productos = "SELECT * FROM producto ORDER BY Nombre_producto";
$result_productos = $conn->query($sql_productos);

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $id_venta = $_POST['id_venta'];
    $fecha = $_POST['fecha'];
    $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
    $cantidad = $_POST['cantidad'];

    // Validar datos
    if (empty($id_venta) || empty($fecha) || empty($id_producto) || empty($cantidad)) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        $_SESSION['tipo_mensaje'] = "error";
    } else {
        // Verificar si el ID_venta ya existe
        $sql_check = "SELECT COUNT(*) as count FROM ventas WHERE ID_venta = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $id_venta);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            $_SESSION['mensaje'] = "Ya existe una venta con ese ID.";
            $_SESSION['tipo_mensaje'] = "error";
        } else {
            // Preparar la consulta SQL
            $sql = "INSERT INTO ventas (ID_venta, Fecha_venta, ID_producto, Cantidad) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isii", $id_venta, $fecha, $id_producto, $cantidad);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Venta registrada correctamente.";
                $_SESSION['tipo_mensaje'] = "exito";
                header("Location: mostrar-ventas.php");
                exit();
            } else {
                $_SESSION['mensaje'] = "Error al registrar la venta: " . $conn->error;
                $_SESSION['tipo_mensaje'] = "error";
            }

            // Cerrar la conexión
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelería Lupita - Registrar Venta</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/forms.css">
    <style>
        body {
            background-color: #6ecece;
        }

        .main-container {
            max-width: 650px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-header {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-field {
            margin-bottom: 15px;
        }

        .form-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-field input,
        .form-field select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .action-button {
            padding: 10px 20px;
            background-color: #f5f5f5;
            border: 1px solid #999;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="title-container">
            <h1>REGISTRAR</h1>
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
            <div class="form-title">Registrar Venta</div>
        </div>
        <form id="venta-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-field">
                <label for="id-venta">Id de la Venta:</label>
                <input type="text" id="id-venta" name="id_venta" required>
            </div>
            <div class="form-field">
                <label for="fecha-venta">Fecha de la venta:</label>
                <input type="date" id="fecha-venta" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-field">
                <label for="id-producto">Producto:</label>
                <select id="id-producto" name="id_producto" required>
                    <option value="">-- Seleccionar producto --</option>
                    <?php
                    if ($result_productos && $result_productos->num_rows > 0) {
                        while ($producto = $result_productos->fetch_assoc()) {
                            echo "<option value='" . $producto['ID_producto'] . "'>" . $producto['Nombre_producto'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-field">
                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" min="1" required>
            </div>

            <div class="button-group">
                <button type="button" class="action-button" onclick="window.location.href='registrar.php'">Regresar al menú</button>
                <button type="submit" class="action-button">Registrar</button>
            </div>
        </form>
    </div>
</body>

</html>
