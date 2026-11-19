<?php
declare(strict_types=1);

require_once __DIR__ . '/../../controller/adminBlock/admin_init.php';
require_once __DIR__ . '/../../header/header.php';
$records = $admin->get(null, 'doctor');

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<?php if (!empty($_SESSION['success'])): ?>
    <div style="color: green; margin-bottom: 12px;">
        <?= e($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['errors'])): ?>
    <div style="color: red; margin-bottom: 12px;">
        <?php foreach ($_SESSION['errors'] as $err): ?>
            <div><?= e((string) $err) ?></div>
        <?php endforeach; ?>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<div class="container-fluid py-4">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Registered Staff List</h5>
      <a href="./RegDr.php" class="btn btn-light btn-sm fw-bold">+ Add New Doctor</a>
    </div>

    <div class="card-body">
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
         <thead>
        <tr>
            <th>#</th>
            <th>Photo</th>
            <th>Name &amp; Role</th>
            <th>Email &amp; Phone</th>
            <th>Role Specific Details</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
        <tbody>
        <?php if (empty($records)): ?>
            <tr>
                <td colspan="7" align="center">Koi record nahi mila.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($records as $index => $row): ?>
                <?php
                $userId   = (int) $row['user_id'];
                $roleName = strtolower(trim((string) $row['role_name']));
                $profile  = is_array($row['profile'] ?? null) ? $row['profile'] : [];
                $photo    = !empty($profile['profile_photo'])
                    ? '../../uploads/profiles/' . rawurlencode((string) $profile['profile_photo'])
                    : null;
                ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <?php if ($photo): ?>
                            <img src="<?= e($photo) ?>" alt="<?= e($row['full_name']) ?>" width="50" height="50" style="object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <span>No Photo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= e($row['full_name']) ?></strong><br>
                        <small><?= e(ucfirst($roleName)) ?></small>
                    </td>
                    <td>
                        <?= e($row['email']) ?><br>
                        <small><?= e($profile['phone'] ?? '-') ?></small>
                    </td>
                    <td>
                        <?php if ($roleName === 'doctor'): ?>
                            <strong>Specialization:</strong> <?= e($profile['specialization'] ?? '-') ?><br>
                            <strong>PMDC:</strong> <?= e($profile['pmdc_number'] ?? '-') ?><br>
                            <strong>Fee:</strong> <?= e((string) ($profile['consultation_fee'] ?? '0')) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= ((int) $row['is_active'] === 1) ? 'Active' : 'Inactive' ?>
                    </td>
                    <td>
                        <!-- Edit link / form trigger -->
                        <a href="edit_user.php?id=<?= $userId ?>">
                            <button type="button" class="btn btn-sm btn-primary">Edit</button>
                        </a>

                        <!-- Delete form calling actions/delete_user.php -->
                        <form action="../../controller/adminBlock/dlt_data.php" method="POST" style="display:inline;" onsubmit="return confirm('Kya aap waqai is record ko delete karna chahte hain?');">
                            <input type="hidden" name="user_id"  value="<?= $userId ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</table>
      </div>
    </div>
  </div>
</div>


<table border="1" cellpadding="8" cellspacing="0" width="100%">
    
    
</body>
</html>