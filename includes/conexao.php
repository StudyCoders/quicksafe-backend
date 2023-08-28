<?php
include_once("../class/Database.class.php");
include_once("../environments/environments.php");

$hostname = $CFG_PROD["hostname"];
$username = $CFG_PROD["username"];
$password = $CFG_PROD["password"];
$database = $CFG_PROD["database"];

$con = new db($hostname, $username, $password, $database);