<?php 

$servidor = "mysql:dbname=anotador;host=localhost";
$usuario = "root";
$password = "";

try {
    $pdo = new PDO($servidor, $usuario, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
    // echo "conectado...";
} catch (PDOException $e) {
    echo "Conexion mala.. " .$e->getMessage();
}

?>