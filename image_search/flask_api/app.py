# from flask_cors import CORS
# from flask import Flask, request, jsonify
# import tensorflow as tf
# import numpy as np
# import faiss
# from PIL import Image
# import io
# import os

# app = Flask(__name__)
# CORS(app)  # 開放所有來源，避免 CORS 錯誤

# # 載入模型
# model = tf.keras.applications.MobileNetV2(weights="imagenet", include_top=False, pooling='avg')

# # 載入特徵向量與對應資訊
# image_vectors = np.load("food_vectors.npy")
# image_info = np.load("food_info.npy", allow_pickle=True).item()

# # 建立 Faiss index
# index = faiss.IndexFlatL2(image_vectors.shape[1])
# index.add(image_vectors)

# def preprocess(img):
#     img = img.resize((224, 224))
#     img = np.array(img) / 255.0
#     img = np.expand_dims(img, axis=0)
#     return img

# @app.route('/search', methods=['POST'])
# def search_image():
#     file = request.files['image']
#     img = Image.open(io.BytesIO(file.read())).convert('RGB')
#     processed = preprocess(img)
#     vector = model.predict(processed)[0].reshape(1, -1)

#     D, I = index.search(vector, 3)
#     results = []
#     for i in I[0]:
#         results.append(image_info[i])

#     return jsonify({'results': results})

# if __name__ == '__main__':
#     app.run(debug=True)

from flask import Flask, request, jsonify
from flask_cors import CORS
import tensorflow as tf
import numpy as np
import faiss
from PIL import Image
import io

# 自定義的模型
from tensorflow.keras.models import load_model
import json

app = Flask(__name__)
CORS(app)


# 建立兩個模型：一個特徵向量用、一個分類用
feature_model = tf.keras.applications.MobileNetV2(weights="imagenet", include_top=False, pooling='avg')
# class_model = tf.keras.applications.MobileNetV2(weights="imagenet")  # 含分類層

# 圖片資料與向量
image_vectors = np.load("food_vectors.npy")
image_info = np.load("food_info.npy", allow_pickle=True).item()

# 建 Faiss index
index = faiss.IndexFlatL2(image_vectors.shape[1])
index.add(image_vectors)

# 預處理輸入圖片
def preprocess(img):
    img = img.resize((224, 224))
    arr = tf.keras.applications.mobilenet_v2.preprocess_input(np.array(img))
    return np.expand_dims(arr, axis=0)

# 預測圖片分類（返回標籤）
# def predict_label(img):
#     processed = preprocess(img)
#     preds = class_model.predict(processed)
#     decoded = tf.keras.applications.mobilenet_v2.decode_predictions(preds, top=1)[0]
#     return decoded[0][1]  # 回傳分類名稱（英文）

# 載入自訂模型與標籤
class_model = load_model("food_class_model.h5")
with open("label_map.json", "r", encoding="utf-8") as f:
    label_map = json.load(f)
index_to_label = {v: k for k, v in label_map.items()}

def predict_label(img):
    img = img.resize((224, 224)).convert("RGB")
    arr = np.array(img) / 255.0
    arr = np.expand_dims(arr, axis=0)
    preds = class_model.predict(arr)[0]
    class_index = np.argmax(preds)
    label = index_to_label[class_index]
    return label

@app.route('/search', methods=['POST'])
def search_image():
    file = request.files['image']
    img = Image.open(io.BytesIO(file.read())).convert('RGB')

    # 特徵提取
    feat_input = img.resize((224, 224))
    feat_array = np.array(feat_input) / 255.0
    feat_array = np.expand_dims(feat_array, axis=0)
    vector = feature_model.predict(feat_array)[0].reshape(1, -1)

    # 相似搜尋
    D, I = index.search(vector, 8)
    results = []
    for i in I[0]:
        item = image_info[i].copy()
        filename = item['image_url'].split('/')[-1]
        mid_part = filename.split('-')[0]
        mid = mid_part[1:]
        item['merchant_url'] = f"http://localhost/database_project/customer/merchant.php?mid={mid}"
        results.append(item)

    # 食物分類
    predicted_class = predict_label(img)

    return jsonify({
        'predicted_class': predicted_class,
        'results': results
    })


# if __name__ == '__main__':
#     print("✅ 正在執行 JungleBite Flask API")
#     app.run(debug=True)
if __name__ == '__main__':
    print("✅ 正在執行 JungleBite Flask API")
    app.run(debug=True, host="0.0.0.0", port=5000)
