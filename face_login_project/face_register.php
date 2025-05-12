<form action="upload_face.php" method="POST" enctype="multipart/form-data">
  <input type="file" name="face_image" accept="image/*" required>
  <input type="hidden" name="cid" value="1"> <!-- 從 session 拿 -->
  <button type="submit">上傳臉部照片</button>
</form>
