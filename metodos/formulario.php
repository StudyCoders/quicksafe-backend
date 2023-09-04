<?php
include_once("../includes/auth.php");

$retorno = file_get_contents('php://input');

if (empty($retorno)) {
    retorna_erro("Nenhum dado foi enviado na requisição.", 400);
}

$dados = json_decode($retorno);

if(!empty($dados->id_formulario))
    $id_formulario = $dados->id_formulario;

$id_usuario = $decoded_array['id_usuario'];
$id_contato = $dados->id_contato;

$verifca_contato = !empty($id_contato) ? " AND ID_CONTATO = ?" : "";

$values_formulario = array(
    $cep = preg_replace('/\D/', '', $dados->cep),
    $endereco = $dados->endereco,
    $bairro = $dados->bairro,
    $complemento = $dados->complemento,
    $id_cidade = $dados->id_cidade,
    $telefone = preg_replace('/\D/', '', $dados->telefone),
    $celular = preg_replace('/\D/', '', $dados->celular),
    $dt_nascimento = transformarData($dados->dt_nascimento),
    $tp_sexo = $dados->tp_sexo,
    $id_plano = $dados->id_plano,
    $ds_plano = $dados->ds_plano,
    $alergia = $dados->alergia,
    $ds_alergia = $dados->ds_alergia,
    $id_comorbidade = $dados->id_comorbidade,
    $ds_comorbidade = $dados->ds_comorbidade,
    $med_cont = $dados->med_cont,
    $ds_med_cont = $dados->ds_med_cont,
    $cirurgia = $dados->cirurgia,
    $ds_cirurgia = $dados->ds_cirurgia
);


$sql = "SELECT * FROM FORMULARIO WHERE ID_USUARIO = ?" . $verifca_contato;
$values = array($id_usuario);

if(!empty($id_contato))
    array_push($values, $id_contato);

$count = $con->count($sql, $values);

if($count === 0){
    array_unshift($values_formulario, $id_usuario, $id_contato);

    $sql = "INSERT INTO FORMULARIO(
                ID_USUARIO,
                ID_CONTATO,
                CEP,
                ENDERECO,
                BAIRRO,
                COMPLEMENTO,
                ID_CIDADE,
                TELEFONE,
                CELULAR,
                DT_NASCIMENTO,
                TP_SEXO,
                ID_PLANO,
                DS_PLANO,
                ALERGIA,
                DS_ALERGIA,
                ID_COMORBIDADE,
                DS_COMORBIDADE,
                MEDICAMENTO_CONTINUO,
                DS_MEDICAMENTO_CONTINUO,
                CIRURGIA,
                DS_CIRURGIA
            )
                VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $con->insert($sql, $values_formulario);

    die(json_encode(array("msg" => "Formulario criado com sucesso")));
}else{
    array_push($values_formulario, $id_formulario);
    
    $sql = "UPDATE FORMULARIO SET
                CEP = ?,
                ENDERECO = ?,
                BAIRRO = ?,
                COMPLEMENTO = ?,
                ID_CIDADE = ?,
                TELEFONE = ?,
                CELULAR = ?,
                DT_NASCIMENTO = ?,
                TP_SEXO = ?,
                ID_PLANO = ?,
                DS_PLANO = ?,
                ALERGIA = ?,
                DS_ALERGIA = ?,
                ID_COMORBIDADE = ?,
                DS_COMORBIDADE = ?,
                MEDICAMENTO_CONTINUO = ?,
                DS_MEDICAMENTO_CONTINUO = ?,
                CIRURGIA = ?,
                DS_CIRURGIA = ?
            WHERE
                ID_FORMULARIO = ?";
    
    $stmt = $con->update($sql, $values_formulario);

    die(json_encode(array("msg" => "Formulario alterado com sucesso")));
}

?>