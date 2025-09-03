<?php
session_start();
require_once 'database/function.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal = $_POST['postal'];
    $country = $_POST['country'];
    $number = $_POST['number'];
    $gender = $_POST['gender']; // Added gender

    $address = "$street, $city, $state, $postal, $country";

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $existingUser = $db->select('users', '*', ['email' => $email]);
    if (isset($existingUser['data']) && count($existingUser['data']) > 0) {
        echo "<script>alert('Email already exists.'); window.location.href = 'page-register.php';</script>";
    } else {
        $result = $db->insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'type' => 0,
            'number' => $number,
            'address' => $address,
            'gender' => $gender // Added gender
        ]);

        if ($result['status'] === 'success') {
            echo "<script>alert('Registration successful. Please log in.'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Registration failed: " . $result['message'] . "'); window.location.href = 'register.php';</script>";
        }
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
                            <div class="card-header pt-4 pb-4 text-center ">
                                <a href="index.php">
                                    <span><img src="assets/images/logo.png" alt="" height="60"></span>
                                </a>
                            </div>

                            <div class="card-body p-4">
                                
                      

                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST"> 

                                    <div class="mb-3">
                                        <label for="fullname" class="form-label">Full Name</label>
                                        <input class="form-control" type="text" id="fullname" name="name" placeholder="Enter your name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="emailaddress" class="form-label">Email address</label>
                                        <input class="form-control" type="email" id="emailaddress" name="email" required placeholder="Enter your email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="street" class="form-label">Street Address</label>
                                        <input class="form-control" type="text" id="street" name="street" placeholder="e.g. 123 Rizal Ave" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input class="form-control" type="text" id="city" name="city" placeholder="e.g. Pasay City" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="state" class="form-label">State/Province</label>
                                        <input class="form-control" type="text" id="state" name="state" placeholder="e.g. Metro Manila" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="postal" class="form-label">Postal Code</label>
                                        <input class="form-control" type="text" id="postal" name="postal" placeholder="e.g. 1300" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input class="form-control" type="text" id="country" name="country" placeholder="e.g. Philippines" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-control" id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="number" class="form-label">Phone Number</label>
                                        <input class="form-control" required type="tel" value="63" pattern="\639\d{9}"
oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12); if (!this.value.startsWith('63')) this.value = '63' + this.value.slice(2);" id="number" name="number" required placeholder="Enter your phone number">
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="checkbox-signup" required>
                                            <label class="form-check-label" for="checkbox-signup">I accept <a href="#" class="text-muted">Terms and Conditions</a></label>
                                        </div>
                                    </div>

                                    <div class="mb-3 text-center">
                                        <button class="btn btn-primary" type="submit"> Sign Up </button>
                                    </div>

                                </form>
                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->

                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <p class="text-muted">Already have account? <a href="index.php" class="text-muted ms-1"><b>Log In</b></a></p>
                            </div> <!-- end col-->
                        </div>
                        <!-- end row -->

                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->
 

        <!-- bundle -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/js/app.min.js"></script>
        
    </body>
</html>
