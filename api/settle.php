<?php
// api/settle.php
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'] ?? 0;

if (!$group_id) {
    echo json_encode(['status' => 'error', 'message' => 'Group ID required.']);
    exit;
}

// Verify membership
$stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
$stmt->execute([$group_id, $user_id]);
if ($stmt->rowCount() == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
    exit;
}

// Calculate Balances
// 1. Get how much each user paid in total
$stmt_paid = $conn->prepare("
    SELECT paid_by as user_id, SUM(amount) as total_paid 
    FROM expenses 
    WHERE group_id = ? 
    GROUP BY paid_by
");
$stmt_paid->execute([$group_id]);
$paid_data = $stmt_paid->fetchAll(PDO::FETCH_ASSOC);

// 2. Get how much each user owes in total
$stmt_owed = $conn->prepare("
    SELECT es.user_id, SUM(es.amount_owed) as total_owed 
    FROM expense_splits es
    JOIN expenses e ON es.expense_id = e.id
    WHERE e.group_id = ?
    GROUP BY es.user_id
");
$stmt_owed->execute([$group_id]);
$owed_data = $stmt_owed->fetchAll(PDO::FETCH_ASSOC);

// Get all usernames in group for reference
$stmt_users = $conn->prepare("
    SELECT u.id, u.username 
    FROM users u
    JOIN group_members gm ON u.id = gm.user_id
    WHERE gm.group_id = ?
");
$stmt_users->execute([$group_id]);
$users_ref = [];
while ($row = $stmt_users->fetch(PDO::FETCH_ASSOC)) {
    $users_ref[$row['id']] = $row['username'];
}

// Combine into net balances
$balances = [];
foreach ($users_ref as $uid => $uname) {
    $balances[$uid] = 0;
}

foreach ($paid_data as $row) {
    $balances[$row['user_id']] += (float)$row['total_paid'];
}

foreach ($owed_data as $row) {
    $balances[$row['user_id']] -= (float)$row['total_owed'];
}

// Settlement Algorithm (Greedy approach)
$debtors = [];
$creditors = [];

foreach ($balances as $uid => $balance) {
    if ($balance < -0.01) {
        $debtors[] = ['id' => $uid, 'name' => $users_ref[$uid], 'amount' => abs($balance)];
    } elseif ($balance > 0.01) {
        $creditors[] = ['id' => $uid, 'name' => $users_ref[$uid], 'amount' => $balance];
    }
}

// Sort both by amount descending to minimize transactions
usort($debtors, function($a, $b) { return $b['amount'] <=> $a['amount']; });
usort($creditors, function($a, $b) { return $b['amount'] <=> $a['amount']; });

$transactions = [];

$i = 0; // debtors index
$j = 0; // creditors index

while ($i < count($debtors) && $j < count($creditors)) {
    $debtor = &$debtors[$i];
    $creditor = &$creditors[$j];

    $settle_amount = min($debtor['amount'], $creditor['amount']);
    
    // Format to 2 decimal places
    $settle_amount = round($settle_amount, 2);

    $transactions[] = [
        'from_id' => $debtor['id'],
        'from_name' => $debtor['name'],
        'to_id' => $creditor['id'],
        'to_name' => $creditor['name'],
        'amount' => $settle_amount
    ];

    $debtor['amount'] -= $settle_amount;
    $creditor['amount'] -= $settle_amount;

    if ($debtor['amount'] < 0.01) $i++;
    if ($creditor['amount'] < 0.01) $j++;
}

// Format balances for display
$formatted_balances = [];
foreach ($balances as $uid => $amount) {
    $formatted_balances[] = [
        'user_id' => $uid,
        'name' => $users_ref[$uid],
        'balance' => round($amount, 2)
    ];
}

echo json_encode([
    'status' => 'success', 
    'balances' => $formatted_balances, 
    'settlements' => $transactions
]);
?>
