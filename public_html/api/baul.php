<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");

  $endpoint = $_GET['endpoint'] ?? null;

  if(!$endpoint){
    http_response_code(400);
    echo json_encode(["error" => "No se encontró el recurso"]);
    exit;
  }
  $endpoint = preg_replace('/[^a-zA-Z0-9_]/', '', $endpoint);

  $archivo_api = __DIR__ . "/" . $endpoint . "_api.php";

  if(file_exists($archivo_api)){
    require_once $archivo_api;
  }else{
    http_response_code(400);
    echo json_encode(["error" => "El recurso ". $endpoint . " no existe"]);
  }

?>