<?php
// Configuración de la conexión a la base de datos
$host = "localhost";      // Servidor de la base de datos
$username = "root";       // Usuario de la base de datos (ajustar según tu configuración)
$password = "";           // Contraseña (ajustar según tu configuración)
$database = "papeleria_lupita"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

// Establecer conjunto de caracteres
$conn->set_charset("utf8");
?>