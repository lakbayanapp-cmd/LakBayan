<?php require_once 'includes/config.php'; ?>  

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $type = $_POST['type'];
        $number = $_POST['number'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $address = $_POST['address'] ?? null;
        $birthdate = $_POST['birthdate'] ?? null;
        $status = $_POST['status'];

        // Handle file upload
        $profile = null;
        if (!empty($_FILES['profile']['name'])) {
            $uploadDir = __DIR__ . "/assets/images/profiles/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $filename = time() . "_" . basename($_FILES['profile']['name']);
            move_uploaded_file($_FILES['profile']['tmp_name'], $uploadDir . $filename);
            $profile = $filename;
        }

        $db->insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'type' => $type,
            'number' => $number,
            'gender' => $gender,
            'profile' => $profile,
            'address' => $address,
            'birthdate' => $birthdate,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        header("Location: users.php");
        exit;
    }

    elseif ($action === 'edit') {
        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'type' => $_POST['type'],
            'number' => $_POST['number'] ?? null,
            'gender' => $_POST['gender'] ?? null,
            'address' => $_POST['address'] ?? null,
            'birthdate' => $_POST['birthdate'] ?? null,
            'status' => $_POST['status'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // File upload (replace only if new uploaded)
        if (!empty($_FILES['profile']['name'])) {
            $uploadDir = __DIR__ . "/assets/images/profiles/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $filename = time() . "_" . basename($_FILES['profile']['name']);
            move_uploaded_file($_FILES['profile']['tmp_name'], $uploadDir . $filename);
            $data['profile'] = $filename;
        }

        $db->update('users', $data, ['id' => $id]);

        header("Location: users.php");
        exit;
    }

    elseif  ($action === 'delete') {
        $id = $_POST['id'];
        $db->delete('users', ['id' => $id]);
        header("Location: users.php");
        exit;
    }
}


$result = $db->select('users', '*');
?>

<!DOCTYPE html>
<html lang="en">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php require_once 'includes/head.php'; ?>  
<body class="loading" data-layout-color="light" data-leftbar-theme="light" data-layout-mode="fluid" data-rightbar-onstart="true">
    <div class="wrapper">
        <?php require_once 'includes/sidebar.php'; ?>  
        <?php require_once 'includes/topbar.php'; ?>  
        <div class="content-page">
            <div class="content">
                <div class="container-fluid"> 
                    <!-- Page Title -->
             <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body"> 
                                        <div class="d-flex justify-content-between align-items-center mb-3"> 
                                            <h4 class="card-title mb-0">Users</h4>
                                        </div>
                                              <div class="table-responsive">
                                        <table id="users-table" class="table table-striped dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Type</th>
                                                    <th>Email</th>
                                                    <th>Number</th>
                                                    <th>Gender</th>
                                                    <th>Profile</th>
                                                    <th>Address</th>
                                                    <th>Birthdate</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                              foreach ($result['data'] as $user) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($user['name']) . "</td>";
    echo "<td>" . ($user['type'] == 1 ? "Admin" : "User") . "</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "<td>" . ($user['number'] ? htmlspecialchars($user['number']) : "—") . "</td>";
    echo "<td>" . ($user['gender'] ? htmlspecialchars($user['gender']) : "—") . "</td>";
    if ($user['profile']) {
        echo "<td><img src='assets/images/profiles/{$user['profile']}' width='50' alt='Profile'></td>";
    } else {
        echo "<td>No Image</td>";
    }
    echo "<td>" . ($user['address'] ? htmlspecialchars($user['address']) : "—") . "</td>";
    echo "<td>" . ($user['birthdate'] ? htmlspecialchars($user['birthdate']) : "—") . "</td>";
    echo "<td>" . ($user['status'] == 1 ? "Active" : "Inactive") . "</td>";

    // Action buttons
    echo "<td>
        <button class='btn btn-primary btn-sm editUserBtn'
            data-id='{$user['id']}'
            data-name='" . htmlspecialchars($user['name'], ENT_QUOTES) . "'
            data-type='{$user['type']}'
            data-email='" . htmlspecialchars($user['email'], ENT_QUOTES) . "'
            data-number='" . htmlspecialchars($user['number'], ENT_QUOTES) . "'
            data-gender='" . htmlspecialchars($user['gender'], ENT_QUOTES) . "'
            data-profile='{$user['profile']}'
            data-address='" . htmlspecialchars($user['address'], ENT_QUOTES) . "'
            data-birthdate='" . htmlspecialchars($user['birthdate'], ENT_QUOTES) . "'
            data-status='{$user['status']}'
            data-bs-toggle='modal'
            data-bs-target='#editUserModal'>
            <i class='fa fa-edit'></i>
        </button>
        <button class='btn btn-danger btn-sm deleteUserBtn'
            data-id='{$user['id']}'
            data-bs-toggle='modal'
            data-bs-target='#deleteUserModal'>
            <i class='fa fa-trash'></i>
        </button>
    </td>";
    echo "</tr>";
}

                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addUserForm" method="POST" action="users.php" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>User Type</label>
                        <select name="type" class="form-control">
                            <option value="0">User</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Contact Number</label>
                        <input type="text" name="number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">—</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Profile Image</label>
                        <input type="file" name="profile" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Birthdate</label>
                        <input type="date" name="birthdate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editUserForm" method="POST" action="users.php" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editUserId">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="editUserName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="editUserEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>User Type</label>
                        <select name="type" id="editUserType" class="form-control">
                            <option value="0">User</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Contact Number</label>
                        <input type="text" name="number" id="editUserNumber" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Gender</label>
                        <select name="gender" id="editUserGender" class="form-control">
                            <option value="">—</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Profile Image</label>
                        <input type="file" name="profile" class="form-control" accept="image/*">
                        <small class="text-muted">Leave blank to keep current image</small>
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" id="editUserAddress" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Birthdate</label>
                        <input type="date" name="birthdate" id="editUserBirthdate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="editUserStatus" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="deleteUserForm" method="POST" action="users.php">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteUserId">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
$(document).on("click", ".editUserBtn", function () {
    $("#editUserId").val($(this).data("id"));
    $("#editUserName").val($(this).data("name"));
    $("#editUserEmail").val($(this).data("email"));
    $("#editUserType").val($(this).data("type"));
    $("#editUserNumber").val($(this).data("number"));
    $("#editUserGender").val($(this).data("gender"));
    $("#editUserAddress").val($(this).data("address"));
    $("#editUserBirthdate").val($(this).data("birthdate"));
    $("#editUserStatus").val($(this).data("status"));


});

$(document).on("click", ".deleteUserBtn", function () {
    $("#deleteUserId").val($(this).data("id"));
});
</script>

                                </div>
                            </div>
                        </div>
                    </div>
      
                </div>
            </div>
           <?php require_once 'includes/footer.php'; ?>
        </div>
    </div>
           <?php require_once 'includes/right-sidebar.php'; ?>
           <?php require_once 'includes/scripts.php'; ?>

</body>
</html>
