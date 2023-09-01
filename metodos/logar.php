<?php
include_once("../includes/conexao.php");
include_once("../class/php-jwt/src/JWT.php");
include_once("../includes/auth.php");

use Firebase\JWT\JWT as FirebaseJWT;

$retorno = file_get_contents('php://input');

if (empty($retorno)) {
    retorna_erro("Nenhum dado foi enviado na requisição.", 400);
}

$dados = json_decode($retorno);

if (property_exists($dados, "email") && property_exists($dados, "senha")) {
    $email = $dados->email;
    $senha = $dados->senha;
} else {
    retorna_erro("Informe todas as propriedades necessárias: email e senha.", 400);
}

$sql = "SELECT * FROM USUARIO WHERE EMAIL = ?"; 
$values = array($email);

$count = $con->count($sql, $values);

if($count > 0){
    $data = $con->fetch($sql, $values);
    $email_banco = $data['EMAIL'];
    $senha_banco = $data['SENHA'];

    if(password_verify($senha, $senha_banco)){
        $id_usuario = $data['ID_USUARIO'];
        $nome = $data['NOME_COMPLETO'];
        $email = $data['EMAIL'];

        $payload = [
            'id_usuario' => $id_usuario,
            'email' => $email,
            'nome' => $nome
        ];
                
        $jwt = FirebaseJWT::encode($payload, $API_SECRET, 'HS256');

        die(json_encode(array("msg" => "Entrando no aplicativo!", "token" =>  $jwt )));
    }else{
        retorna_erro("Senha incorreta", 403);
    }

}else{
    retorna_erro("Usuário não encontrado", 403);
}
