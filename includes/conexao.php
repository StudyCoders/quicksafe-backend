<?php
include_once("../class/Database.class.php");
include_once("../environments/environments.php");
include_once("config.php");
include_once("funcoes.php");

$hostname = $CFG_PROD["hostname"];
$username = $CFG_PROD["username"];
$password = $CFG_PROD["password"];
$database = $CFG_PROD["database"];

$con = new db($hostname, $username, $password, $database);