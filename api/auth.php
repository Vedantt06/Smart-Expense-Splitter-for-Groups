<?php
// api/auth.php
require_once 'db.php';
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'register') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required.']);
        exit;
    }

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already taken.']);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $hashed_password])) {
        $user_id = $conn->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        echo json_encode(['status' => 'success', 'message' => 'Registration successful.', 'user' => ['id' => $user_id, 'username' => $username]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
    }
} 
elseif ($action == 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo json_encode(['status' => 'success', 'message' => 'Login successful.', 'user' => ['id' => $user['id'], 'username' => $user['username']]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
    }
} 
elseif ($action == 'logout') {
    session_destroy();
    echo json_encode(['status' => 'success', 'message' => 'Logged out.']);
} 
elseif ($action == 'check') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'success', 'user' => ['id' => $_SESSION['user_id'], 'username' => $_SESSION['username']]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    }
} 
elseif ($action == 'users') {
    // Search users for adding to a group
    $term = $_GET['q'] ?? '';
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE username LIKE ? AND id != ? LIMIT 10");
    $stmt->execute(['%' . $term . '%', $_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'users' => $users]);
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
?>
