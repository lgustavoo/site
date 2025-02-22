<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "acesso_powerbi";

// Conectar ao banco de dados
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
