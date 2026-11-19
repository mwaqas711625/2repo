<?php
declare(strict_types=1);

namespace Admin;

use PDO;
use Throwable;
use RuntimeException;
use InvalidArgumentException;
require_once __DIR__ . '/../database/config.php';
class Admin
{
    private PDO $pdo;
    private int $adminId;
    private string $uploadDir;

    public function __construct(PDO $pdo, int $adminId, ?string $uploadDir = null)
    {
        $this->pdo = $pdo;
        $this->adminId = $adminId;
        $this->uploadDir = $uploadDir ?? (__DIR__ . '/../uploads/profiles/');
    }

    /**
     * GET: Single user (by user_id) ya sab doctors/staff ki list fetch karein
     */
    public function get(?int $userId = null, ?string $roleFilter = null): array
    {
        if ($userId !== null) {
            $stmt = $this->pdo->prepare("
                SELECT u.user_id, u.full_name, u.email, u.role_id, u.is_active, u.created_at,
                       r.role_name
                FROM users u
                INNER JOIN roles r ON r.role_id = u.role_id
                WHERE u.user_id = ?
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return [];
            }

            $roleName = strtolower(trim((string) $user['role_name']));
            $user['profile'] = $this->getProfileByRole($userId, $roleName);

            return $user;
        }

        $sql = "
            SELECT u.user_id, u.full_name, u.email, u.role_id, u.is_active, u.created_at,
                   r.role_name
            FROM users u
            INNER JOIN roles r ON r.role_id = u.role_id
            WHERE 1=1
        ";
        $params = [];

        if ($roleFilter !== null && $roleFilter !== '') {
            $sql .= " AND LOWER(TRIM(r.role_name)) = ?";
            $params[] = strtolower(trim($roleFilter));
        } else {
            $sql .= " AND LOWER(TRIM(r.role_name)) IN ('doctor', 'staff')";
        }

        $sql .= " ORDER BY u.user_id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as &$row) {
            $roleName = strtolower(trim((string) $row['role_name']));
            $row['profile'] = $this->getProfileByRole((int) $row['user_id'], $roleName);
        }
        unset($row);

