<?php
include_once("../includes/conexao.php");
include_once("../class/php-jwt/src/JWT.php");
include_once("../includes/auth.php");

bloquearToken($con, $jwtToken);

die(json_encode(array("msg" => "Logout efetuado com sucesso!")));
?>