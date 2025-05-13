import mysql.connector
from datetime import datetime, date

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="junglebite",
)
cursor = conn.cursor()

# 用戶 ID（實際可從命令列、網頁傳入等）
# cid = int(input("請輸入你的 CID："))
import sys
cid = int(sys.argv[1])

import cv2
import numpy as np
import random

bird_frame=1
bird_x = 200
bird_y = 200
bird_size=[40,30]
bird_image=[1,1]
bird_image[0]=cv2.imread(r'./Flappy-Bird1.png')
bird_image[1]=cv2.imread(r'./Flappy-Bird2.png')
bird_image[0] = cv2.resize(bird_image[0],(bird_size[0],bird_size[1]))
bird_image[1] = cv2.resize(bird_image[1],(bird_size[0],bird_size[1]))

pipes=[[350,200,False],[700,300,False],[1050,100,False]]
pipe_width=70
pipe_gap = 150

speed = 15
score = 0
run = True

def draw_pipe(img):
    global score
    height = img.shape[0]
    for pipe in pipes:
        cv2.rectangle(img, (pipe[0], 0),(pipe[0]+pipe_width,pipe[1]),(88, 218,125 ),cv2.FILLED)
        cv2.rectangle(img, (pipe[0], pipe[1]+pipe_gap),(pipe[0]+pipe_width,height),(88, 218,125 ),cv2.FILLED) 
        pipe[0]-=speed+int(score*0.1)
        if(pipe[0]<0):
            pipe[0] = max(pipes[0][0],pipes[1][0],pipes[2][0])+350
            pipe[1] = random.randint(100,300) 
            pipe[2] = False
        if pipe[0]+pipe_width<bird_x and pipe[2]==False:
            score=score+1
            pipe[2]=True

def draw_bird(img,gray_frame):
    global bird_y
    global run, bird_frame
    faceCascade=cv2.CascadeClassifier(cv2.samples.findFile(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'))
    faceRect = faceCascade.detectMultiScale(gray_frame, 1.1, 6)
    for(x,y,w,h)in faceRect:
        cv2.rectangle(frame,(x,y),(x+w,y+h),(0,255,0),2)
        bird_y = int(y+h/2)    
    img[bird_y:bird_y+bird_size[1],bird_x:bird_x+bird_size[0]]=bird_image[bird_frame]
    bird_frame = 1 - bird_frame
    for pipe in pipes:
        if pipe[0]<bird_x+bird_size[0] and pipe[0]+pipe_width>bird_x and (bird_y<pipe[1] or bird_y+bird_size[1]>pipe_gap+pipe[1]):
            run=False
            record_score(score)  # 遊戲結束時記錄分數
            break

def reset_game():
    global bird_y, score, pipes, run
    bird_y = 200
    score = 0
    run = True
    pipes = [[350,200,False],[700,300,False],[1050,100,False]]

def game_over(img):
    if cv2.waitKey(10) == ord(' '):
        reset_game()
    cv2.putText(img,"Your Score:"+str(score),(50,100),cv2.FONT_HERSHEY_SIMPLEX,1.5,(255,255,255),4)
    cv2.putText(img,"press q to quit the game",(50,200),cv2.FONT_HERSHEY_SIMPLEX,1,(255,255,255),2)
    cv2.putText(img,"press space to restart the game",(50,300),cv2.FONT_HERSHEY_SIMPLEX,1,(255,255,255),2)
    return 0

def record_score(score):
    today = date.today()
    cursor.execute("SELECT COUNT(*) FROM Coupon WHERE cid = %s AND DATE(created_at) = %s", (cid, today))
    count = cursor.fetchone()[0]
    if count >= 3:
        print("❌ 今天已達到 3 次遊戲上限，成績不再記錄。")
        return
    discount = min(score, 30)
    cursor.execute("INSERT INTO Coupon (cid, discount, created_at, game_score) VALUES (%s, %s, NOW(), %s)",
                   (cid, discount, score))
    conn.commit()
    print(f"✅ 成績 {score} 已儲存，獲得 {discount}% off 優惠券")

cap = cv2.VideoCapture(0)
while True:
    ret, frame = cap.read()
    if ret:
        frame = cv2.resize(frame,(0,0),fx=1.2,fy=1.2)
        gray_frame=cv2.cvtColor(frame,cv2.COLOR_BGR2GRAY)
        if(run==True):
            draw_pipe(frame)
            draw_bird(frame,gray_frame)
            cv2.putText(frame, str(score),(int(frame.shape[1]/2),100),cv2.FONT_HERSHEY_SIMPLEX,1.5,(255,255,255),4)
        else:
            game_over(frame)
        cv2.imshow('flappy bird',frame)
    else:
        break
    if cv2.waitKey(10)==ord('q'):
        break
