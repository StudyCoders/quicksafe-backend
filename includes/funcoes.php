<?php
include_once("../environments/environments.php");

function retorna_erro($msg, $cod)
{
	http_response_code($cod);
	die(json_encode(array("erro" => $msg)));
}

function getAuthorizationHeader()
{
	$headers = null;
	if (isset($_SERVER['Authorization'])) {
		$headers = trim($_SERVER["Authorization"]);
	} else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
		$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
	} elseif (function_exists('apache_request_headers')) {
		$requestHeaders = apache_request_headers();

		$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

		if (isset($requestHeaders['Authorization'])) {
			$headers = trim($requestHeaders['Authorization']);
		}
	}
	return $headers;
}

function getBearerToken()
{
	$headers = getAuthorizationHeader();

	if (!empty($headers)) {
		if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
			return $matches[1];
		}
	}
	return null;
}

function gerarHashToken($token) {
	return hash('sha256',$token);
}

function bloquearToken($con, $token)
{
	$hashedToken = gerarHashToken($token);
	$con->insert("INSERT INTO TOKENS_BLOQUEADOS(TOKEN_HASH) VALUES (?)", array($hashedToken));
}

function verificarTokenBloqueado($con, $token)
{
	$hashedToken = gerarHashToken($token);
	$query = "SELECT TOKEN_HASH FROM TOKENS_BLOQUEADOS WHERE TOKEN_HASH = ?";
	$totalLinhas = $con->count($query, array($hashedToken));

	if ($totalLinhas > 0) {
		// O token está na lista de bloqueio
		return true;
	} else {
		// O token não está na lista de bloqueio
		return false;
	}
}
