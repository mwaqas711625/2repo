
<?php

require_once __DIR__ . '/admin_init.php';

// Sab doctors aur staff ka data GET karein
$allStaffAndDoctors = $admin->get();

// Sirf doctors ka data GET karein
$doctors = $admin->get(null, 'doctor');

// Sirf staff ka data GET karein
$staff = $admin->get(null, 'staff');

// Kisi ek user (e.g. user_id = 12) ka data GET karein
$singleUser = $admin->get(12);
?>