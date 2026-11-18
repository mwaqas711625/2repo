<?php
session_start();
require_once __DIR__ . '/../database/config.php';

// 1. Simple Check: Kya user logged-in hai aur Admin hai?
if (!isset($_SESSION['user']['role_id'])) {
    exit('Access denied: Please login first.');
}

$stmtRole = $pdo->prepare("SELECT role_name FROM roles WHERE role_id = ?");
$stmtRole->execute([$_SESSION['user']['role_id']]);
$userRole = strtolower(trim($stmtRole->fetchColumn()));

if ($userRole !== 'admin' && $userRole !== 'superadmin') {
    exit('Access denied: Only Admin can register users.');
}

// Logged-in Admin ki ID (created_by ke liye)
$admin_id = $_SESSION['user']['user_id'] ?? $_SESSION['user']['role_id'];

// 2. Form POST hone par data receive aur validate karein
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $role_id   = (int) $_POST['role_id'];
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $pass      = $_POST['password'];
    $c_pass    = $_POST['confirm_password'];
    $is_active = (int) ($_POST['is_active'] ?? 1);

    $phone     = trim($_POST['phone']);
    $cnic      = trim($_POST['cnic']);
    $gender    = trim($_POST['gender']);
    $address   = trim($_POST['address']);
    $bio       = trim($_POST['bio']);

    // Basic Validation
    if (empty($full_name) || empty($email) || empty($pass) || empty($phone) || empty($cnic)) {
        $_SESSION['errors'] = ["Tamam zaroori (*) fields bharna lazmi hain."];
        header("Location: ../pages/admin/RegDr.php");
		// die("Tamam zaroori (*) fields bharna lazmi hain.");
        exit();
    }

    if ($pass !== $c_pass) {
        $_SESSION['errors'] = ["Password aur Confirm Password match nahi kar rahe."];
       header("Location: ../pages/admin/RegDr.php");
		// die("Password aur Confirm Password match nahi kar rahe.");
        exit();
    }

    // Check karein email pehle se to nahi hai
    $check = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        $_SESSION['errors'] = ["Ye email pehle se registered hai."];
        header("Location: ../pages/admin/RegDr.php");
		// die("Ye email pehle se registered hai.");
        exit();
    }

    // Check karein post Doctor ki hai ya Staff ki
    $targetStmt = $pdo->prepare("SELECT role_name FROM roles WHERE role_id = ?");
    $targetStmt->execute([$role_id]);
    $targetRole = strtolower(trim($targetStmt->fetchColumn()));

    // Profile Photo Upload (Simple)
    $photo_name = null;
    if (!empty($_FILES['profile_photo']['name'])) {
        $upload_dir = __DIR__ . '/../uploads/profiles/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $photo_name = time() . '_' . basename($_FILES['profile_photo']['name']);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_dir . $photo_name);
    }

    // 3. Database mein Insert Karein
    try {
        $pdo->beginTransaction();

        // Step A: Login data 'users' table mein insert karein
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $sqlUser = "INSERT INTO users (full_name, email, password_hash, role_id, is_active) VALUES (?, ?, ?, ?, ?)";
        $pdo->prepare($sqlUser)->execute([$full_name, $email, $hashed_pass, $role_id, $is_active]);
        
        // Naye user ki ID hasil karein
        $new_user_id = $pdo->lastInsertId();

        // Step B: Agar Doctor hai to 'doctor_profiles' mein insert karein
        if ($targetRole === 'doctor') {
            $sqlDoc = "INSERT INTO doctor_profiles 
                (user_id, phone, cnic, gender, address, profile_photo, pmdc_number, specialization, qualification, experience_years, consultation_fee, clinic_timings, bio, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $pdo->prepare($sqlDoc)->execute([
                $new_user_id,
                $phone,
                $cnic,
                $gender,
                $address,
                $photo_name,
                trim($_POST['pmdc_number']),
                trim($_POST['specialization']),
                trim($_POST['qualification']),
                (int) $_POST['experience_years'],
                $_POST['consultation_fee'],
                trim($_POST['clinic_timings']),
                $bio,
                $admin_id
            ]);

        // Step C: Agar Staff hai to 'staff_profiles' mein insert karein
        } elseif ($targetRole === 'staff') {
            $sqlStaff = "INSERT INTO staff_profiles 
                (user_id, phone, cnic, gender, address, profile_photo, employee_id, designation, department, shift_timing, joining_date, salary, bio, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $pdo->prepare($sqlStaff)->execute([
                $new_user_id,
                $phone,
                $cnic,
                $gender,
                $address,
                $photo_name,
                trim($_POST['employee_id']),
                trim($_POST['designation']),
                trim($_POST['department']),
                trim($_POST['shift_timing']),
                $_POST['joining_date'],
                $_POST['salary'],
                $bio,
                $admin_id
            ]);
        }

        $pdo->commit();

        $_SESSION['success'] = ucfirst($targetRole) . " ($full_name) kamyabi se register ho gaya hai!";
        header("Location: ../pages/admin/RegDr.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['errors'] = ["Error: " . $e->getMessage()];
        // header("Location: ../register_user.php");
		die("An error occurred while registering the user.");
        exit();
    }
}
