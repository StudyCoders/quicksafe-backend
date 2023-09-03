<?php
include_once("conexao.php");
include_once("../class/php-jwt/src/JWT.php");
include_once("../class/php-jwt/src/Key.php");
include_once("../class/php-jwt/src/SignatureInvalidException.php");
include_once("../class/php-jwt/src/BeforeValidException.php");
include_once("../class/php-jwt/src/ExpiredException.php");

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key as FirebaseKey;
use Firebase\JWT\SignatureInvalidException as FirebaseSignatureInvalidException;
use Firebase\JWT\BeforeValidException as FirebaseBeforeValidException;
use Firebase\JWT\ExpiredException as FirebaseExpiredException;

try {
    /* Checa se o token existe, senão retorna um erro de não autorizado */

    $jwtToken = getBearerToken();

    if (!$jwtToken) {
        retorna_erro("Chave não informada!", 403);
    }
    
    if ( verificarTokenBloqueado($con, $jwtToken) ) {
        retorna_erro("O token fornecido não é mais válido devido ao encerramento da sessão.", 401);
    }

    /* Faz a validação da chave */
    $decoded = FirebaseJWT::decode($jwtToken, new FirebaseKey($API_SECRET, 'HS256'));
    /* Transforma os dados em uma Array */
    $decoded_array = (array) $decoded;

} catch (InvalidArgumentException $e) {
    retorna_erro($e->getMessage(), 400);
} catch (DomainException $e) {
    retorna_erro($e->getMessage(), 400);
} catch (FirebaseSignatureInvalidException $e) {
    retorna_erro($e->getMessage(), 401);
} catch (FirebaseBeforeValidException $e) {
    retorna_erro($e->getMessage(), 401);
} catch (FirebaseExpiredException $e) {
    retorna_erro($e->getMessage(), 401);
} catch (UnexpectedValueException $e) {
    retorna_erro($e->getMessage(), 400);
}
