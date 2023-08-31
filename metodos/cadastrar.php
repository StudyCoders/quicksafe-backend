<?php
include_once("../includes/conexao.php");

$retorno = file_get_contents('php://input');
$dados = json_decode($retorno);

$nome_completo = strtoupper($dados->nome_completo);
$email = $dados->email;
$cpf = preg_replace('/\D/', '', $dados->cpf);
$senha = password_hash($dados->senha, PASSWORD_BCRYPT);

$sql = "SELECT * FROM USUARIO WHERE EMAIL = ?";
$values = array($email);
$count = $con->count($sql, $values);

if($count === 0){
    $sql = "INSERT INTO USUARIO(NOME_COMPLETO, EMAIL, CPF, SENHA)
                VALUES
            (?, ?, ?, ?)";
    $values = array($nome_completo, $email, $cpf, $senha);

    $stmt = $con->insert($sql, $values);

    die(json_encode(array("msg" => "Usuario salvo com sucesso")));
}else{
    retorna_erro("E-mail já existente", 501);
}
?>