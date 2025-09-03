<?php
// Import Font Awesome CSS for icons
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}
?>
          <!-- ========== Left Sidebar Start ========== -->
            <?php
            $userType = isset($_SESSION['user']['type']) ? $_SESSION['user']['type'] : null;
            ?>

            <div class="leftside-menu">

                <!-- LOGO -->
                <a href="index.php" class="logo text-center logo-light">
                    <span class="logo-lg">
                        <img src="assets/images/logo.png" alt="" height="50">
                    </span>
                    <span class="logo-sm">
                        <img src="assets/images/logo_sm.png" alt="" height="50">
                    </span>
                </a>

                <!-- LOGO -->
                <a href="index.php" class="logo text-center logo-dark">
                    <span class="logo-lg">
                        <img src="assets/images/logo-dark.png" alt="" height="50">
                    </span>
                    <span class="logo-sm">
                        <img src="assets/images/logo_sm_dark.png" alt="" height="50">
                    </span>
                </a>

        <div class="h-100" id="leftside-menu-container" data-simplebar>
    <ul class="side-nav">
        <?php if ($userType === 0): ?> <!-- USER -->
            <li class="side-nav-title side-nav-item">Main</li>
            <li class="side-nav-item">
                <a href="dashboard-user.php" class="side-nav-link">
                    <i class="uil-dashboard"></i>
                    <span> Book  </span>
                </a>
            </li>

    
        <?php elseif ($userType === 1): ?> <!-- ADMIN -->
            <li class="side-nav-title side-nav-item">Home</li>
            <li class="side-nav-item">
                <a href="dashboard.php" class="side-nav-link">
                    <i class="uil-dashboard"></i>
                    <span> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-title side-nav-item">User Management</li>
            <li class="side-nav-item">
                <a href="users.php" class="side-nav-link">
                    <i class="uil-users-alt"></i>
                    <span> System Users </span>
                </a>
            </li>

            <li class="side-nav-title side-nav-item">Nutrition Management</li>
            <li class="side-nav-item">
                <a href="mealplan.php" class="side-nav-link">
                    <i class="uil-utensils-alt"></i>
                    <span> Meal Plan Templates </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="foodrecipes.php" class="side-nav-link">
                    <i class="uil-book-open"></i>
                    <span> Food Recipes </span>
                </a>
            </li> 

            <li class="side-nav-title side-nav-item">Reports & Analytics</li>
            <li class="side-nav-item">
                <a href="reports.php" class="side-nav-link">
                    <i class="uil-chart"></i>
                    <span> Reports </span>
                </a>
            </li>

       
        <?php endif; ?>
    </ul>
    <div class="clearfix"></div>
</div>

                <!-- Sidebar -left -->
            </div>
            <!-- Left Sidebar End -->
             
                  <style>
            .topalert {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 350px;
                min-width: 250px;
                width: auto;
                float: right;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                pointer-events: auto;
            }
</style>

<?php require_once  'right-sidebar.php' ?>