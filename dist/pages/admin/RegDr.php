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
            <h5 class="mb-0">Register Doctor / Staff & Create Login</h5>          
            </div>

            <div class="card-body p-4 p-md-5">
            <form action="./../../controller/admin.php" method="POST" enctype="multipart/form-data">

                <!-- Role Selection -->
                <div class="row mb-4 bg-light p-3 rounded border">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-primary">Select Role *</label>
                    <select name="role_id" id="roleSelect" class="form-select form-select-lg" required>
                    <?php foreach ($availableRoles as $r): ?>
                        <option value="<?= (int)$r['role_id']; ?>" data-rolename="<?= strtolower(trim($r['role_name'])); ?>">
                        <?= ucfirst($r['role_name']); ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </div>
                </div>

                <!-- 1. Login Credentials (Ye data 'users' table mein jayega) -->
                <h5 class="text-secondary border-bottom pb-2 mb-3">1. Portal Login Credentials (Users Table)</h5>
                <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" placeholder="e.g. Dr. Ali Ahmad" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Email (Login ID) *</label>
                    <input type="email" name="email" class="form-control" placeholder="user@gmail.com" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Account Status *</label>
                    <select name="is_active" class="form-select" required>
                    <option value="1" selected>Active (1)</option>
                    <option value="0">Inactive (0)</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                </div>
                </div>

                <!-- 2. Personal Bio (Ye data profile tables mein jayega) -->
                <h5 class="text-secondary border-bottom pb-2 mb-3">2. Personal Bio Information</h5>
                <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="phone" class="form-control" placeholder="0300-1234567" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">CNIC Number *</label>
                    <input type="text" name="cnic" class="form-control" placeholder="35202-1234567-1" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-select" required>
                    <option value="" disabled selected>Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-12 col-md-8">
                    <label class="form-label">Complete Address *</label>
                    <input type="text" name="address" class="form-control" placeholder="House #, Street, City" required>
                </div>
                <div class="col-12 col-md-8">
                    <label class="form-label">Bio *</label>
                    <input type="text" name="bio" class="form-control" placeholder="Brief description about the doctor" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                </div>
                </div>

                <!-- 3A. DOCTOR SPECIFIC BIO FIELDS -->
                <div id="doctorFields">
                <h5 class="text-primary border-bottom pb-2 mb-3">3. Doctor Professional Bio</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                    <label class="form-label">PMDC Registration No *</label>
                    <input type="text" name="pmdc_number" class="form-control doc-input" placeholder="e.g. 12345-P">
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Specialization *</label>
                    <input type="text" name="specialization" class="form-control doc-input" placeholder="e.g. Cardiologist">
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Qualification *</label>
                    <input type="text" name="qualification" class="form-control doc-input" placeholder="e.g. MBBS, FCPS">
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Experience (Years) *</label>
                    <input type="number" name="experience_years" class="form-control doc-input" min="0">
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Consultation Fee (PKR) *</label>
                    <input type="number" name="consultation_fee" class="form-control doc-input" min="0">
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Clinic Timings *</label>
                    <input type="text" name="clinic_timings" class="form-control doc-input" placeholder="e.g. 5:00 PM - 9:00 PM">
                    </div>
                </div>
                </div>

                <!-- 3B. STAFF SPECIFIC BIO FIELDS -->
                <div id="staffFields" class="d-none">
                <h5 class="text-success border-bottom pb-2 mb-3">3. Staff Job Bio</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                    <label class="form-label">Employee ID *</label>
                    <input type="text" name="employee_id" class="form-control staff-input" placeholder="e.g. EMP-101">
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Designation *</label>
                    <input type="text" name="designation" class="form-control staff-input" placeholder="e.g. Receptionist, Nurse">
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Department *</label>
                    <input type="text" name="department" class="form-control staff-input" placeholder="e.g. OPD, Lab">
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Shift Timing *</label>
                    <select name="shift_timing" class="form-select staff-input">
                        <option value="Morning (8AM - 4PM)">Morning (8AM - 4PM)</option>
                        <option value="Evening (4PM - 12AM)">Evening (4PM - 12AM)</option>
                        <option value="Night (12AM - 8AM)">Night (12AM - 8AM)</option>
                    </select>
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Joining Date *</label>
                    <input type="date" name="joining_date" class="form-control staff-input">
                    </div>
                    <div class="col-12 col-md-4">
                    <label class="form-label">Monthly Salary (PKR) *</label>
                    <input type="number" name="salary" class="form-control staff-input" min="0">
                    </div>
                </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
  </div>                        
<!-- html for page content end here -->
<script>
    const roleSelect = document.getElementById('roleSelect');
const doctorFields = document.getElementById('doctorFields');
const staffFields = document.getElementById('staffFields');
const docInputs = document.querySelectorAll('.doc-input');
const staffInputs = document.querySelectorAll('.staff-input');
function toggleRoleFields() {
const selectedOption = roleSelect.options[roleSelect.selectedIndex];
const roleName = selectedOption.getAttribute('data-rolename');
if (roleName === 'doctor') {
doctorFields.classList.remove('d-none');
staffFields.classList.add('d-none');
docInputs.forEach(i => i.setAttribute('required', 'required'));
staffInputs.forEach(i => i.removeAttribute('required'));
} else {
staffFields.classList.remove('d-none');
doctorFields.classList.add('d-none');
staffInputs.forEach(i => i.setAttribute('required', 'required'));
docInputs.forEach(i => i.removeAttribute('required'));
}
}
roleSelect.addEventListener('change', toggleRoleFields);
window.addEventListener('DOMContentLoaded', toggleRoleFields);

</script>

<?php
require_once __DIR__ . '/../../footer/footer.php';
?>