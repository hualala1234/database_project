### train_model.py
import cv2
import numpy as np
import os

def train_faces():
    recognizer = cv2.face.LBPHFaceRecognizer_create()
    detector = cv2.CascadeClassifier('haarcascade_frontalface_default.xml')

    faces = []
    labels = []
    label_map = {}
    label_count = 0

    for user_id in os.listdir("faces"):
        folder = f"faces/{user_id}"
        if not os.path.isdir(folder):
            continue
        for img_name in os.listdir(folder):
            img_path = os.path.join(folder, img_name)
            gray = cv2.imread(img_path, cv2.IMREAD_GRAYSCALE)
            faces.append(gray)
            if user_id not in label_map:
                label_map[user_id] = label_count
                label_count += 1
            labels.append(label_map[user_id])

    recognizer.train(faces, np.array(labels))
    recognizer.save('trainer.yml')
    np.save('labels.npy', label_map)

if __name__ == "__main__":
    train_faces()