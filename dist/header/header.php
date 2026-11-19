<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$appRoot = realpath(__DIR__ . '/..');
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
if ($appRoot === false || $documentRoot === false) {
    throw new RuntimeException('Unable to determine the application URL base.');
}

$appRootPath = str_replace('\\', '/', $appRoot);
$documentRootPath = rtrim(str_replace('\\', '/', $documentRoot), '/');
if ($appRootPath === $documentRootPath) {
    $appBaseUrl = '';
} elseif (stripos($appRootPath, $documentRootPath . '/') === 0) {
    $appBaseUrl = substr($appRootPath, strlen($documentRootPath));
} else {
    throw new RuntimeException('The application directory is outside the document root.');
}

$siteBaseUrl = $appBaseUrl === '' ? '' : rtrim(dirname($appBaseUrl), '/');
$loginUrl = $siteBaseUrl . '/index.php';

if (
    !isset($_SESSION['user'])
    || !is_array($_SESSION['user'])
    || empty($_SESSION['user']['email'])
    || !isset($_SESSION['user']['role_id'])
    || (int) $_SESSION['user']['role_id'] <= 0
) {
    header('Location: ' . $loginUrl);
    exit;
}

require_once __DIR__ . '/../database/config.php';

$roleStatement = $pdo->prepare('SELECT role_name FROM roles WHERE role_id = :role_id LIMIT 1');
$roleStatement->execute(['role_id' => (int) $_SESSION['user']['role_id']]);
$role = $roleStatement->fetch();
$roleName = strtolower(trim((string) ($role['role_name'] ?? '')));

if (!in_array($roleName, ['admin', 'doctor', 'staff'], true)) {
    http_response_code(403);
    exit('Access denied.');
}
$dashboardTitle = ucfirst($roleName) . ' Dashboard';
?>
<!-- main content of html till header -->
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= htmlspecialchars($dashboardTitle, ENT_QUOTES, 'UTF-8') ?> | HMS</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($appBaseUrl, ENT_QUOTES, 'UTF-8') ?>/css/adminlte.css" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.3.0/index.css"
      integrity="sha256-5UwXtC6TaPHCAbVQ59waEAqOssHyhn/twyuLRRWszzo="
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.16.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="<?= htmlspecialchars($appBaseUrl, ENT_QUOTES, 'UTF-8') ?>/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->

    <!-- jsvectormap -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/jsvectormap@1.7.0/dist/jsvectormap.min.css"
      integrity="sha256-Zshqz6X+Rs2Rf5sPtiSba+rwLBL1ZTyLBppbYOstR9M="
      crossorigin="anonymous"
    />
  </head>
  <!--end::Head-->
<!-- main content of html till header -->
   <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a
                class="nav-link"
                data-lte-toggle="sidebar"
                href="#"
                role="button"
                aria-label="Toggle sidebar"
              >
                <i class="bi bi-list"></i>
              </a>
            </li>
            <!-- logout -->
             <li class="nav-item">
              <a class="nav-link" href="<?= htmlspecialchars($appBaseUrl, ENT_QUOTES, 'UTF-8') ?>/controller/logout.php" role="button" aria-label="Logout">
                <button type="button" class="btn btn-danger">Logout</button>
  
              </a>
            </li>
          </ul>
          <!--end::Start Navbar Links-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--begin::Body-->
      <!--end::Header-->
      <?php include __DIR__ . '/../sidebar/sidebar.php'; ?>
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6">
                <h1 class="mb-0 fs-3"><?= htmlspecialchars($dashboardTitle, ENT_QUOTES, 'UTF-8') ?></h1>
              </div>
              <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->