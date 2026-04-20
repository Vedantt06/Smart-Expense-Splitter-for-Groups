<?php
// api/expenses.php
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'add') {
    $group_id = $_POST['group_id'] ?? 0;
    $amount = $_POST['amount'] ?? 0;
    $description = $_POST['description'] ?? '';
    // JSON array of user IDs who are splitting this
    $split_with = isset($_POST['split_with']) ? json_decode($_POST['split_with'], true) : [];

    if (empty($group_id) || empty($amount) || empty($description) || empty($split_with)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Verify membership
    $stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    if ($stmt->rowCount() == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
        exit;
    }

    try {
        $conn->beginTransaction();

        // 1. Insert Expense
        $stmt = $conn->prepare("INSERT INTO expenses (group_id, paid_by, amount, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$group_id, $user_id, $amount, $description]);
        $expense_id = $conn->lastInsertId();

        // 2. Insert Splits (Equal split for simplicity, or we can just divide evenly)
        $num_people = count($split_with);
        $split_amount = $amount / $num_people;

        $stmt2 = $conn->prepare("INSERT INTO expense_splits (expense_id, user_id, amount_owed) VALUES (?, ?, ?)");
        foreach ($split_with as $split_user_id) {
            $stmt2->execute([$expense_id, $split_user_id, $split_amount]);
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Expense added.']);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Failed to add expense.']);
    }
} 
elseif ($action == 'list') {
    $group_id = $_GET['group_id'] ?? 0;

    // Verify membership
    $stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    if ($stmt->rowCount() == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT e.id, e.amount, e.description, e.created_at, u.username as paid_by_name
        FROM expenses e
        JOIN users u ON e.paid_by = u.id
        WHERE e.group_id = ?
        ORDER BY e.created_at DESC
    ");
    $stmt->execute([$group_id]);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'expenses' => $expenses]);
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
?>
