### README.md
# Face Login Project

這是一個使用 OpenCV + Flask 的人臉辨識登入系統，專案結構如下：

## 📁 專案結構
```
face_login_project/
├── app.py                 # Flask 主程式 (提供登入 API)
├── capture_faces.py       # 擷取人臉照片並儲存
├── train_model.py         # 訓練人臉辨識模型
├── face_recognition_login.py   # 登入時執行辨識
├── haarcascade_frontalface_default.xml   # OpenCV 人臉分類器
├── trainer.yml         ← 執行 train_model 後自動生成
├── labels.npy          ← 執行 train_model 後自動生成
└── faces/              # 儲存人臉圖片的資料夾
    ├── user001/
    │   ├── 1.jpg
    │   ├── ...
    └── user002/
        ├── 1.jpg
        ├── ...            ← 蒐集到的使用者人臉圖片
```

## 📸 建立人臉資料集
```bash
python capture_faces.py <user_id>
# 例如：python capture_faces.py user001
```

## 🧠 訓練模型
```bash
python train_model.py
```

## 👤 測試辨識登入（單機）
```bash
python face_recognition_login.py
```

## 🌐 啟動 API（Flask）
```bash
python app.py
```
前往 `http://localhost:5050/face-login` 即可開始辨識。

## 📦 其他說明
- `haarcascade_frontalface_default.xml` 為 OpenCV 提供的人臉偵測模型，需放在同一目錄。
- `faces/` 資料夾會依 user_id 自動分類圖片。

---

如果你要把辨識結果整合回 PHP 系統，可以讓 PHP 呼叫 `http://localhost:5000/face-login` 並根據回傳的 user_id 決定是否登入成功。
