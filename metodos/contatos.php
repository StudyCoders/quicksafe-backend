<?php
include_once("../includes/auth.php");

$retorno = file_get_contents('php://input');

if (empty($retorno)) {
    retorna_erro("Nenhum dado foi enviado na requisição.", 400);
}

$dados = json_decode($retorno);
$propriedades = array("tipo_contato");
$contato_existe = false;

if(!empty($dados->id_contato)) {
    $id_contato = $dados->id_contato;
    array_push($propriedades, "id_contato");
    $contato_existe = true;
}

if (verificarPropriedades($dados, $propriedades)) {
    $tipo_contato = $dados->tipo_contato;
    $id_usuario = $decoded_array['id_usuario'];
    $cpf = !empty($dados->cpf) ? preg_replace('/\D/', '', $dados->cpf) : "";
} else {
    retorna_erro("Informe todas as propriedades necessárias: tipo_contato e/ou id_contato (caso tenha).", 400);
}

if(!$contato_existe) {
    $sql = "INSERT INTO CONTATOS(TIPO_CONTATO, ID_USUARIO, CPF)
                VALUES
            (?, ?, ?)";
    $values = array($tipo_contato, $id_usuario, $cpf);

    $stmt = $con->insert($sql, $values);

    die(json_encode(array("msg" => "Contato criado com sucesso")));
} else {
    $sql = "UPDATE CONTATOS SET
                TIPO_CONTATO = ?,
                CPF = ?
            WHERE ID_CONTATO = ?";
    $values = array($tipo_contato, $cpf, $id_contato);

    $stmt = $con->update($sql, $values);

    die(json_encode(array("msg" => "Contato alterado com sucesso"))); 
}

?>