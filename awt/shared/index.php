<?php

header('Content-Type: application/json; charset =utf-8');


if (!isset($_SERVER["CONTENT_TYPE"]) || $_SERVER["CONTENT_TYPE"] != "application/json") {
    http_response_code(403);
    die("Thou shall not pass!!! Forbidden");
}

require_once './classes/class.handler.php';

$request = new Request();

$result = 1;
if($result == 1){
    $apiKeyCheck = $request->checkApiKey($_SERVER);
    if($apiKeyCheck["key_id"] > 0 && !empty($apiKeyCheck["permissions"])){
        $request->process($_SERVER, $apiKeyCheck["permissions"]);
    } else {
        http_response_code(401);
        die('Access Denied');
    }
} else if($result == -1){
    http_response_code(400);
    die("Incomplete request data");
}else{
    http_response_code(429);
    die("Too many requests! Rate limit exceeded.");
}

?>



