<?php
// api/groups.php
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'create') {
    $name = $_POST['name'] ?? '';
    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Group name is required.']);
        exit;
    }

    try {
        $conn->beginTransaction();
        
        $stmt = $conn->prepare("INSERT INTO groups (name, created_by) VALUES (?, ?)");
        $stmt->execute([$name, $user_id]);
        $group_id = $conn->lastInsertId();

        // Add creator as member
        $stmt2 = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt2->execute([$group_id, $user_id]);

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Group created.', 'group_id' => $group_id]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Failed to create group.']);
    }
} 
elseif ($action == 'list') {
    // Get groups the user is a member of
    $stmt = $conn->prepare("
        SELECT g.id, g.name, g.created_at, u.username as creator
        FROM groups g
        JOIN group_members gm ON g.id = gm.group_id
        JOIN users u ON g.created_by = u.id
        WHERE gm.user_id = ?
        ORDER BY g.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'groups' => $groups]);
} 
elseif ($action == 'details') {
    $group_id = $_GET['id'] ?? 0;
    
    // Verify membership
    $stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    if ($stmt->rowCount() == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
        exit;
    }

    // Get group info
    $stmt = $conn->prepare("SELECT id, name FROM groups WHERE id = ?");
    $stmt->execute([$group_id]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get members
    $stmt = $conn->prepare("
        SELECT u.id, u.username 
        FROM users u
        JOIN group_members gm ON u.id = gm.user_id
        WHERE gm.group_id = ?
    ");
    $stmt->execute([$group_id]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'group' => $group, 'members' => $members]);
}
elseif ($action == 'add_member') {
    $group_id = $_POST['group_id'] ?? 0;
    $new_user_id = $_POST['user_id'] ?? 0;

    // Verify current user is a member
    $stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    if ($stmt->rowCount() == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
        exit;
    }

    // Add new member if not exists
    try {
        $stmt = $conn->prepare("INSERT IGNORE INTO group_members (group_id, user_id) VALUES (?, ?)");
        if ($stmt->execute([$group_id, $new_user_id])) {
            echo json_encode(['status' => 'success', 'message' => 'Member added.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add member.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
    }
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
?>
