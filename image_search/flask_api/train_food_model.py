from tensorflow.keras.applications import MobileNetV2
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.layers import Dense, GlobalAveragePooling2D
from tensorflow.keras.models import Model
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.callbacks import EarlyStopping
import os

# 設定參數
dataset_path = "food_dataset"  # 👉 改成你資料夾的路徑
img_size = (224, 224)
batch_size = 32
epochs = 10

# 資料前處理（含驗證分割）
datagen = ImageDataGenerator(
    rescale=1./255,
    validation_split=0.2
)

train_gen = datagen.flow_from_directory(
    dataset_path,
    target_size=img_size,
    batch_size=batch_size,
    class_mode='categorical',
    subset='training'
)

val_gen = datagen.flow_from_directory(
    dataset_path,
    target_size=img_size,
    batch_size=batch_size,
    class_mode='categorical',
    subset='validation'
)

num_classes = train_gen.num_classes
print("分類數量：", num_classes)
print("分類名稱：", train_gen.class_indices)

# 建立模型
base_model = MobileNetV2(weights='imagenet', include_top=False, input_shape=(224, 224, 3))
x = base_model.output
x = GlobalAveragePooling2D()(x)
x = Dense(128, activation='relu')(x)
predictions = Dense(num_classes, activation='softmax')(x)
model = Model(inputs=base_model.input, outputs=predictions)

for layer in base_model.layers:
    layer.trainable = False  # 冻結預訓練層

model.compile(optimizer=Adam(), loss='categorical_crossentropy', metrics=['accuracy'])

# 訓練
model.fit(
    train_gen,
    validation_data=val_gen,
    epochs=epochs,
    callbacks=[EarlyStopping(patience=2)]
)

# 儲存模型
model.save("food_class_model.h5")

# 儲存分類標籤對應表
import json
with open("label_map.json", "w", encoding="utf-8") as f:
    json.dump(train_gen.class_indices, f, ensure_ascii=False, indent=2)

print("✅ 模型與分類標籤已儲存完成")
