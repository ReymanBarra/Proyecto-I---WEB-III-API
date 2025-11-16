<?php
header("Content-Type: application/json");
echo json_encode([
    "api" => "Proyecto WEB III API",
    "status" => "running",
    "message" => "API funcionando correctamente"
]);
