import mysql.connector
from datetime import date
import sys
import cv2
import numpy as np
import random

# 讀取使用者 CID
cid = int(sys.argv[1])

# 建立資料庫連線
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="junglebite",
)
cursor = conn.cursor()

# 初始狀態
bird_frame = 1
bird_x, bird_y = 200, 200
bird_size = [40, 30]
bird_image = [
    cv2.resize(cv2.imread('./Flappy-Bird1.png'), tuple(bird_size)),
    cv2.resize(cv2.imread('./Flappy-Bird2.png'), tuple(bird_size))
]

pipes = [[350, 200, False], [700, 300, False], [1050, 100, False]]
pipe_width, pipe_gap = 70, 150
speed = 15
score = 0
state = "ready"  # "ready" / "playing" / "gameover"

# 記錄分數與優惠券
def record_score(score):
    today = date.today()
    cursor.execute("SELECT COUNT(*) FROM Coupon WHERE cid = %s AND game=2 AND DATE(created_at) = %s", (cid, today))
    count = cursor.fetchone()[0]
    # qualified = count < 3
    print(f"Count: {count}")
    qualified = (count + 1) <= 3
    discount = min(score, 30) if qualified and score > 0 else 0

    cursor.execute("""
        INSERT INTO Coupon (cid, discount, created_at, game_score, qualified, used,game)
        VALUES (%s, %s, NOW(), %s, %s, FALSE,%s)
    """, (cid, discount, score, qualified,2))
    conn.commit()

    if qualified and score > 0:
        print(f"V Score {score} saved. Discount: {discount}%")
    elif qualified and score == 0:
        print("V Score 0 saved. No discount")
    else:
        print(f"X Score {score} saved. Already 3 coupons today~ No discount")

# 重設遊戲
def reset_game():
    global bird_y, score, pipes, state
    bird_y = 200
    score = 0
    state = "playing"
    pipes = [[350, 200, False], [700, 300, False], [1050, 100, False]]

# 畫出水管並更新分數
def draw_pipe(img):
    global score
    height = img.shape[0]
    for pipe in pipes:
        cv2.rectangle(img, (pipe[0], 0), (pipe[0] + pipe_width, pipe[1]), (88, 218, 125), cv2.FILLED)
        cv2.rectangle(img, (pipe[0], pipe[1] + pipe_gap), (pipe[0] + pipe_width, height), (88, 218, 125), cv2.FILLED)
        pipe[0] -= speed + int(score * 0.1)
        if pipe[0] < 0:
            pipe[0] = max(pipes[0][0], pipes[1][0], pipes[2][0]) + 350
            pipe[1] = random.randint(100, 300)
            pipe[2] = False
        if pipe[0] + pipe_width < bird_x and not pipe[2]:
            score += 1
            pipe[2] = True

# 畫出小鳥，檢查碰撞
def draw_bird(img, gray_frame):
    global bird_y, bird_frame, state
    faceCascade = cv2.CascadeClassifier(cv2.samples.findFile(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'))
    faceRect = faceCascade.detectMultiScale(gray_frame, 1.1, 6)
    for (x, y, w, h) in faceRect:
        cv2.rectangle(frame, (x, y), (x + w, y + h), (0, 255, 0), 2)
        bird_y = int(y + h / 2)
    img[bird_y:bird_y + bird_size[1], bird_x:bird_x + bird_size[0]] = bird_image[bird_frame]
    bird_frame = 1 - bird_frame

    for pipe in pipes:
        if pipe[0] < bird_x + bird_size[0] and pipe[0] + pipe_width > bird_x and \
           (bird_y < pipe[1] or bird_y + bird_size[1] > pipe_gap + pipe[1]):
            record_score(score)
            state = "gameover"
            break

# 主遊戲迴圈
cap = cv2.VideoCapture(0)
while True:
    ret, frame = cap.read()
    if not ret:
        break

    frame = cv2.resize(frame, (0, 0), fx=1.2, fy=1.2)
    gray_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

    if state == "ready":
        cv2.putText(frame, "Press any key to start", (100, 200), cv2.FONT_HERSHEY_SIMPLEX, 1.2, (255, 255, 255), 3)
        cv2.imshow("flappy bird", frame)
        key = cv2.waitKey(10)
        if key != -1:
            reset_game()

    elif state == "playing":
        draw_pipe(frame)
        draw_bird(frame, gray_frame)
        cv2.putText(frame, str(score), (int(frame.shape[1] / 2), 100), cv2.FONT_HERSHEY_SIMPLEX, 1.5, (255, 255, 255), 4)
        cv2.imshow("flappy bird", frame)
        if cv2.waitKey(10) == ord('q'):
            break

    elif state == "gameover":
        cv2.putText(frame, "Game Over!", (100, 150), cv2.FONT_HERSHEY_SIMPLEX, 1.8, (0, 0, 255), 5)
        cv2.putText(frame, f"Your Score: {score}", (100, 250), cv2.FONT_HERSHEY_SIMPLEX, 1.3, (255, 255, 255), 3)
        cv2.putText(frame, "Press any key to restart / press q to leave", (100, 350), cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 255, 255), 2)
        cv2.imshow("flappy bird", frame)
        key = cv2.waitKey(0)
        if key == ord('q'):
            break
        else:
            reset_game()

cap.release()
cv2.destroyAllWindows()
