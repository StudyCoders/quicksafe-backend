<?php
include_once("../includes/auth.php");
date_default_timezone_set('America/Sao_Paulo');

$retorno = file_get_contents('php://input');
$id_usuario = $decoded_array['id_usuario'];

if (empty($retorno)) {
    retorna_erro("Nenhum dado foi enviado na requisição.", 400);
}

$dados = json_decode($retorno);
$propriedades = array("latitude", "longitude");

if (verificarPropriedades($dados, $propriedades)) {
    $latitude = $dados->latitude;
    $longitude = $dados->longitude;
    $dthr = date('Y/m/d H:i:s', time());
} else {
    retorna_erro("Informe todas as propriedades necessárias: latitude e longitude.", 400);
}

$sql = "INSERT INTO LOCALIDADE(LATITUDE, LONGITUDE, DTHR_LOCALIDADE, ID_USUARIO)
            VALUES (?, ?, ?, ?)";

$values = array($latitude, $longitude, $dthr, $id_usuario);

$stmt = $con->insert($sql, $values);

die(json_encode(array("msg" => "Localidade salva com sucesso")));
