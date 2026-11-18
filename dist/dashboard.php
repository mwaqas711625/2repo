<?php
require_once __DIR__ . '/header/header.php';
if($roleName === 'admin'){
    $dashboardTitle = 'Admin Dashboard';
// show the values of data doctor and staff list in the dashboard for admin
// Total Doctors aur Total Staff count karne ki query
$countStmt = $pdo->query("
    SELECT 
        SUM(CASE WHEN LOWER(r.role_name) = 'doctor' THEN 1 ELSE 0 END) AS total_doctors,
        SUM(CASE WHEN LOWER(r.role_name) = 'staff' THEN 1 ELSE 0 END) AS total_staff
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
");
$counts = $countStmt->fetch(PDO::FETCH_ASSOC);

$totalDoctors = (int) ($counts['total_doctors'] ?? 0);
$totalStaff   = (int) ($counts['total_staff'] ?? 0);

    ?>
<!-- dashboard content for admin -->
   
            <div class="row">
  
  <!-- Total Doctors Box -->
  <div class="col-lg-3 col-6">
    <div class="small-box text-bg-primary">
      <div class="inner">
        <h3><?= $totalDoctors; ?></h3>
        <p>Total Doctors</p>
      </div>
      <!-- Doctor Icon (Bootstrap Icons / FontAwesome) -->
      <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
      </svg>
      <a href="./pages/admin/dr_list.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
        More info <i class="bi bi-link-45deg"></i>
      </a>
    </div>
  </div>

  <!-- Total Staff Box -->
  <div class="col-lg-3 col-6">
    <div class="small-box text-bg-success">
      <div class="inner">
        <h3><?= $totalStaff; ?></h3>
        <p>Total Staff</p>
      </div>
      <!-- Staff Icon -->
      <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
      </svg>
      <a href="./pages/admin/staff_list.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
        More info <i class="bi bi-link-45deg"></i>
      </a>
    </div>
  </div>

</div>

              
<?php 
} 
else if($roleName === 'doctor'){
    $dashboardTitle = 'Doctor Dashboard';
    // Additional logic for doctor dashboard
    ?>
<!-- dashboard content for doctor -->
  
            <div class="row">
              
            </div>
            <!--end::Row-->

<?php
    
} 
else if($roleName === 'staff'){
    $dashboardTitle = 'Staff Dashboard';
    // Additional logic for staff dashboard
        ?>
<!-- dashboard content for staff -->
            <!--begin::Row-->
            <div class="row">
              
            </div>
            <!--end::Row-->
      
<?php
}
elseif($roleName === 'patient'){
    $dashboardTitle = 'Patient Dashboard';
    // Additional logic for patient dashboard
        ?>
<!-- dashboard content for patient -->
    <div class="row">
              
      </div> 
       <!--end::Row-->
      
<?php
}

require_once __DIR__ .'/footer/footer.php'
?>
