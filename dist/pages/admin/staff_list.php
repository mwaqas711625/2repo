<?php
require_once __DIR__ . '/../../header/header.php';

if (!in_array($roleName, ['admin', 'superadmin'], true)) {
    http_response_code(403);
    exit('Access denied.');
}

// Sirf Staff ka data fetch karein
$sql = "SELECT 
            u.user_id, u.full_name, u.email, u.is_active,
            sp.phone, sp.profile_photo, sp.employee_id, sp.designation, 
            sp.department, sp.shift_timing, sp.joining_date, sp.salary,
            creator.full_name AS created_by_name
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        INNER JOIN staff_profiles sp ON u.user_id = sp.user_id
        LEFT JOIN users creator ON creator.user_id = sp.created_by
        WHERE LOWER(r.role_name) = 'staff'
        ORDER BY u.user_id DESC";

$staffList = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid py-4">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Registered Staff List</h5>
      <a href="./RegDr.php" class="btn btn-light btn-sm fw-bold">+ Add New Staff</a>
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
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Photo</th>
              <th>Staff Info</th>
              <th>Emp ID & Role</th>
              <th>Department & Shift</th>
              <th>Salary & Joining</th>
              <th>Phone</th>
              <th>Status</th>
              <th>Added By</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($staffList) > 0): ?>
              <?php foreach ($staffList as $index => $staff): ?>
                <?php $photoPath = !empty($staff['profile_photo']) ? 'uploads/profiles/' . $staff['profile_photo'] : 'https://placeholder.com'; ?>
                <tr>
                  <td><?= $index + 1; ?></td>
                  <td>
                    <img src="<?= htmlspecialchars($photoPath); ?>" class="rounded-circle" width="45" height="45" style="object-fit: cover;">
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($staff['full_name']); ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars($staff['email']); ?></small>
                  </td>
                  <td>
                    <span class="badge bg-secondary"><?= htmlspecialchars($staff['employee_id']); ?></span><br>
                    <strong><?= htmlspecialchars($staff['designation']); ?></strong>
                  </td>
                  <td>
                    <?= htmlspecialchars($staff['department']); ?><br>
                    <small class="text-muted"><?= htmlspecialchars($staff['shift_timing']); ?></small>
                  </td>
                  <td>
                    <strong>Rs. <?= number_format($staff['salary']); ?></strong><br>
                    <small class="text-muted">Joined: <?= htmlspecialchars($staff['joining_date']); ?></small>
                  </td>
                  <td><?= htmlspecialchars($staff['phone']); ?></td>
                  <td>
                    <?= $staff['is_active'] == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
                  </td>
                  <td><small class="text-muted"><?= htmlspecialchars($staff['created_by_name'] ?? 'Admin'); ?></small></td>
                  <td class="text-center">
                    <a href="edit_user.php?id=<?= (int)$staff['user_id']; ?>" class="btn btn-sm btn-warning">Update</a>
                    <a href="controller/admin.php?delete_id=<?= (int)$staff['user_id']; ?>&redirect=staff" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Kya aap waqai is Staff member ka record delete karna chahte hain?');">
                      Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="10" class="text-center py-4 text-muted">Abhi tak koi Staff member register nahi hua.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
