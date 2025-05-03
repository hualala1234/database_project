<?php
// 獲取錯誤訊息
$wrong_password = $_GET['wrongpw'] ?? 'Enter your password';
$error_email = $_GET['erroremail'] ?? 'Enter your email';
$create_email = $_GET['create'] ?? 'Log in to stay connected with us';

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width", initial-scale="1.0">
        <link rel="stylesheet" href="login.css">
        <script src="login.js" defer></script>
    </head>
    <body>
        
        <div class="container"id="container">
            <div class="form-container sign-up-container">
                <form action="register.php" method="POST" enctype="multipart/form-data">

                    <div class="form-header" style="
                        display: flex;
                        justify-content: space-around;
                        align-items: center;
                        margin-bottom: 20px;
                        width: 100%;">
                        <!-- 左上角 Create Account -->
                        <div class="create_account" style="display: flex; flex-direction: column;">
                            <h1 style="margin: 0;">Create</h1>
                            <h1 style="margin: 0;">Account</h1>
                        </div>
                                
                        <!-- 右上角頭像 -->
                        <div id="imageContainer">

                            <!-- 圓圈背景 -->
                            <div class="circle-layer" id="circleBg"></div>

                            <!-- 預覽圖片（中間層） -->
                            <img id="previewImage" src="" alt="">

                            <!-- 圖片 icon（最上層） -->
                            <img id="uploadIcon" src="gallery.png" alt="上傳圖示">
                        </div>

                        <input type="file" name="image" id="imageUpload" accept="image/*" style="display: none" class="hidden" >
                    </div>

                    <span>only for delivery person</span>
                
                    <!-- 雙欄排列 -->
                    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 10px;">
                        <!-- 左欄 -->
                        <div style="flex: 1; min-width: 200px;">
                            
                            <input type="text"
                                name="fullname"
                                placeholder="Name"
                                style="width: 100%; margin-bottom: 10px;"
                                required
                                value="<?php echo htmlspecialchars($_GET['fullname'] ?? ''); ?>" />
                            <input type="email"
                                name="email"
                                placeholder="Email"
                                style="width: 100%; margin-bottom: 10px;"
                                required
                                value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" />
                            <input type="password"
                                name="password"
                                placeholder="Password"
                                style="width: 100%; margin-bottom: 10px;"
                                required />
                        </div>
                        <!-- 右欄 -->
                        <div style="flex: 1; min-width: 200px;">
                            <input type="text"
                                name="address"
                                placeholder="Address"
                                style="width: 100%; margin-bottom: 10px;"
                                value="<?php echo htmlspecialchars($_GET['address'] ?? ''); ?>" />
                            <input type="text"
                                name="introducer"
                                placeholder="Introducer"
                                style="width: 100%; margin-bottom: 10px;"
                                value="<?php echo htmlspecialchars($_GET['introducer'] ?? ''); ?>" />
                        </div>
                    </div>

                    <button type="submit">Register</button>
                </form>
            </div>

        
            <div class="form-container sign-in-container">
                <form action="login.php" method="POST">
                    <h1>Login</h1>
                    <span>Use Your Account To Sign In</span>
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Enter your name" 
                        
                        value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>"
                    >
                    
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="<?php echo htmlspecialchars($error_email); ?>" 
                        required 
                        class="<?php echo $error_email !== 'Enter your email' ? 'erroremail' : ''; ?>"
                    >
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="<?php echo htmlspecialchars($wrong_password); ?>" 
                        required
                        class="<?php echo $wrong_password !== 'Enter your password' ? 'wrongpw' : ''; ?>"
                    >
                    <button type="submit">Log in</button>
                </form>
            </div>



            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1>Hello, Again</h1>
                        <img src="1.png" style="width:187.5px;margin-bottom:-20px;">
                        <p>Log in to stay connected with us</p>
                        <button class="ghost" id="signIn">Sign In</button>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1>Welcome</h1>
                        <img src="lock.png" style="width:127.5px;margin-top:20px;margin-bottom:10px;">
                        <p><?php echo htmlspecialchars($create_email); ?></p>
                        <button class="ghost" id="signUp">Sign Up</button>
                    </div>
                </div>
                
            </div>
        
        </div>

    </body>
</html>