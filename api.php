<?php
header("Content-type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$headers = getallheaders();
$body = file_get_contents("php://input");
//$data = json_decode($body, true);

