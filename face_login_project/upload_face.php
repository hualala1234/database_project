<?php
include('config.php');
$target_dir = "faces/";
$cid = $_POST['cid'];
$image_name = "face_" . $cid . ".jpg";
$target_file = $target_dir . $image_name;

if (move_uploaded_file($_FILES["face_image"]["tmp_name"], $target_file)) {
    $sql = "UPDATE customer SET face_image_path='$target_file' WHERE cid=$cid";
    mysqli_query($conn, $sql);
    echo "upload success";
} else {
    echo "upload failed";
}
?>
