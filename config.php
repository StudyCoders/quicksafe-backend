<?php
/* Lidar com o CORS (Cross-Origin Resource Sharing) */

// Especifica os domínios dos quais as solicitações são permitidas
header('Access-Control-Allow-Origin: *');

// Especifica quais métodos de solicitação são permitidos
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');

// Cabeçalhos adicionais que podem ser enviados com a solicitação CORS
header('Access-Control-Allow-Headers: X-Requested-With, Authorization, Content-Type');

// Define o tempo de vida do controle de acesso para 1 dia para melhorar a velocidade/armazenamento em cache.
header('Access-Control-Max-Age: 86400');

// Sai antecipadamente para que a página não seja totalmente carregada para solicitações OPTIONS
if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
    exit();
}