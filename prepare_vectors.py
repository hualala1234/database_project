import os
from PIL import Image
import numpy as np
import tensorflow as tf

# 載入 CNN 模型（用來萃取特徵向量）
model = tf.keras.applications.MobileNetV2(weights="imagenet", include_top=False, pooling='avg')

# 圖片資料夾（來源圖）
image_folder = "food_images/"
vectors = []
info = []

# 只處理圖檔副檔名
valid_extensions = ('.jpg', '.jpeg', '.png')

for idx, fname in enumerate(os.listdir(image_folder)):
    if not fname.lower().endswith(valid_extensions):
        continue  # 跳過非圖檔
    path = os.path.join(image_folder, fname)
    img = Image.open(path).resize((224, 224)).convert("RGB")
    arr = np.array(img) / 255.0
    vec = model.predict(np.expand_dims(arr, 0))[0]
    vectors.append(vec)

    # 圖片網址改為 Flask 伺服器提供靜態資源的位置
    image_url = f"http://localhost:5000/static/{fname}"
    info.append({'name': fname.split('.')[0], 'image_url': image_url})

# 儲存 numpy 檔案
np.save("food_vectors.npy", np.array(vectors))
np.save("food_info.npy", dict(enumerate(info)))

print("✔ 向量與圖片資訊儲存完畢，共處理圖片數量：", len(vectors))
