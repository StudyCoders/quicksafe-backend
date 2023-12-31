<?php
include_once("../includes/conexao.php");
include_once("../class/php-jwt/src/JWT.php");

use Firebase\JWT\JWT as FirebaseJWT;

$retorno = file_get_contents('php://input');

if (empty($retorno)) {
    retorna_erro("Nenhum dado foi enviado na requisição.", 400);
}

$dados = json_decode($retorno);
$propriedades = array("email", "senha");

if (verificarPropriedades($dados, $propriedades)) {
    $email = $dados->email;
    $senha = $dados->senha;
} else {
    retorna_erro("Informe todas as propriedades necessárias: email e senha.", 400);
}

$sql = "SELECT * FROM USUARIO WHERE EMAIL = ?  AND ATIVO = 'S'";
$values = array($email);

$count = $con->count($sql, $values);

if ($count > 0) {
    $data = $con->fetch($sql, $values);
    $email_banco = $data['EMAIL'];
    $senha_banco = $data['SENHA'];
    // Data que o token foi criado
    $issuedAt = time();
    // jwt válido para 30 dias (60 segundos * 60 minutos * 24 horas * 30 dias)
    $expirationTime = $issuedAt + 60 * 60 * 24 * 30;

    if (password_verify($senha, $senha_banco)) {
        $id_usuario = $data['ID_USUARIO'];
        $nome = $data['NOME_COMPLETO'];
        $email = $data['EMAIL'];
        $cpf = maskCpf($data['CPF']);

        $payload = [
            'id_usuario' => $id_usuario,
            'email' => $email,
            'nome' => $nome,
            'cpf'  => $cpf,
            'existe_form' => existeFormulario($con, $id_usuario),
            'iat' => $issuedAt,
            'exp' => $expirationTime
        ];

        $jwt = FirebaseJWT::encode($payload, $API_SECRET, 'HS256');

        die(json_encode(array("msg" => "Entrando no aplicativo!", "token" =>  $jwt)));
    } else {
        retorna_erro("Senha incorreta", 401);
    }
} else {
    retorna_erro("Usuário não encontrado", 403);
}
