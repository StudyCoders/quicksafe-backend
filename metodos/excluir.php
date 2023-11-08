<?php
include_once("../includes/auth.php");

$id_usuario = $decoded_array['id_usuario'];
$id_contato = null;

$tabelas = array('FORMULARIO', 'CONTATOS');

if(!empty($_GET['id_contato'])){
    $id_contato = $_GET['id_contato'];

    foreach ($tabelas as $key => $value) {
        $sql = "DELETE FROM " .$value.
                  " WHERE ID_CONTATO = ?";
        $stmt = $con->delete($sql, array($id_contato));
    }

    die(json_encode(array("msg" => "Contato apagado com sucesso")));
}else{
    foreach ($tabelas as $key => $value) {
        $sql = "DELETE FROM " .$value.
                  " WHERE ID_USUARIO = ?";
        $stmt = $con->delete($sql, array($id_usuario));
    }

    $sql_usu = "UPDATE USUARIO SET ATIVO = 'N'
                      WHERE ID_USUARIO = ?";
    $stmt = $con->update($sql_usu, array($id_usuario));
    
    die(json_encode(array("msg" => "Usuario apagado com sucesso")));
}
?>