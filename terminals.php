<?php require_once 'includes/config.php'; ?>  

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = $_POST['name'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $base_rate = $_POST['base_rate'];
        $per_km_rate = $_POST['per_km_rate'];

        // Handle file upload
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . "/assets/images/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $filename = time() . "_" . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
            $image = "assets/images/" . $filename;
        }

        $db->insert('terminals', [
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'image' => $image,
            'base_rate' => $base_rate,
            'per_km_rate' => $per_km_rate,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        header("Location: terminals.php");
        exit;
    }

    elseif ($action === 'edit') {
        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'latitude' => $_POST['latitude'],
            'longitude' => $_POST['longitude'],
            'base_rate' => $_POST['base_rate'],
            'per_km_rate' => $_POST['per_km_rate'],
        ];

        // File upload (replace only if new uploaded)
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . "/assets/images/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $filename = time() . "_" . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
            $data['image'] = "assets/images/" . $filename;
        }

        $db->update('terminals', $data, ['id' => $id]);

        header("Location: terminals.php");
        exit;
    }

    elseif  ($action === 'delete') {
        $id = $_POST['id'];
        $db->delete('terminals', ['id' => $id]);
        header("Location: terminals.php");
        exit;
    }
}

$result = $db->select('terminals', '*');
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
                                        <h4 class="card-title mb-0">Terminals</h4>
                                        <!-- Add Terminal Button -->
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTerminalModal">
                                            <i class="fa fa-plus"></i> Add Terminal
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="terminals-table" class="table table-striped dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Latitude</th>
                                                    <th>Longitude</th>
                                                    <th>Image</th>
                                                    <th>Base Rate</th>
                                                    <th>Per KM Rate</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($result['data'] as $terminal) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($terminal['name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($terminal['latitude']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($terminal['longitude']) . "</td>";
                                                    if ($terminal['image']) {
                                                        echo "<td><img src='{$terminal['image']}' width='50' alt='Image'></td>";
                                                    } else {
                                                        echo "<td>No Image</td>";
                                                    }
                                                    echo "<td>" . htmlspecialchars($terminal['base_rate']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($terminal['per_km_rate']) . "</td>";
                                                    echo "<td>
                                                        <button class='btn btn-primary btn-sm editTerminalBtn'
                                                            data-id='{$terminal['id']}'
                                                            data-name='" . htmlspecialchars($terminal['name'], ENT_QUOTES) . "'
                                                            data-latitude='" . htmlspecialchars($terminal['latitude'], ENT_QUOTES) . "'
                                                            data-longitude='" . htmlspecialchars($terminal['longitude'], ENT_QUOTES) . "'
                                                            data-image='{$terminal['image']}'
                                                            data-base_rate='" . htmlspecialchars($terminal['base_rate'], ENT_QUOTES) . "'
                                                            data-per_km_rate='" . htmlspecialchars($terminal['per_km_rate'], ENT_QUOTES) . "'
                                                            data-created_at='" . htmlspecialchars($terminal['created_at'], ENT_QUOTES) . "'
                                                            data-bs-toggle='modal'
                                                            data-bs-target='#editTerminalModal'>
                                                            <i class='fa fa-edit'></i>
                                                        </button>
                                                        <button class='btn btn-danger btn-sm deleteTerminalBtn'
                                                            data-id='{$terminal['id']}'
                                                            data-bs-toggle='modal'
                                                            data-bs-target='#deleteTerminalModal'>
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

                        <!-- Add Terminal Modal -->
                        <div class="modal fade" id="addTerminalModal" tabindex="-1">
                            <div class="modal-dialog">
                                <form id="addTerminalForm" method="POST" action="terminals.php" enctype="multipart/form-data">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Add Terminal</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="add">
                                            <div class="mb-3">
                                                <label>Name</label>
                                                <input type="text" name="name" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Latitude</label>
                                                <input type="text" name="latitude" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Longitude</label>
                                                <input type="text" name="longitude" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Image</label>
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                            </div>
                                            <div class="mb-3">
                                                <label>Base Rate</label>
                                                <input type="number" step="0.01" name="base_rate" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Per KM Rate</label>
                                                <input type="number" step="0.01" name="per_km_rate" class="form-control" required>
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

                        <!-- Edit Terminal Modal -->
                        <div class="modal fade" id="editTerminalModal" tabindex="-1"> 
                            <div class="modal-dialog">
                                <form id="editTerminalForm" method="POST" action="terminals.php" enctype="multipart/form-data">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Terminal</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" id="editTerminalId">
                                            <div class="mb-3">
                                                <label>Name</label>
                                                <input type="text" name="name" id="editTerminalName" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Latitude</label>
                                                <input type="text" name="latitude" id="editTerminalLatitude" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Longitude</label>
                                                <input type="text" name="longitude" id="editTerminalLongitude" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Image</label>
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                                <small class="text-muted">Leave blank to keep current image</small>
                                            </div>
                                            <div class="mb-3">
                                                <label>Base Rate</label>
                                                <input type="number" step="0.01" name="base_rate" id="editTerminalBaseRate" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Per KM Rate</label>
                                                <input type="number" step="0.01" name="per_km_rate" id="editTerminalPerKmRate" class="form-control" required>
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

                        <!-- Delete Terminal Modal -->
                        <div class="modal fade" id="deleteTerminalModal" tabindex="-1">
                            <div class="modal-dialog">
                                <form id="deleteTerminalForm" method="POST" action="terminals.php">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Delete Terminal</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete this terminal?</p>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" id="deleteTerminalId">
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
                        $(document).on("click", ".editTerminalBtn", function () {
                            $("#editTerminalId").val($(this).data("id"));
                            $("#editTerminalName").val($(this).data("name"));
                            $("#editTerminalLatitude").val($(this).data("latitude"));
                            $("#editTerminalLongitude").val($(this).data("longitude"));
                            $("#editTerminalBaseRate").val($(this).data("base_rate"));
                            $("#editTerminalPerKmRate").val($(this).data("per_km_rate"));
                        });

                        $(document).on("click", ".deleteTerminalBtn", function () {
                            $("#deleteTerminalId").val($(this).data("id"));
                        });
                        </script>

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
