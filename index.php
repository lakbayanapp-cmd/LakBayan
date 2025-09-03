<?php
session_start();
require_once 'database/function.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $db->select('users', '*', ['email' => $email]);
    if (isset($user['data']) && count($user['data']) > 0) {
        $userdata = $user['data'][0];
        if (password_verify($password, $userdata['password'])) {
            $_SESSION['user'] = $userdata; // Store all user data in session

            if ($userdata['type'] == 1) {
            header('Location: dashboard.php');
            } else {
            header('Location: dashboard-user.php');
            }
            exit();
        } else {
            echo "<script>alert('Wrong password.'); window.location.href = 'index.php';</script>";
        }
        } else {
        echo "<script>alert('Account doesn\\'t exist.'); window.location.href = 'index.php';</script>";
        }
    }
    ?>

<!DOCTYPE html>
<html lang="en">
    

<?php require_once 'includes/head.php'; ?>
    
    <body class="loading authentication-bg" data-layout-config='{"darkMode":false}'>
        <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-4 col-lg-5">
                        <div class="card">

                            <!-- Logo -->
                            <div class="card-header pt-4 pb-4 text-center ">
                                <a href="index.php">
                                    <span><img src="assets/images/logo.png" alt="" height="60"></span>
                                </a>
                            </div>

                            <div class="card-body p-4">
                                
                                <div class="text-center w-75 m-auto">
                                    <h4 class="text-dark-50 text-center pb-0 fw-bold">Sign In</h4>
                                    <p class="text-muted mb-4">Enter your email address and password to access admin panel.</p>
                                </div>
                                                          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST"> 


                                    <div class="mb-3">
                                        <label for="emailaddress" class="form-label">Email address</label>
                                        <input class="form-control" type="email" id="emailaddress" name="email" required="" placeholder="Enter your email">
                                    </div>

                                    <div class="mb-3">
                                        <a hidden href="pages-recoverpw.php" class="text-muted float-end"><small>Forgot your password?</small></a>
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" name="password" class="form-control" required="" placeholder="Enter your password">
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="checkbox-signin" checked>
                                            <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                        </div>
                                    </div>

                                    <div class="mb-3 mb-0 text-center">
                                        <button class="btn btn-primary" type="submit"> Log In </button>
                                    </div>

                                </form>
                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->

                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <p class="text-muted">Don't have an account? <a href="register.php" class="text-muted ms-1"><b>Sign Up</b></a></p>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->

                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->

        <!-- <footer class="footer footer-alt">
            2018 - <script>document.write(new Date().getFullYear())</script> © WondaGo
        </footer> -->

        <!-- bundle -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/js/app.min.js"></script>
        
    </body>


</html>
