<?php
include_once("../class/php-jwt/src/JWT.php");
include_once("../includes/auth.php");


$decoded_array['existe_form'] = existeFormulario($con, $decoded_array['id_usuario']);

die(json_encode(array("msg" => "Token válido", "data" => $decoded_array)));
