<?php
include_once("../includes/conexao.php");

$retorno = file_get_contents('php://input');

if (empty($retorno)) {
    retorna_erro("Nenhum dado foi enviado na requisição.", 400);
}

$dados = json_decode($retorno);
$propriedades = array("nome_completo", "email", "cpf", "senha");

if (verificarPropriedades($dados, $propriedades)) {
    $nome_completo = strtoupper($dados->nome_completo);
    $email = $dados->email;
    $cpf = preg_replace('/\D/', '', $dados->cpf);
    $senha = password_hash($dados->senha, PASSWORD_BCRYPT);
} else {
    retorna_erro("Informe todas as propriedades necessárias: nome_completo, email, cpf, senha.", 400);
}

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