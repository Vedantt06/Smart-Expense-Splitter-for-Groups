<?php

header('Content-Type: application/json');


// ================== YOUR LOGIC ==================

function calculateBalances($users, $expenses) {
    $balances = [];

    foreach ($users as $user) {
        $balances[$user] = 0;
    }

    foreach ($expenses as $expense) {
        $paidBy = $expense['paid_by'];
        $amount = $expense['amount'];
        $splitUsers = $expense['split_between'];

        $splitAmount = $amount / count($splitUsers);

        $balances[$paidBy] += $amount;

        foreach ($splitUsers as $user) {
            $balances[$user] -= $splitAmount;
        }
    }

    return $balances;
}

function settleBalances($balances) {
    $creditors = [];
    $debtors = [];
    $transactions = [];

    foreach ($balances as $user => $amount) {
        if ($amount > 0) $creditors[$user] = round($amount, 2);
        elseif ($amount < 0) $debtors[$user] = round($amount, 2);
    }

    while (!empty($creditors) && !empty($debtors)) {

        $creditor = array_keys($creditors, max($creditors))[0];
        $debtor = array_keys($debtors, min($debtors))[0];

        $settleAmount = min($creditors[$creditor], abs($debtors[$debtor]));

        $transactions[] = [
            "from" => $debtor,
            "to" => $creditor,
            "amount" => round($settleAmount, 2)
        ];

        $creditors[$creditor] -= $settleAmount;
        $debtors[$debtor] += $settleAmount;

        if (round($creditors[$creditor], 2) == 0) unset($creditors[$creditor]);
        if (round($debtors[$debtor], 2) == 0) unset($debtors[$debtor]);
    }

    return $transactions;
}


// ================== TEMP TEST ==================

$users = ["A", "B", "C"];

$expenses = [
    [
        "paid_by" => "A",
        "amount" => 900,
        "split_between" => ["A", "B", "C"]
    ]
];

$result = settleBalances(calculateBalances($users, $expenses));

echo json_encode($result);

?>
