<?php
include("connect.php");

$tranId = $_POST['tranId'] ?? null;
$type = $_POST['type'] ?? null;
$comment = $_POST['comment'] ?? '';

if (!$tranId || !$type || !in_array($type, ['mComment', 'dComment'])) {
    die("Invalid input.");
}

$comment = $conn->real_escape_string($comment);
$sql = "UPDATE transaction SET $type = '$comment' WHERE tranId = $tranId";

if ($conn->query($sql)) {
    echo "Success";
    echo '<meta http-equiv="refresh" content="0">';
} else {
    echo "Failed: " . $conn->error;
}
?>
