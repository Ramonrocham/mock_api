<?php
/**
 * Mock API de login para testes
 * API root: /mock_api/api/
 * 
 */
include "MockDataStorage.php";
header("Content-type: application/json");
$api_root = "/mock_api/api/";
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$headers = getallheaders();
$body = file_get_contents("php://input");
if(isset($method) && isset($uri)){
    if($uri == "/mock_api/api/"){
        http_response_code(400);
        echo json_encode(array(
            "mensagem" => "Bad Request"
        ));
    }
    switch($method){
    case "POST":
        switch ($uri) {
            case $api_root."register":
                if(isset($body) && !empty($body)){
                    $data = json_decode($body, true);
                    if(isset($data["username"]) && isset($data["password"]) && isset($data["name"]) && isset($data["email"])){
                        $result = MockDataStorage::newUser($data["username"], $data["password"], $data["name"], $data["email"]);
                        if($result["status"] == "success"){
                            http_response_code(201);
                            echo json_encode(array(
                                "mensagem" => "User created successfully",
                                "username" => $data["username"]
                            ));
                            return;
                        }else{
                            http_response_code(400);
                            echo json_encode(array(
                                "error" => $result["message"] ?? "Erro desconhecido"
                            ));
                            return;
                        }
                    }else{
                        http_response_code(400);
                        echo json_encode(array(
                            "error" => "Missing required fields"
                        ));
                    }
                }
                break;
        }
    break;
    case "GET":
        switch ($uri) {
            case $api_root."username":
                if(isset($_GET["username"]) && !empty($_GET["username"])){
                    $result = MockDataStorage::getUsersByUsername($_GET["username"]);
                    if($result["status"] == "success"){
                        http_response_code(200);
                        echo json_encode(array(
                            "mensagem" => "User found",
                            "user" => $result['data']
                        ));
                    }else{
                        http_response_code(404);
                        echo json_encode(array(
                            "mensagem" => "User not found",
                            "user" => null
                        ));
                    }
                    return;
                }else{
                    http_response_code(400);
                    echo json_encode(array(
                        'error' =>'Username parameter is required'
                    ));
                    return;
                }
            break;
            case $api_root."ping":
                http_response_code(200);
                echo json_encode(array(
                    "mensagem" => 'pong',
                    "status" => 200
                ));
                return;
            break;
        }
    break;
}
}else{
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
}