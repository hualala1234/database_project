<?php
session_start();
require '../dbh.php';

$data = json_decode(file_get_contents('php://input'), true);
$did = $_SESSION['did'] ?? null;

if ($did && isset($data['lat'], $data['lng'])) {
    $sql = "UPDATE deliveryperson SET latitude = ?, longitude = ? WHERE did = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddi", $data['lat'], $data['lng'], $did);
    if (!$stmt->execute()) {
        error_log("SQL Error: " . $stmt->error);
    } else {
        echo "update success";
    }
} else {
    echo "missing did or coordinates";
}
?>
