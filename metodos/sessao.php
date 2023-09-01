<?php
include_once("../class/php-jwt/src/JWT.php");
include_once("../includes/auth.php");

die(json_encode(array("msg" => "Token válido", "data" => $decoded_array )));