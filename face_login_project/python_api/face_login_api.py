from flask import Flask, request, jsonify
import face_recognition
import os
from PIL import Image
import numpy as np
import tempfile

app = Flask(__name__)
KNOWN_FACES_DIR = '../faces'

@app.route('/face_login', methods=['POST'])
def face_login():
    try:
        file = request.files['image']

        # 使用 PIL 強制轉 RGB，再存成暫時檔
        pil_image = Image.open(file).convert('RGB')
        with tempfile.NamedTemporaryFile(suffix=".jpg", delete=False) as tmp_file:
            pil_image.save(tmp_file.name)
            tmp_path = tmp_file.name

        # 使用 face_recognition 載入處理後的檔案
        unknown_image = face_recognition.load_image_file(tmp_path)
        encodings = face_recognition.face_encodings(unknown_image)
        if len(encodings) == 0:
            print("❌ No face detected in uploaded image")
            return jsonify({"login": False, "error": "No face detected in image"})

        unknown_encoding = encodings[0]

        # 處理完成後刪除臨時圖檔
        os.remove(tmp_path)

    except Exception as e:
        print("❌ error：", e)
        return jsonify({"login": False, "error": "Failed to process image", "details": str(e)})

    for filename in os.listdir(KNOWN_FACES_DIR):
        if filename.endswith(".jpg"):
            try:
                known_image = face_recognition.load_image_file(os.path.join(KNOWN_FACES_DIR, filename))
                known_encoding = face_recognition.face_encodings(known_image)[0]
                results = face_recognition.compare_faces([known_encoding], unknown_encoding)
                if results[0]:
                    cid = filename.split("_")[1].split(".")[0]
                    return jsonify({"login": True, "cid": cid})
            except Exception as e:
                print("⚠️ ignore error file：", filename, e)
                continue

    return jsonify({"login": False})

if __name__ == "__main__":
    app.run(host="localhost", port=5000)
