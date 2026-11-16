<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if (
	!isset($_SESSION['user'])
	|| !is_array($_SESSION['user'])
	|| empty($_SESSION['user']['email'])
	|| !isset($_SESSION['user']['role_id'])
	|| (int) $_SESSION['user']['role_id'] <= 0
) {
	header('Location: ../index.php');
	exit;
}

require_once __DIR__ . '../../../database/config.php';

$roleStatement = $pdo->prepare('SELECT role_name FROM roles WHERE role_id = :role_id LIMIT 1');
$roleStatement->execute(['role_id' => (int) $_SESSION['user']['role_id']]);
$role = $roleStatement->fetch(PDO::FETCH_ASSOC);
$roleName = strtolower(trim((string) ($role['role_name'] ?? '')));

$sidebarMenus = [
	'admin' => [
		['Dashboard', 'dashboard.php'],
		['Patients', 'patients.php'],
		['Appointments', 'appointments.php'],
		['Doctors', 'doctors.php'],
		['Staff', 'staff.php'],
		['Billing', 'billing.php'],
		['Reports', 'reports.php'],
	],
	'doctor' => [
		['Dashboard', 'dashboard.php'],
		['My Patients', 'patients.php'],
		['Appointments', 'appointments.php'],
		['My Profile', 'profile.php'],
	],
	'staff' => [
		['Dashboard', 'dashboard.php'],
		['Patients', 'patients.php'],
		['Appointments', 'appointments.php'],
		['Billing', 'billing.php'],
	],
	'patient' => [
		['Dashboard', 'dashboard.php'],
		['My Appointments', 'appointments.php'],
		['My Profile', 'profile.php'],
	],
];

if (!isset($sidebarMenus[$roleName])) {
	http_response_code(403);
	exit('Access denied.');
}
?>