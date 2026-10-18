<?php
// ============================================
// FILE 8: delete.php
// Location: crud-app/delete.php
// Purpose: DELETE - Remove a user
// ============================================

require_once __DIR__ . '/config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('danger', 'Invalid user ID.');
    header('Location: index.php');
    exit;
}

// Fetch user to get name for the message
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    setFlash('danger', 'User not found.');
    header('Location: index.php');
    exit;
}

// Delete
$delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
$delete->execute([$id]);

setFlash('success', 'User "' . $user['name'] . '" was deleted successfully.');
header('Location: index.php');
exit;