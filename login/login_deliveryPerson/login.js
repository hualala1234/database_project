const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

// Sign Up 加 class
signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});
  
// Sign In 移除 class
signInButton.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

// 切換角色表單
document.querySelectorAll('.role-btn').forEach(button => {
    button.addEventListener('click', () => {
      const selectedRole = button.getAttribute('data-role');
  
      document.querySelectorAll('.role-form').forEach(form => {
        form.style.display = 'none';
      });
  
      const targetForm = document.querySelector(`.role-form[data-role="${selectedRole}"]`);
      if (targetForm) targetForm.style.display = 'block';
  
      document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
    });
});
  


// 改圖片上傳功能
const imageContainer = document.getElementById('imageContainer');
const imageUpload = document.getElementById('imageUpload');
const previewImage = document.getElementById('previewImage');
const uploadIcon = document.getElementById('uploadIcon');

imageContainer.addEventListener('click', () => {
  imageUpload.click();
});

imageUpload.addEventListener('change', function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      previewImage.src = e.target.result;
      uploadIcon.style.display = 'none'; // ✅ 隱藏 icon
      previewImage.classList.remove('hidden'); // ✅ 修正變數名
    };
    reader.readAsDataURL(file);
  } else {
    previewImage.src = '';
    previewImage.classList.add('hidden');
    uploadIcon.style.display = 'block'; // ✅ 顯示 icon
  }
});


document.querySelectorAll('.role-btn').forEach(button => {
    button.addEventListener('click', () => {
        const selectedRole = button.getAttribute('data-role');

        // 1. 切換顯示的 form
        document.querySelectorAll('.role-form').forEach(form => {
            form.style.display = 'none';
        });

        const targetForm = document.querySelector(`.role-form[data-role="${selectedRole}"]`);
        if (targetForm) {
            targetForm.style.display = 'block';
        }

        // 2. 移除所有按鈕的 active，並加上目前這顆
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        button.classList.add('active');
    });
});

