# 🐤 JungleBite Flappy Bird 遊戲模組說明

這是結合臉部偵測、分數記錄與優惠券發放功能的 Flappy Bird 遊戲，作為 JungleBite 平台互動式行銷機制的一環。

---

## 📦 專案內容

| 檔案名稱                                  | 說明                                         |
| -------------------------------------     | ----------------------                       |
| `flappy_bird_ans.py`                      | 主遊戲程式，支援人臉操控、分數記錄、圖表產生 |
| `requirements.txt`                        | 所有需要安裝的 Python 套件清單               |
| `install_all.bat`                         | 一鍵安裝所需套件（Windows）                  |
| `score_chart.png`                         | 自動產出的得分趨勢圖表（每日最高分）         |
| `Flappy-Bird1.png`、`Flappy-Bird2.png`    | 小鳥角色圖片（動畫用）                       |

---

## 💻 執行步驟

### 1️⃣  安裝 Python 套件

#### 雙擊安裝（建議）

執行 install_all.bat 即可自動安裝需要的環境。

#### 手動安裝

打開終端機，執行：

```bash

pip install -r requirements.txt

```

---

### 2️⃣  開始遊戲

```bash

python flappy_bird_ans.py <你的CID>

```

✅ 請將 `<你的CID>` 替換為登入者的使用者 ID，例如 `python flappy_bird_ans.py 12`

---

## 🎮 遊戲操作

* 🟢 臉部偵測控制小鳥上下
* 🟡 每天最多玩 3 次
* 🟣 遊戲結束將自動儲存分數與折扣券
* 🟤 折扣券依得分產生，最多 30%
* 🔵 成績將顯示於網站 my\_coupons.php 並附每日圖表

---

## 📈 得分圖表

每次遊戲後，系統會自動產生 `score_chart.png` 圖表
並於 `my_coupons.php` 右側顯示每日最高得分折線圖，追蹤玩家成長！

---

## 🔗 建議搭配頁面

此遊戲與 JungleBite 網站中的 `my_coupons.php` 頁面整合：

* 顯示今日剩餘可玩次數
* 顯示歷史優惠券清單
* 顯示每次遊戲記錄 + 得分趨勢圖
