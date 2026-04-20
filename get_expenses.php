<?php

header('Content-Type: application/json');

// TEMP TEST (remove later)
$expenses = [
    [
        "paid_by" => "A",
        "amount" => 500,
        "split_between" => ["A", "B"]
    ]
];

echo json_encode($expenses);

?>