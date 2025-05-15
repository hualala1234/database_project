# import pandas as pd
# import matplotlib.pyplot as plt

# # 讀取資料
# df = pd.read_csv("user_score_data.csv", parse_dates=['play_date'])

# # 成績等級分類
# def classify(score):
#     if score >= 25:
#         return '🎯 神人'
#     elif score >= 15:
#         return '🔥 高手'
#     elif score >= 5:
#         return '😎 普通'
#     else:
#         return '🥚 菜鳥'

# df['grade'] = df['game_score'].apply(classify)

# # 統計每日資訊
# daily_stats = df.groupby('play_date').agg(
#     highest_score=('game_score', 'max'),
#     avg_score=('game_score', 'mean'),
#     games_played=('game_score', 'count')
# ).reset_index()

# # 畫圖
# plt.figure(figsize=(12, 8))

# # 1. 每日最高分
# plt.subplot(3, 1, 1)
# plt.plot(daily_stats['play_date'], daily_stats['highest_score'], marker='o')
# plt.title('📈 每日最高分')
# plt.ylabel('分數')
# plt.grid(True)

# # 2. 每日次數 + 平均分數
# plt.subplot(3, 1, 2)
# plt.bar(daily_stats['play_date'], daily_stats['games_played'], label='遊戲次數', alpha=0.7)
# plt.plot(daily_stats['play_date'], daily_stats['avg_score'], color='red', marker='s', label='平均分數')
# plt.legend()
# plt.ylabel('次數 / 分數')
# plt.grid(True)

# # 3. 成績等級比例
# plt.subplot(3, 1, 3)
# grade_counts = df['grade'].value_counts().sort_index()
# plt.pie(grade_counts, labels=grade_counts.index, autopct='%1.1f%%')
# plt.title('🧠 成績分類比例')

# plt.tight_layout()
# plt.savefig("score_chart.png")
# plt.show()
# print("✅ score_chart.png 已成功產生")

# score_data.py
import pandas as pd
import matplotlib.pyplot as plt
import matplotlib
import emoji
import sys
sys.stdout.reconfigure(encoding='utf-8')
print(f"[DEBUG] sys.argv = {sys.argv}")

# 從參數讀取 cid
cid = sys.argv[1] if len(sys.argv) > 1 else 'unknown'
csv_file = f"user_score_data_{cid}.csv"
png_file = f"score_chart_{cid}.png"

matplotlib.use('Agg')  # 確保在無 GUI 環境下也能執行
print(f"[DEBUG] 正在處理 CID: {cid}, 輸出圖表為: {png_file}")

# 讀取 CSV
try:
    # df = pd.read_csv("user_score_data.csv", parse_dates=['play_date'])
    df = pd.read_csv(csv_file, parse_dates=['play_date'])
    plt.savefig(png_file)
    print(f"✅ {csv_file} 已成功讀取")

except Exception as e:
    # with open("log.txt", "w") as log:
    with open("log.txt", "a", encoding="utf-8") as log:
        log.write(f"[Error] CSV read failed: {e}\n")
    exit()

# 分類等級

def classify(score):
    if score >= 25:
        return '🎊 Legend'
    elif score >= 15:
        return '🤜 Master'
    elif score >= 5:
        return '😎 Intermediate'
    else:
        return '👎 Rookie'

df['grade'] = df['game_score'].apply(classify)

# 每日統計
try:
    daily_stats = df.groupby('play_date').agg(
        highest_score=('game_score', 'max'),
        avg_score=('game_score', 'mean'),
        games_played=('game_score', 'count')
    ).reset_index()
except Exception as e:
    # with open("log.txt", "a") as log:
    with open("log.txt", "a", encoding="utf-8") as log:
        log.write(f"[Error] Aggregation failed: {e}\n")
    exit()

# 畫圖
try:
    plt.figure(figsize=(12, 8))

    plt.subplot(3, 1, 1)
    plt.plot(daily_stats['play_date'], daily_stats['highest_score'], marker='o')
    plt.title('📈 Daily Highest Score')
    plt.ylabel('Score')
    plt.grid(True)

    plt.subplot(3, 1, 2)
    plt.bar(daily_stats['play_date'], daily_stats['games_played'], alpha=0.7, label='Play Count')
    plt.plot(daily_stats['play_date'], daily_stats['avg_score'], color='red', marker='s', label='Average Score')
    plt.ylabel('Times / Score')
    plt.grid(True)
    plt.legend()

    grade_counts = df['grade'].value_counts().sort_index()
    plt.subplot(3, 1, 3)
    plt.pie(grade_counts, labels=grade_counts.index, autopct='%1.1f%%')
    plt.title('🧠 Ratio of grades')

    plt.tight_layout()
    # plt.savefig("score_chart.png")
    plt.savefig(png_file)

    print("✅ score_chart.png 已成功產生")
    print(f"✅ {png_file} 已成功產生")
except Exception as e:
    # with open("log.txt", "a") as log:
    with open("log.txt", "a", encoding="utf-8") as log:
        log.write(f"[Error] Plotting failed: {e}\n")
    exit()
