<?php
// ============================================
// FILE 5: index.php
// Location: crud-app/index.php
// Purpose: READ - List all users
// ============================================

require_once __DIR__ . '/config/database.php';

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="table-container">
    <div class="header-row">
        <h2><i class="fa fa-users" aria-hidden="true"></i> PHP CRUD Operation</h2>
        <a href="create.php" class="btn btn-primary">
            <i class="fa fa-plus-circle"></i> Add New User
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['phone']) ?></td>
                        <td class="action-buttons">
                            <a href="edit.php?id=<?= (int)$user['id'] ?>"
                               class="btn btn-warning btn-sm">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            <a href="delete.php?id=<?= (int)$user['id'] ?>"
                               class="btn btn-danger btn-sm deleteBtn"
                               data-name="<?= e($user['name']) ?>">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                        No users found. Click "Add New User" to create one.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p class="text-muted mt-3 mb-0" style="font-size:0.9rem;">
        <i class="fa fa-info-circle"></i> Total users: <?= count($users) ?>
    </p>
</div>

<script>
// Confirm delete before navigating
$(document).on('click', '.deleteBtn', function(e) {
    var name = $(this).data('name');
    if (!confirm('Are you sure you want to delete "' + name + '"?')) {
        e.preventDefault();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>