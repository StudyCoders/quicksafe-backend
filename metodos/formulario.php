<?php
include_once("../includes/auth.php");   

$retorno = file_get_contents('php://input');

if (empty($retorno)) {
    retorna_erro("Nenhum dado foi enviado na requisição.", 400);
}

$dados = json_decode($retorno);
$id_usuario = $decoded_array['id_usuario'];
$id_contato = "";

if(!empty($dados->nm_contato)){
    if (verificarPropriedades($dados, array("nm_contato"))) {
        $contato_existe = false;
        $tipo_contato = $dados->nm_contato;
        $id_usuario = $decoded_array['id_usuario'];
        $cpf = !empty($dados->cpf_contato) ? preg_replace('/\D/', '', $dados->cpf_contato) : "";

        if(!empty($_POST['id_contato'])){
            $id_contato = $_POST['id_contato'];
            $contato_existe = true;
        }
    } else {
        retorna_erro("Informe todas as propriedades necessárias: nm_contato.", 400);
    }

    if(!$contato_existe) {
        $sql_count = "SELECT ID_CONTATO FROM CONTATOS";
        $id_contato = $con->count($sql_count) + 1;

        $sql = "INSERT INTO CONTATOS(ID_CONTATO, TIPO_CONTATO, ID_USUARIO, CPF)
                    VALUES
                (?, ?, ?, ?)";
        $values = array($id_contato, $tipo_contato, $id_usuario, $cpf);
    
        $stmt = $con->insert($sql, $values);
    
    } else {
        $sql = "UPDATE CONTATOS SET
                  TIPO_CONTATO = ?,
                  CPF = ?
                WHERE ID_CONTATO = ?
                  AND ID_USUARIO = ?";
        $values = array($tipo_contato, $cpf, $id_contato, $id_usuario);
    
        $stmt = $con->update($sql, $values);
    }
}

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

$verifica_contato = !empty($id_contato) ? " AND ID_CONTATO = ?" : "";

$sql = "SELECT * FROM FORMULARIO WHERE ID_USUARIO = ?" . $verifica_contato;
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
    array_push($values_formulario, $id_usuario);

    if(!empty($id_contato)){
        $verifica_contato = " AND ID_CONTATO = ?";
        array_push($values_formulario, $id_contato);
    }else{
        $verifica_contato = " AND ID_CONTATO IS NULL";
    }
    
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
                ID_USUARIO = ?" .
                $verifica_contato;
    
    $stmt = $con->update($sql, $values_formulario);

    die(json_encode(array("msg" => "Formulario alterado com sucesso")));
}

?>