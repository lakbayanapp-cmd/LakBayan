<?php require_once 'includes/config.php'; ?>  
      
                                                
<!DOCTYPE html>
    <html lang="en">

        <?php require_once 'includes/head.php'; ?>  
    
    <body class="loading" data-layout-color="light" data-leftbar-theme="light" data-layout-mode="fluid" data-rightbar-onstart="true">
        <!-- Begin page -->
        <div class="wrapper">
            <?php require_once 'includes/sidebar.php'; ?>  
            <?php require_once 'includes/topbar.php'; ?>  

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
               
                    
                    <!-- Start Content-->
                    <div class="container-fluid">
 <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update session user data (for UI feedback)
    $_SESSION['user']['name'] = $_POST['name'] ?? $_SESSION['user']['name'];
    $_SESSION['user']['birthdate'] = $_POST['birthdate'] ?? $_SESSION['user']['birthdate'];
    $_SESSION['user']['address'] = $_POST['address'] ?? $_SESSION['user']['address'];
    $_SESSION['user']['email'] = $_POST['email'] ?? $_SESSION['user']['email'];
    $_SESSION['user']['number'] = $_POST['number'] ?? $_SESSION['user']['number'];

    // Handle profile image upload (save to assets/images/users/)
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/assets/images/users/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmp = $_FILES['profile']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['profile']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            $_SESSION['user']['profile'] = $fileName; // save filename in session
        }
    }

    // Update in database
    if (isset($_SESSION['user']['id'])) {
        $userId = $_SESSION['user']['id'];
        $updateData = [
            'name' => $_SESSION['user']['name'],
            'birthdate' => $_SESSION['user']['birthdate'],
            'address' => $_SESSION['user']['address'],
            'email' => $_SESSION['user']['email'],
            'number' => $_SESSION['user']['number'],
        ];

        // If a new profile image was uploaded
        if (!empty($_SESSION['user']['profile'])) {
            $updateData['profile'] = $_SESSION['user']['profile'];
        }

        // Remove empty values
        foreach ($updateData as $k => $v) {
            if (is_string($v) && trim($v) === '') unset($updateData[$k]);
        }

        $result = $db->update('users', $updateData, ['id' => $userId]);
        if ($result['status'] === 'success') {
            echo '<div class="alert alert-success mt-3">Profile updated!</div>';
        } else {
            echo '<div class="alert alert-danger mt-3">Update failed: ' . htmlspecialchars($result['message']) . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger mt-3">User not found in session.</div>';
    }
}
?>

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <h4 class="page-title">Profile</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title --> 

                        <div class="row">
                            <div class="col-xl-4 col-lg-5">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <?php
                                            $profileImg = 'assets/images/user.png';
                                            if (!empty($_SESSION['user']['profile'])) {
                                                                                               $profileImg = 'assets/images/users/' . htmlspecialchars($_SESSION['user']['profile']);          }

                                        ?>
                                        <img src="<?php echo $profileImg; ?>" class="rounded-circle avatar-lg img-thumbnail"
                                        alt="profile-image">

                                   
                                <h4 class="mb-0 mt-2">
                                    <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Dominic Keller'); ?>
                                </h4>
                                <?php
                                if (isset($_SESSION['user']['type']) && $_SESSION['user']['type'] == 0) {
                                    echo '<p class="text-muted font-14">Member</p>';
                                } else {
                                    echo '<p class="text-muted font-14">' . htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') . '</p>';
                                }
                                ?>


                                        <?php
$exclude = [
    'id', 'password', 'created_at', 'updated_at', 'profile', 'type',
    'allergies', 'dietary_restrictions', 'goals', 'status'
];

                                        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
                                            echo '<div class="text-start mt-3">';
                                            foreach ($_SESSION['user'] as $key => $value) {
                                                if (in_array($key, $exclude)) continue;
                                                $label = ucwords(str_replace('_', ' ', $key));
                                                $display = htmlspecialchars($value ?? 'N/A');
                                                echo '<p class="text-muted mb-2 font-13"><strong>' . $label . ' :</strong> <span class="ms-2">' . ($display !== '' ? $display : 'N/A') . '</span></p>';
                                            }
                                            echo '</div>';
                                        }
                                        ?>
 
                                    </div> <!-- end card-body -->
                                </div> <!-- end card -->
 

                            </div> <!-- end col-->

                            <div class="col-xl-8 col-lg-7">
                                <div class="card">
                                    <div class="card-body">
                                        <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                            <li class="nav-item" hidden>
                                                <a href="#aboutme" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0">
                                                    About
                                                </a>
                                            </li>
                                            <li class="nav-item" hidden>
                                                <a href="#timeline" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0 active">
                                                    Timeline
                                                </a>
                                            </li>
                                            <li class="nav-item" hidden>
                                                <a href="#settings" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0 active">
                                                    Profile Edit
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                 
                                            <div class="tab-pane show active" id="settings">
                                                <?php
                                                    // Prepare user data
                                                    $user = $_SESSION['user'] ?? [];
                                                    $firstName = htmlspecialchars($user['name'] ?? '');
                                                    $email = htmlspecialchars($user['email'] ?? '');
                                                    $number = htmlspecialchars($user['number'] ?? '');
                                                    $address = htmlspecialchars($user['address'] ?? '');
                                                    $birthdate = htmlspecialchars($user['birthdate'] ?? '');
                                                ?>
                                   <form method="post" action="" enctype="multipart/form-data">
    <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i> Personal Info</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="firstname" class="form-label">Name</label>
                <input type="text" class="form-control" id="firstname" name="name" value="<?php echo $firstName; ?>" placeholder="Enter name">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="birthdate" class="form-label">Birthdate</label>
                <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $birthdate; ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <label for="userbio" class="form-label">Address</label>
                <input type="text" class="form-control" id="userbio" name="address" value="<?php echo $address; ?>" placeholder="address">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="useremail" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="useremail" name="email" value="<?php echo $email; ?>" placeholder="Enter email">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="usernumber" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="usernumber" name="number" value="<?php echo $number; ?>" placeholder="Enter phone number">
            </div>
        </div>
    </div>

    <!-- ✅ New field for profile image -->
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="profile" class="form-label">Profile Image</label>
                <input type="file" class="form-control" id="profile" name="profile">
            </div>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-success mt-2"><i class="mdi mdi-content-save"></i> Save</button>
    </div>
</form>

                                        
                                            </div>
                                            <!-- end settings content-->
    
                                        </div> <!-- end tab-content -->
                                    </div> <!-- end card body -->
                                </div> <!-- end card -->
                            </div> <!-- end col -->
                        </div>
                        <!-- end row-->

                    </div>
                    <!-- container -->

                </div>
                <!-- content -->


           <?php require_once 'includes/footer.php'; ?>

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->

           <?php require_once 'includes/right-sidebar.php'; ?>

        <!-- bundle -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/js/app.min.js"></script>

    </body>

</html>