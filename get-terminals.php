<?php
require_once 'includes/config.php'; // PDO connection file

header('Content-Type: application/json');

try {
    $terminals = $db->select('terminals', '*');

    echo json_encode([
        "status" => "success",
        "data" => $terminals
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
