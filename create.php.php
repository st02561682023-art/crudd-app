<?php
// ============================================
// FILE 6: create.php
// Location: crud-app/create.php
// Purpose: CREATE - Add new user
// ============================================

require_once __DIR__ . '/config/database.php';

$errors = [];
$name = $email = $phone = '';

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

    // Check for duplicate email
    if (empty($errors['email'])) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors['email'] = 'This email is already registered.';
        }
    }

    // Insert if valid
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $phone]);

        setFlash('success', 'User "' . $name . '" was created successfully.');
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="form-container">
    <h2><i class="fa fa-user-plus"></i> Add New User</h2>

    <form method="POST" action="create.php" novalidate>
        <div class="form-group">
            <label for="name">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                   value="<?= e($name) ?>" placeholder="Enter full name">
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?= e($errors['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email"
                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                   value="<?= e($email) ?>" placeholder="Enter email address">
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= e($errors['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="phone">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone" id="phone"
                   class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                   value="<?= e($phone) ?>" placeholder="Enter phone number">
            <?php if (isset($errors['phone'])): ?>
                <div class="invalid-feedback"><?= e($errors['phone']) ?></div>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save User
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>