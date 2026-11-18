<?php
require_once __DIR__ . '/../../header/header.php';

if (!in_array($roleName, ['admin', 'superadmin'], true)) {
    http_response_code(403);
    exit('Access denied.');
}

// Sirf Doctors ka data fetch karein
$sql = "SELECT 
            u.user_id, u.full_name, u.email, u.is_active,
            dp.phone, dp.profile_photo, dp.pmdc_number, dp.specialization, 
            dp.qualification, dp.experience_years, dp.consultation_fee, dp.clinic_timings,
            creator.full_name AS created_by_name
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        INNER JOIN doctor_profiles dp ON u.user_id = dp.user_id
        LEFT JOIN users creator ON creator.user_id = dp.created_by
        WHERE LOWER(r.role_name) = 'doctor'
        ORDER BY u.user_id DESC";

$doctors = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid py-4">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Registered Doctors List</h5>
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
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Photo</th>
              <th>Doctor Info</th>
              <th>PMDC & Qualification</th>
              <th>Specialization</th>
              <th>Fee & Timings</th>
              <th>Phone</th>
              <th>Status</th>
              <th>Added By</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($doctors) > 0): ?>
              <?php foreach ($doctors as $index => $doc): ?>
                <?php $photoPath = !empty($doc['profile_photo']) ? 'uploads/profiles/' . $doc['profile_photo'] : 'https://placeholder.com'; ?>
                <tr>
                  <td><?= $index + 1; ?></td>
                  <td>
                    <img src="<?= htmlspecialchars($photoPath); ?>" class="rounded-circle" width="45" height="45" style="object-fit: cover;">
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($doc['full_name']); ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars($doc['email']); ?></small>
                  </td>
                  <td>
                    <span class="badge bg-info text-dark"><?= htmlspecialchars($doc['pmdc_number']); ?></span><br>
                    <small><?= htmlspecialchars($doc['qualification']); ?> (<?= (int)$doc['experience_years']; ?> Yrs)</small>
                  </td>
                  <td><?= htmlspecialchars($doc['specialization']); ?></td>
                  <td>
                    <strong>Rs. <?= number_format($doc['consultation_fee']); ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars($doc['clinic_timings']); ?></small>
                  </td>
                  <td><?= htmlspecialchars($doc['phone']); ?></td>
                  <td>
                    <?= $doc['is_active'] == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
                  </td>
                  <td><small class="text-muted"><?= htmlspecialchars($doc['created_by_name'] ?? 'Admin'); ?></small></td>
                  <td class="text-center">
                    <a href="edit_user.php?id=<?= (int)$doc['user_id']; ?>" class="btn btn-sm btn-warning">Update</a>
                    <a href="controller/admin.php?delete_id=<?= (int)$doc['user_id']; ?>&redirect=doctors" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Kya aap waqai is Doctor ka record delete karna chahte hain?');">
                      Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="10" class="text-center py-4 text-muted">Abhi tak koi Doctor register nahi hua.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