        return $users;
    }

    /**
     * POST: Naya Doctor ya Staff register karein (users + profile table)
     */
    public function post(array $data, array $files = []): array
    {
        $roleId   = (int) ($data['role_id'] ?? 0);
        $fullName = trim((string) ($data['full_name'] ?? ''));
        $email    = trim((string) ($data['email'] ?? ''));
        $pass     = (string) ($data['password'] ?? '');
        $cPass    = (string) ($data['confirm_password'] ?? '');
        $isActive = (int) ($data['is_active'] ?? 1);

        $phone    = trim((string) ($data['phone'] ?? ''));
        $cnic     = trim((string) ($data['cnic'] ?? ''));
        $gender   = trim((string) ($data['gender'] ?? ''));
        $address  = trim((string) ($data['address'] ?? ''));
        $bio      = trim((string) ($data['bio'] ?? ''));

        if ($roleId <= 0 || $fullName === '' || $email === '' || $pass === '' || $phone === '' || $cnic === '') {
            throw new InvalidArgumentException("Tamam zaroori (*) fields bharna lazmi hain.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Valid email address enter karein.");
        }

        if ($pass !== $cPass) {
            throw new InvalidArgumentException("Password aur Confirm Password match nahi kar rahe.");
        }

        $check = $this->pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            throw new InvalidArgumentException("Ye email pehle se registered hai.");
        }

        $targetRole = $this->getRoleNameById($roleId);
        if (!in_array($targetRole, ['doctor', 'staff'], true)) {
            throw new InvalidArgumentException("Sirf Doctor ya Staff role allow hai.");
        }

        $creatorId = $this->adminId;
        if ($creatorId <= 0) {
            throw new InvalidArgumentException("Admin account verify nahi ho saka. Dobara login karein.");
        }

        $creatorCheck = $this->pdo->prepare("SELECT user_id FROM users WHERE user_id = ? LIMIT 1");
        $creatorCheck->execute([$creatorId]);
        if (!$creatorCheck->fetchColumn()) {
            throw new InvalidArgumentException("Admin account database mein nahi mila. Dobara login karein.");
        }

        $photoName = $this->handlePhotoUpload($files['profile_photo'] ?? null);

        try {
            $this->pdo->beginTransaction();

            $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
            $sqlUser = "INSERT INTO users (full_name, email, password_hash, role_id, is_active)
                        VALUES (:full_name, :email, :password_hash, :role_id, :is_active)";
            $this->pdo->prepare($sqlUser)->execute([
                'full_name' => $fullName,
                'email' => $email,
                'password_hash' => $hashedPass,
                'role_id' => $roleId,
                'is_active' => $isActive
            ]);

            $newUserId = (int) $this->pdo->lastInsertId();
            if ($newUserId <= 0) {
                throw new RuntimeException("Naya user ID create nahi ho saka.");
            }

            if ($targetRole === 'doctor') {
                $sqlDoc = "INSERT INTO doctor_profiles
                    (user_id, phone, cnic, gender, address, profile_photo, pmdc_number, specialization, qualification, experience_years, consultation_fee, clinic_timings, bio, created_by)
                    VALUES (:user_id, :phone, :cnic, :gender, :address, :profile_photo, :pmdc_number, :specialization, :qualification, :experience_years, :consultation_fee, :clinic_timings, :bio, :created_by)";

                $this->pdo->prepare($sqlDoc)->execute([
                    'user_id' => $newUserId,
                    'phone' => $phone,
                    'cnic' => $cnic,
                    'gender' => $gender,
                    'address' => $address,
                    'profile_photo' => $photoName,
                    'pmdc_number' => trim((string) ($data['pmdc_number'] ?? '')),
                    'specialization' => trim((string) ($data['specialization'] ?? '')),
                    'qualification' => trim((string) ($data['qualification'] ?? '')),
                    'experience_years' => (int) ($data['experience_years'] ?? 0),
                    'consultation_fee' => $data['consultation_fee'] ?? 0,
                    'clinic_timings' => trim((string) ($data['clinic_timings'] ?? '')),
                    'bio' => $bio,
                    'created_by' => $creatorId
                ]);
            } elseif ($targetRole === 'staff') {
                $sqlStaff = "INSERT INTO staff_profiles
                    (user_id, phone, cnic, gender, address, profile_photo, employee_id, designation, department, shift_timing, joining_date, salary, bio, created_by)
                    VALUES (:user_id, :phone, :cnic, :gender, :address, :profile_photo, :employee_id, :designation, :department, :shift_timing, :joining_date, :salary, :bio, :created_by)";

                $this->pdo->prepare($sqlStaff)->execute([
                    'user_id' => $newUserId,
                    'phone' => $phone,
                    'cnic' => $cnic,
                    'gender' => $gender,
                    'address' => $address,
                    'profile_photo' => $photoName,
                    'employee_id' => trim((string) ($data['employee_id'] ?? '')),
                    'designation' => trim((string) ($data['designation'] ?? '')),
                    'department' => trim((string) ($data['department'] ?? '')),
                    'shift_timing' => trim((string) ($data['shift_timing'] ?? '')),
                    'joining_date' => $data['joining_date'] ?? null,
                    'salary' => $data['salary'] ?? 0,
                    'bio' => $bio,
                    'created_by' => $creatorId
                ]);
            }

            $this->pdo->commit();

            return [
                'user_id' => $newUserId,
                'role'    => $targetRole,
                'message' => ucfirst($targetRole) . " ($fullName) kamyabi se register ho gaya hai!"
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            if ($photoName !== null) {
                $this->deletePhotoFile($photoName);
            }
            throw new RuntimeException("User register karte waqt error aaya: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * UPDATE: Maujooda Doctor ya Staff ka data update karein
     */
    public function update(int $userId, array $data, array $files = []): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException("Invalid user ID.");
        }

        $existing = $this->get($userId);
        if (empty($existing)) {
            throw new InvalidArgumentException("User nahi mila.");
        }

        $fullName = trim((string) ($data['full_name'] ?? $existing['full_name']));
        $email    = trim((string) ($data['email'] ?? $existing['email']));
        $isActive = isset($data['is_active']) ? (int) $data['is_active'] : (int) $existing['is_active'];
        $pass     = (string) ($data['password'] ?? '');

        if ($fullName === '' || $email === '') {
            throw new InvalidArgumentException("Full name aur email lazmi hain.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Valid email address enter karein.");
        }

        // Check duplicate email on other users
        $checkEmail = $this->pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $checkEmail->execute([$email, $userId]);
        if ($checkEmail->fetch()) {
            throw new InvalidArgumentException("Ye email kisi aur user ke liye pehle se maujood hai.");
        }

        $targetRole = strtolower(trim((string) $existing['role_name']));
        $currentProfile = $existing['profile'] ?? [];
        $oldPhoto = $currentProfile['profile_photo'] ?? null;

        $newPhoto = $this->handlePhotoUpload($files['profile_photo'] ?? null);
        $photoToSave = $newPhoto ?? $oldPhoto;

        $phone   = trim((string) ($data['phone'] ?? ($currentProfile['phone'] ?? '')));
        $cnic    = trim((string) ($data['cnic'] ?? ($currentProfile['cnic'] ?? '')));
        $gender  = trim((string) ($data['gender'] ?? ($currentProfile['gender'] ?? '')));
        $address = trim((string) ($data['address'] ?? ($currentProfile['address'] ?? '')));
        $bio     = trim((string) ($data['bio'] ?? ($currentProfile['bio'] ?? '')));

        try {
            $this->pdo->beginTransaction();

            if ($pass !== '') {
                $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
                $stmtUser = $this->pdo->prepare("UPDATE users SET full_name = ?, email = ?, password_hash = ?, is_active = ? WHERE user_id = ?");
                $stmtUser->execute([$fullName, $email, $hashedPass, $isActive, $userId]);
            } else {
                $stmtUser = $this->pdo->prepare("UPDATE users SET full_name = ?, email = ?, is_active = ? WHERE user_id = ?");
                $stmtUser->execute([$fullName, $email, $isActive, $userId]);
            }

            if ($targetRole === 'doctor') {
                $sqlDoc = "UPDATE doctor_profiles SET
                    phone = ?, cnic = ?, gender = ?, address = ?, profile_photo = ?,
                    pmdc_number = ?, specialization = ?, qualification = ?,
                    experience_years = ?, consultation_fee = ?, clinic_timings = ?, bio = ?
                    WHERE user_id = ?";

                $this->pdo->prepare($sqlDoc)->execute([
                    $phone,
                    $cnic,
                    $gender,
                    $address,
                    $photoToSave,
                    trim((string) ($data['pmdc_number'] ?? ($currentProfile['pmdc_number'] ?? ''))),
                    trim((string) ($data['specialization'] ?? ($currentProfile['specialization'] ?? ''))),
                    trim((string) ($data['qualification'] ?? ($currentProfile['qualification'] ?? ''))),
                    (int) ($data['experience_years'] ?? ($currentProfile['experience_years'] ?? 0)),
                    $data['consultation_fee'] ?? ($currentProfile['consultation_fee'] ?? 0),
                    trim((string) ($data['clinic_timings'] ?? ($currentProfile['clinic_timings'] ?? ''))),
                    $bio,
                    $userId
                ]);
            } elseif ($targetRole === 'staff') {
                $sqlStaff = "UPDATE staff_profiles SET
                    phone = ?, cnic = ?, gender = ?, address = ?, profile_photo = ?,
                    employee_id = ?, designation = ?, department = ?,
                    shift_timing = ?, joining_date = ?, salary = ?, bio = ?
                    WHERE user_id = ?";

                $this->pdo->prepare($sqlStaff)->execute([
                    $phone,
                    $cnic,
                    $gender,
                    $address,
                    $photoToSave,
                    trim((string) ($data['employee_id'] ?? ($currentProfile['employee_id'] ?? ''))),
                    trim((string) ($data['designation'] ?? ($currentProfile['designation'] ?? ''))),
                    trim((string) ($data['department'] ?? ($currentProfile['department'] ?? ''))),
                    trim((string) ($data['shift_timing'] ?? ($currentProfile['shift_timing'] ?? ''))),
                    $data['joining_date'] ?? ($currentProfile['joining_date'] ?? null),
                    $data['salary'] ?? ($currentProfile['salary'] ?? 0),
                    $bio,
                    $userId
                ]);
            }

            $this->pdo->commit();

            if ($newPhoto !== null && $oldPhoto) {
                $this->deletePhotoFile($oldPhoto);
            }

            return [
                'user_id' => $userId,
                'message' => ucfirst($targetRole) . " ($fullName) ka record update ho gaya hai!"
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            if ($newPhoto !== null) {
                $this->deletePhotoFile($newPhoto);
            }
            throw new RuntimeException("Update karte waqt error aaya: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * DELETE: User aur uska profile + photo delete karein
     */
    public function delete(int $userId): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException("Invalid user ID.");
        }

        if ($userId === $this->adminId) {
            throw new InvalidArgumentException("Aap apna hi active admin account delete nahi kar sakte.");
        }

        $existing = $this->get($userId);
        if (empty($existing)) {
            throw new InvalidArgumentException("Delete karne ke liye user nahi mila.");
        }

        $roleName = strtolower(trim((string) $existing['role_name']));
        $photoName = $existing['profile']['profile_photo'] ?? null;

        try {
            $this->pdo->beginTransaction();

            if ($roleName === 'doctor') {
                $this->pdo->prepare("DELETE FROM doctor_profiles WHERE user_id = ?")->execute([$userId]);
            } elseif ($roleName === 'staff') {
                $this->pdo->prepare("DELETE FROM staff_profiles WHERE user_id = ?")->execute([$userId]);
            }

            $this->pdo->prepare("DELETE FROM users WHERE user_id = ?")->execute([$userId]);

            $this->pdo->commit();

            if ($photoName) {
                $this->deletePhotoFile($photoName);
            }

            return [
                'user_id' => $userId,
                'message' => "User ($userId) kamyabi se delete ho gaya hai."
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new RuntimeException("Delete karte waqt error aaya: " . $e->getMessage(), 0, $e);
        }
    }

    private function getRoleNameById(int $roleId): string
    {
        $stmt = $this->pdo->prepare("SELECT role_name FROM roles WHERE role_id = ?");
        $stmt->execute([$roleId]);
        return strtolower(trim((string) $stmt->fetchColumn()));
    }

    private function getProfileByRole(int $userId, string $roleName): ?array
    {
        if ($roleName === 'doctor') {
            $stmt = $this->pdo->prepare("SELECT * FROM doctor_profiles WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            return $profile ?: null;
        }

        if ($roleName === 'staff') {
            $stmt = $this->pdo->prepare("SELECT * FROM staff_profiles WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            return $profile ?: null;
        }

        return null;
    }

    private function handlePhotoUpload(?array $file): ?string
    {
        if (empty($file) || empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Photo upload mein error aaya.");
        }

        if (!is_dir($this->uploadDir) && !mkdir($this->uploadDir, 0775, true) && !is_dir($this->uploadDir)) {
            throw new RuntimeException("Upload directory create nahi ho saki.");
        }

        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename((string) $file['name']));
        $photoName = time() . '_' . bin2hex(random_bytes(4)) . '_' . $safeName;
        $destination = rtrim($this->uploadDir, '/\\') . DIRECTORY_SEPARATOR . $photoName;

        if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
            throw new RuntimeException("Photo server par move nahi ho saki.");
        }

        return $photoName;
    }

    private function deletePhotoFile(string $photoName): void
    {
        $filePath = rtrim($this->uploadDir, '/\\') . DIRECTORY_SEPARATOR . basename($photoName);
        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }
}