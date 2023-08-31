<?php
function retorna_erro($msg, $cod){
    http_response_code($cod);
    die(json_encode(array("erro" => $msg)));
}
?>