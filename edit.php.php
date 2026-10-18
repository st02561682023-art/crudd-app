<?php
// ============================================
// FILE 7: edit.php
// Location: crud-app/edit.php
// Purpose: UPDATE - Edit existing user
// ============================================

require_once __DIR__ . '/config/database.php';

$errors = [];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('danger', 'Invalid user ID.');
    header('Location: index.php');
    exit;
}

// Fetch existing user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    setFlash('danger', 'User not found.');
    header('Location: index.php');
    exit;
}

$name  = $user['name'];
$email = $user['email'];
$phone = $user['phone'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Validation
    if ($name === '') {
        $errors['name'] = 'Name is required.';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Name must not exceed 100 characters.';
    }

    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($phone === '') {
        $errors['phone'] = 'Phone is required.';
    } elseif (strlen($phone) > 30) {
        $errors['phone'] = 'Phone must not exceed 30 characters.';
    }

    // Check duplicate email (excluding current user)
    if (empty($errors['email'])) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->execute([$email, $id]);
        if ($check->fetch()) {
            $errors['email'] = 'This email is already used by another user.';
        }
    }

    // Update if valid
    if (empty($errors)) {
        $update = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $update->execute([$name, $email, $phone, $id]);

        setFlash('success', 'User "' . $name . '" was updated successfully.');
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="form-container">
    <h2><i class="fa fa-user-edit"></i> Edit User</h2>

    <form method="POST" action="edit.php?id=<?= (int)$id ?>" novalidate>
        <div class="form-group">
            <label for="name">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                   value="<?= e($name) ?>">
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?= e($errors['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email"
                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                   value="<?= e($email) ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= e($errors['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="phone">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone" id="phone"
                   class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                   value="<?= e($phone) ?>">
            <?php if (isset($errors['phone'])): ?>
                <div class="invalid-feedback"><?= e($errors['phone']) ?></div>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-warning">
                <i class="fa fa-save"></i> Update User
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>