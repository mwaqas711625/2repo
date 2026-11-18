<?php 
require_once __DIR__ . '/sidebarPages.php';
?>
  <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="<?= htmlspecialchars($appBaseUrl, ENT_QUOTES, 'UTF-8') ?>/dashboard.php" class="brand-link">
            <!--begin::Brand Image-->
            <img
              src="<?= htmlspecialchars($appBaseUrl, ENT_QUOTES, 'UTF-8') ?>/assets/img/AdminLTELogo.png"
              alt="AdminLTE Logo"
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">HMS</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2" aria-label="Main navigation">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              data-accordion="false"
              id="navigation"
            >
            
              <li class="nav-item menu-open">
                <a href="#" class="nav-link active">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>
                    Dashboard
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php foreach ($sidebarMenus[$roleName] as [$label, $href]): ?>
                <li class="nav-item">
                    <a href="<?= htmlspecialchars($appBaseUrl . '/' . ltrim($href, '/'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link active">
                      <i class="nav-icon bi bi-circle"></i>
                      <p><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></p>
                    </a>
                  </li>
			        <?php endforeach; ?>

                </ul>
              </li>
              
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->