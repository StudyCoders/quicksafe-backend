<?php
include_once("../includes/conexao.php");

$retorno = file_get_contents('php://input');
$dados = json_decode($retorno);

$nome_completo = $dados->nome_completo;
$email = $dados->email;
$cpf = $dados->cpf;
$senha = password_hash($dados->senha, PASSWORD_BCRYPT);

try{
    $sql = "INSERT INTO USUARIO(NOME_COMPLETO, EMAIL, CPF, SENHA)
                VALUES
            (?, ?, ?, ?)";
    $values = array($nome_completo, $email, $cpf, $senha);

    $con->insert($sql, $values);

    die(json_encode(array("msg"=>"Usuario salvo com sucesso")));

}catch(Exception $e){
    die(json_encode(array("error"=>"Erro ao salvar usuario" . $e->getMessage() )));
}
?>