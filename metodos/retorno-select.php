<?php
include_once("../includes/auth.php");

$retorno = array();

$queries = array(
    "cidade" => "SELECT C.ID_CIDADE AS value, C.NOME_CIDADE AS label FROM CIDADE C ORDER BY ID_CIDADE",
    "comorbidade" => "SELECT C.ID_COMORBIDADE AS value, C.NOME_COMORBIDADE AS label FROM COMORBIDADE C ORDER BY ID_COMORBIDADE",
    "planoSaude" => "SELECT P.ID_PLANO AS value, P.NOME_PLANO AS label FROM PLANO_SAUDE P ORDER BY ID_PLANO"
);

foreach ($queries as $key => $query) {
    $rs = $con->fetchAll($query);
    $retorno[$key] = $rs;
}

die(json_encode($retorno));
?>
