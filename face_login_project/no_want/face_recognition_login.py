# 以 OpenCV 拍攝人臉並儲存
### face_recognition_login.py
import cv2
import numpy as np

# def recognize_face():
#     recognizer = cv2.face.LBPHFaceRecognizer_create()
#     recognizer.read('trainer.yml')
#     label_map = np.load('labels.npy', allow_pickle=True).item()
#     reverse_map = {v: k for k, v in label_map.items()}

#     detector = cv2.CascadeClassifier('haarcascade_frontalface_default.xml')
#     cap = cv2.VideoCapture(0)

#     while True:
#         ret, frame = cap.read()
#         gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
#         faces = detector.detectMultiScale(gray, 1.3, 5)

#         for (x, y, w, h) in faces:
#             face = gray[y:y+h, x:x+w]
#             id_, conf = recognizer.predict(face)
#             if conf < 50:
#                 user_id = reverse_map[id_]
#                 print(f"辨識成功：{user_id}")
#                 cap.release()
#                 cv2.destroyAllWindows()
#                 return user_id

#         cv2.imshow("Recognizing", frame)
#         if cv2.waitKey(1) == 27:
#             break

#     cap.release()
#     cv2.destroyAllWindows()
#     return None

def recognize_face(expected_cid=None):
    recognizer = cv2.face.LBPHFaceRecognizer_create()
    recognizer.read('trainer.yml')
    label_map = np.load('labels.npy', allow_pickle=True).item()
    reverse_map = {v: k for k, v in label_map.items()}

    detector = cv2.CascadeClassifier('haarcascade_frontalface_default.xml')
    cap = cv2.VideoCapture(0)

    while True:
        ret, frame = cap.read()
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = detector.detectMultiScale(gray, 1.3, 5)

        for (x, y, w, h) in faces:
            face = gray[y:y+h, x:x+w]
            id_, conf = recognizer.predict(face)
            if conf < 50:
                user_id = reverse_map[id_]
                if expected_cid is None or user_id == expected_cid:
                    cap.release()
                    cv2.destroyAllWindows()
                    return user_id
                else:
                    print(f"[警告] 臉屬於 {user_id}，非預期 cid：{expected_cid}")

        cv2.imshow("Face Login", frame)
        if cv2.waitKey(1) & 0xFF == 27:
            break

    cap.release()
    cv2.destroyAllWindows()
    return None
