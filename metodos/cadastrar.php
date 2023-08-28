<?php
include_once("../includes/conexao.php");

$retorno = file_get_contents('php://input');
$dados = json_decode($retorno);

$nome_completo = $dados->nome_completo;
$email = $dados->email;
$cpf = $dados->cpf;
$senha = password_hash($dados->senha, PASSWORD_BCRYPT);

$sql = "INSERT INTO USUARIO(NOME_COMPLETO, EMAIL, CPF, SENHA)
    VALUES
    (?, ?, ?, ?)";

$values = array($nome_completo, $email, $cpf, $senha);

$stmt = $con->insert($sql, $values);

die(json_encode(array("msg" => "Usuario salvo com sucesso")));
?>