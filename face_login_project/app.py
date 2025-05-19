# from flask import Flask, request, jsonify
# from flask_cors import CORS
# from capture_faces import capture_face
# from face_recognition_login import recognize_face
# import base64
# import numpy as np
# import cv2
# import os
# from deepface import DeepFace

# app = Flask(__name__)
# CORS(app)

# @app.route('/face-login', methods=['GET'])
# def face_login():
#     expected_cid = request.args.get("cid")
#     user_id = recognize_face(expected_cid)
#     if user_id:
#         return jsonify({"status": "success", "user_id": user_id})
#     else:
#         return jsonify({"status": "fail", "message": "辨識失敗或與 cid 不符"})

# @app.route('/capture-face', methods=['GET'])
# def capture_face_api():
#     cid = request.args.get("cid")
#     if not cid:
#         return jsonify({"status": "fail", "message": "缺少 cid 參數"})
#     capture_face(cid)
#     return jsonify({"status": "success", "message": f"已擷取 {cid} 的人臉圖像"})

# if __name__ == '__main__':
#     app.run(debug=True)


# # ✅ 二、呼叫方式：
# # ✅ 從瀏覽器或 PHP 發 GET 請求：
# # bash
# # 複製程式碼
# # http://localhost:5000/capture-face?cid=123
# # 這會擷取人臉，並儲存在 faces/123/ 資料夾中。

# @app.route('/face-login-image', methods=['POST'])
# def face_login_image():
#     data = request.get_json()
#     image_data = data.get('image')

#     if not image_data:
#         return {'status': 'fail', 'message': '缺少圖像資料'}

#     try:
#         image_data = image_data.split(',')[1]
#         img_bytes = base64.b64decode(image_data)
#         img_array = np.frombuffer(img_bytes, np.uint8)
#         img = cv2.imdecode(img_array, cv2.IMREAD_GRAYSCALE)

#         recognizer = cv2.face.LBPHFaceRecognizer_create()
#         recognizer.read('trainer.yml')
#         label_map = np.load('labels.npy', allow_pickle=True).item()
#         reverse_map = {v: k for k, v in label_map.items()}

#         detector = cv2.CascadeClassifier('haarcascade_frontalface_default.xml')
#         faces = detector.detectMultiScale(img, 1.3, 5)

#         for (x, y, w, h) in faces:
#             face = img[y:y+h, x:x+w]
#             id_, conf = recognizer.predict(face)
#             if conf < 50:
#                 user_id = reverse_map[id_]
#                 return {'status': 'success', 'user_id': user_id}

#         return {'status': 'fail', 'message': '無法辨識人臉'}
#     except Exception as e:
#         return {'status': 'fail', 'message': f'處理錯誤：{e}'}
from flask import Flask, request, jsonify
from flask_cors import CORS
from capture_faces import capture_face
import base64
import numpy as np
import cv2
import os
from deepface import DeepFace
import pymysql

app = Flask(__name__)
CORS(app, supports_credentials=True)

# ✅ 預先載入模型（效能提升）
deepface_model = DeepFace.build_model("SFace")

# ✅ 資料庫連線封裝
def get_db_connection():
    return pymysql.connect(
        host='localhost',
        user='root',
        password='',  # ← 若有密碼請填上
        database='junglebite',
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )

# ✅ 拍照註冊（前端呼叫）
@app.route('/capture-face', methods=['GET'])
def capture_face_api():
    cid = request.args.get("cid")
    if not cid or not cid.isdigit():
        return jsonify({"status": "fail", "message": "缺少或錯誤的 cid 參數"})
    capture_face(cid)
    return jsonify({"status": "success", "message": f"已擷取 {cid} 的人臉圖像"})

# ✅ 人臉辨識登入
@app.route('/face-login-image', methods=['POST', 'OPTIONS'])
def face_login_image():
    if request.method == 'OPTIONS':
        return '', 200

    data = request.get_json()
    image_data = data.get('image')
    email = data.get('email')

    if not image_data or not email:
        return jsonify({'status': 'fail', 'message': '缺少圖像或 Email'})

    try:
        # 儲存上傳圖片（input image）
        image_data = image_data.split(',')[1]
        img_bytes = base64.b64decode(image_data)
        img_array = np.frombuffer(img_bytes, np.uint8)
        img = cv2.imdecode(img_array, cv2.IMREAD_COLOR)

        temp_dir = 'temp'
        os.makedirs(temp_dir, exist_ok=True)
        input_path = os.path.join(temp_dir, 'input.jpg')
        cv2.imwrite(input_path, img)

        # 查找使用者 email → cid
        conn = get_db_connection()
        with conn.cursor() as cursor:
            cursor.execute("SELECT cid FROM customer WHERE email = %s", (email,))
            row = cursor.fetchone()
        conn.close()

        if not row:
            return jsonify({'status': 'fail', 'message': '查無此 email'})

        cid = str(row['cid'])  # 轉為字串方便組路徑

        # ✅ 比對該使用者的臉部圖像：faces/{cid}/1.jpg
        base_dir = os.path.dirname(os.path.abspath(__file__))
        image_path = os.path.join(base_dir, 'faces', cid, '1.jpg')

        if not os.path.exists(image_path):
            return jsonify({'status': 'fail', 'message': f'使用者尚未註冊人臉，路徑：{image_path}'})

        result = DeepFace.verify(
            img1_path=input_path,
            img2_path=image_path,
            model_name="SFace",
            enforce_detection=False
        )

        if result['verified']:
            return jsonify({'status': 'success', 'user_id': cid})
        else:
            return jsonify({'status': 'fail', 'message': '人臉不符合'})

    except Exception as e:
        return jsonify({'status': 'fail', 'message': f'錯誤：{str(e)}'})

if __name__ == '__main__':
    app.run(debug=True)



# python -m venv .venv
# .\.venv\Scripts\Activate.ps1
# pip install deepface
# pip install pymysql
#
