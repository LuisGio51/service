<?php
require_once 'headers.php';

$conn = new mysqli('localhost','root', 'root', 'db_libros');

//Consultar uno o varios registros
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    //Si se proporciona el id se hace un where
    if(isset($_GET['id'])){
        $id = $conn->real_escape_string($_GET['id']);
        $sql = $conn->query("select * from usuarios where id = '$id'");
        $data = $sql->fetch_assoc();
    }
    else{
        //No se proporciona id, se envían todos.
        $data = array();
        $sql = $conn->query("select * from usuarios");
        while($d = $sql->fetch_assoc()){
            $data[] = $d;
        }
    }
    exit(json_encode($data));
}

//Insertar registro
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents("php://input"));
    $hashedPassword = md5($data->contrasenia);
    $sql = $conn->query("insert into usuarios(nombre, usuario, contrasenia) values('".$data->nombre."','".$data->usuario."','".$hashedPassword."')");
    if($sql){
        $data->id = $conn->insert_id;
        exit(json_encode($data));
    }
    else{
        exit(json_encode(array('status' => 'error')));
    }
}

//Modificar registro
if($_SERVER['REQUEST_METHOD'] === 'PUT'){
    if(isset($_GET['id'])){
        $id = $conn->real_escape_string($_GET['id']);
        $data = json_decode(file_get_contents("php://input"));
        $sql = $conn->query("update usuarios set nombre='".$data->nombre."', usuario='".$data->usuario."', contrasenia='".$data->contrasenia."' where id='$id'");
        if($sql){
            exit(json_encode(array('status' => 'success')));
        }
        else{
            exit(json_encode(array('status' => 'error')));
        }
    }
}

//Eliminar registro
if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    if(isset($_GET['id'])){
        $id = $conn->real_escape_string($_GET['id']);
        $data = json_decode(file_get_contents("php://input"));
        $sql = $conn->query("delete from usuarios where id = '$id'");
        
        if($sql){
            exit(json_encode(array('status' => 'success')));
        }
        else{
            exit(json_encode(array('status' => 'error')));
        }
    }
}
