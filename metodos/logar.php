<?php
include_once("../includes/conexao.php");

$retorno = file_get_contents('php://input');
$dados = json_decode($retorno);

$email = $dados->email;
$senha = $dados->senha;

$sql = "SELECT * FROM USUARIO WHERE EMAIL = ?";
$values = array($email);

$count = $con->count($sql, $values);

if($count > 0){
    $data = $con->fetch($sql, $values);
    $email_banco = $data['EMAIL'];
    $senha_banco = $data['SENHA'];

    if(password_verify($senha, $senha_banco)){
        die(json_encode(array("msg" => "Entrando no aplicativo!", "id_usuario" => $data['ID_USUARIO'])));
    }else{
        retorna_erro("Senha incorreta", 403);
    }

}else{
    retorna_erro("Usuário não encontrado", 403);
}

?>