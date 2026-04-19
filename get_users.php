<?php

header("Content-Type: application/json");

echo json_encode([
    ["id" => 1, "name" => "You"],
    ["id" => 2, "name" => "Alice"],
    ["id" => 3, "name" => "Bob"],
    ["id" => 4, "name" => "Charlie"]
]);

?>