### capture_faces.py
import cv2
import os
import sys

def capture_face(user_id):
    cap = cv2.VideoCapture(0)
    detector = cv2.CascadeClassifier('haarcascade_frontalface_default.xml')
    count = 0
    os.makedirs(f"faces/{user_id}", exist_ok=True)

    while True:
        ret, frame = cap.read()
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = detector.detectMultiScale(gray, 1.3, 5)
        for (x, y, w, h) in faces:
            count += 1
            face = gray[y:y+h, x:x+w]
            cv2.imwrite(f"faces/{user_id}/{count}.jpg", face)
            cv2.rectangle(frame, (x, y), (x+w, y+h), (255, 0, 0), 2)
        cv2.imshow("Capture Faces", frame)
        if cv2.waitKey(1) == 27 or count >= 20:
            break

    cap.release()
    cv2.destroyAllWindows()

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python capture_faces.py <user_id>")
    else:
        capture_face(sys.argv[1])

