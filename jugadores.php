<?php
    $txtId = (isset($_POST['txtId'])) ? $_POST['txtId'] : "";
    $txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";
    $txtFoto = (isset($_FILES["txtFoto"]["name"])) ? $_FILES["txtFoto"]["name"] : "";
    $accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";

    $error = array();

    $accionAgregar = "";
    $accionModificar = $accionEliminar = $accionCancelar = "disabled";
    $mostrarModal = false;

    include("conexion.php");

    switch($accion){
        case "btnAgregar":

            if($txtNombre==""){
                $error['Nombre']="Escribe el nombre";
            }
           
            if(count($error)>0){
                $mostrarModal = true;
                break;
            }

            $sentencia = $pdo->prepare("INSERT INTO jugadores(Nombre,Foto) VALUES (:Nombre, :Foto)");
            $sentencia->bindParam(':Nombre', $txtNombre);
            $fecha = new DateTime();
            $nombreArchivo = ($txtFoto!="")?$fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"imagen.png";
            $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
            if($tmpFoto!=""){
                move_uploaded_file($tmpFoto, "Imagenes/" .$nombreArchivo);
            }
            $sentencia->bindParam(':Foto', $nombreArchivo);
            $sentencia->execute();
            header('Location: index.php');
        break;

        case "btnModificar":
            $sentencia = $pdo->prepare("UPDATE jugadores SET Nombre=:Nombre WHERE id=:id");
            $sentencia->bindParam(':Nombre', $txtNombre);
            $sentencia->bindParam(':id', $txtId);
            $sentencia->execute();

            $fecha = new DateTime();
            $nombreArchivo = ($txtFoto!="")?$fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"imagen.png";
            $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
            if($tmpFoto!=""){
                // subimos la foto al servidor
                move_uploaded_file($tmpFoto, "Imagenes/" .$nombreArchivo);
                // eliminamos la fotografia actual
                $sentencia = $pdo->prepare("SELECT Foto FROM jugadores WHERE id=:id");
                $sentencia->bindParam(':id', $txtId);
                $sentencia->execute();
                $jugador = $sentencia->fetch(PDO::FETCH_LAZY);

                if(isset($jugador["Foto"])){
                    if(file_exists("Imagenes/".$jugador["Foto"])){
                        if($jugador['Foto'] != "imagen.png"){
                            unlink("Imagenes/".$jugador["Foto"]);
                        }
                    }
                }
                // actualizamos el link de la foto subida
                $sentencia = $pdo->prepare("UPDATE jugadores SET Foto=:Foto  WHERE id=:id");
                $sentencia->bindParam(':Foto', $nombreArchivo);
                $sentencia->bindParam(':id', $txtId);
                $sentencia->execute();
                header('Location: index.php');
            }

            
        break;

        case "btnEliminar":
            $sentencia = $pdo->prepare("SELECT Foto FROM jugadores WHERE id=:id");
            $sentencia->bindParam(':id', $txtId);
            $sentencia->execute();
            $jugador = $sentencia->fetch(PDO::FETCH_LAZY);

            if(isset($jugador["Foto"])&&($jugador["Foto"]!="imagen.png")){
                if(file_exists("Imagenes/".$jugador["Foto"])){
                    unlink("Imagenes/".$jugador["Foto"]);
                }
            }

            $sentencia = $pdo->prepare("DELETE FROM jugadores WHERE id=:id");
            $sentencia->bindParam(':id', $txtId);
            $sentencia->execute();
            header('Location: index.php');
        break;

        case "btnCancelar":
            header('Location: index.php');
        break;
        case "Seleccionar":
            $accionAgregar = "disabled";
            $accionModificar = $accionEliminar = $accionCancelar = "";
            $mostrarModal = true;

            $sentencia = $pdo->prepare("SELECT * FROM jugadores WHERE id=:id");
            $sentencia->bindParam(':id', $txtId);
            $sentencia->execute();
            $jugador = $sentencia->fetch(PDO::FETCH_LAZY);

            $txtNombre = $jugador['Nombre'];           
            $txtFoto = $jugador['Foto'];

        break;
    }

    $sentencia = $pdo->prepare("SELECT * FROM jugadores");
    $sentencia->execute();
    $listaJugadores = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>