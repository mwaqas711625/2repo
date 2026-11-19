<?php 

require_once __DIR__ . '/../../header/header.php';

//<!-- html for page content -->
// Sirf Admin ya SuperAdmin ko is page ki access dein
if (!in_array($roleName, ['admin', 'superadmin'], true)) {
    http_response_code(403);
    exit('Access denied. Only Admin can register Doctor or Staff.');
}

// Roles table se sirf 'doctor' aur 'staff' fetch karein
$rolesQuery = $pdo->query("SELECT role_id, role_name FROM roles WHERE LOWER(role_name) IN ('doctor', 'staff') ORDER BY role_id ASC");
$availableRoles = $rolesQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-10">

      <!-- Success / Error Messages -->
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
          <ul class="mb-0">
            <?php foreach ($_SESSION['errors'] as $error): ?>
              <li><?= htmlspecialchars($error); ?></li>
            <?php endforeach; unset($_SESSION['errors']); ?>
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Department Page</h5>          
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="./../../controller/admin.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Department Name *</label>
                            <input type="text" name="full_name" class="form-control" placeholder="e.g. Cardiology" required>
                        </div>
                    </div>
                
                        <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>


<?php
require_once __DIR__ . '/../../footer/footer.php';
?>