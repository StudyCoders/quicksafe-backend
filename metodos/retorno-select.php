<?php
include_once("../includes/auth.php");

$acao = $_GET["acao"];
$retorno = array();

if($acao == "cidade") {
    $sql = "SELECT * FROM CIDADE ORDER BY ID_CIDADE";
    $retorno = $con->fetchAll($sql);

} else if($acao == "comorbidade") {
    $sql = "SELECT * FROM COMORBIDADE ORDER BY ID_COMORBIDADE";
    $retorno = $con->fetchAll($sql);

} else if($acao == "plano") {
    $sql = "SELECT * FROM PLANO_SAUDE ORDER BY ID_PLANO";
    $retorno = $con->fetchAll($sql);

}

die(json_encode($retorno));
?>