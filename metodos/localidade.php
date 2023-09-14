<?php
include_once("../includes/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

$retorno = file_get_contents('php://input');

if (empty($retorno)) {
    retorna_erro("Nenhum dado foi enviado na requisição.", 400);
}

$dados = json_decode($retorno);
$propriedades = array("latitude", "longitude", "id_usuario");

if (verificarPropriedades($dados, $propriedades)) {
    $latitude = $dados->latitude;
    $longitude = $dados->longitude;
    $dthr = date('Y/m/d H:i:s', time());
    $id_usuario = $dados->id_usuario;
    $id_contato = $dados->id_contato;
} else {
    retorna_erro("Informe todas as propriedades necessárias: latitude, longitude e id_usuario.", 400);
}

$sql = "INSERT INTO LOCALIDADE(LATITUDE, LONGITUDE, DTHR_LOCALIDADE, ID_USUARIO, ID_CONTATO)
            VALUES
        (?, ?, ?, ?, ?)";
$values = array($latitude, $longitude, $dthr, $id_usuario, $id_contato);

$stmt = $con->insert($sql, $values);

die(json_encode(array("msg" => "Localidade salva com sucesso")));
?>