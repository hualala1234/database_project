// 全域宣告：用來儲存手動收起的分類 ID
var manuallyCollapsed = [];

$(document).ready(function () {

    // 初始化箭頭狀態
    $(".order-toggle").each(function () {
        var categoryId = $(this).attr("href").replace("#", "");
        var collapseEl = $("#" + categoryId);
        var arrow = $(this).find(".arrow");

        if (collapseEl.hasClass("show")) {
            arrow.text("▲");
        } else {
            arrow.text("▼");
            manuallyCollapsed.push(categoryId); // 預設不是展開的就視為收起
        }
    });

    // 點擊分類按鈕時：切換收起狀態 + 更新箭頭 + 更新 manuallyCollapsed 陣列
    $(".order-toggle").on("click", function () {
        var categoryId = $(this).attr("href").replace("#", "");
        var collapseEl = $("#" + categoryId);
        var arrow = $(this).find(".arrow");

        // 延遲判斷展開狀態，讓 Bootstrap 完成動畫
        setTimeout(function () {
            if (collapseEl.hasClass("show")) {
                arrow.text("▲");
                // 若為展開，從 manuallyCollapsed 移除
                var index = manuallyCollapsed.indexOf(categoryId);
                if (index > -1) manuallyCollapsed.splice(index, 1);
            } else {
                arrow.text("▼");
                if (!manuallyCollapsed.includes(tranId)) {
                    manuallyCollapsed.push(tranId);
                }
            }
        }, 350); // 需與 Bootstrap 動畫時間一致
    });

    // 封裝：根據 manuallyCollapsed 陣列應用展開/收起狀態
    function applyManualCollapseState() {
        $(".collapse").each(function () {
            var categoryId = $(this).attr("id");
            if (manuallyCollapsed.includes(categoryId)) {
                $(this).collapse('hide');
                $(`.order-toggle[href="#${categoryId}"] .arrow`).text("▼");
            } else {
                $(this).collapse('show');
                $(`.order-toggle[href="#${categoryId}"] .arrow`).text("▲");
            }
        });
    }

    // 搜尋功能邏輯
    $("#myInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        var hasVisible = false;

        // 還原所有分類與商品的原始內容
        $(".product-title h4:first-child, .order-toggle").each(function () {
            var $this = $(this);
            if (!$this.data("original-html")) {
                $this.data("original-html", $this.html());
            }
            $this.html($this.data("original-html"));
        });

        if (value === "") {
            $(".product-title").show();
            $(".order-toggle").closest(".gap-1").show();
            $(".collapse").show();
            $(".no-data-row").remove();
            applyManualCollapseState(); // 使用者清空搜尋時還原手動狀態
            return;
        }

        $(".order-toggle").each(function () {
            var categoryButton = $(this);
            var categoryId = categoryButton.attr("href");
            var productList = $(categoryId).find(".product-title");
            var originalHtml = categoryButton.data("original-html");
            var originalText = $("<div>").html(originalHtml).text().replace("▲", "").replace("▼", "").trim();
            var regex = new RegExp(value, "ig");
            var matchedCategory = false;
            var hasProductVisible = false;

            if (originalText.toLowerCase().includes(value)) {
                var highlightedCategory = originalText.replace(regex, function (match) {
                    return `<mark style="background-color: #FFB524; padding:0;">${match}</mark>`;
                });

                categoryButton.html('<span class="arrow text-white">▲</span> ' + highlightedCategory);
                categoryButton.closest(".gap-1").show();
                $(categoryId).show();
                productList.show();
                hasVisible = true;

                productList.each(function () {
                    var product = $(this);
                    var title = product.find("h4:first");
                    if (!title.data("original-text")) {
                        title.data("original-text", title.text());
                    }
                    var originalProductText = title.data("original-text");
                    var highlightedProduct = originalProductText.replace(regex, function (match) {
                        return `<mark style="background-color: #FFB524; padding:0;" class="text-white">${match}</mark>`;
                    });
                    title.html(highlightedProduct);
                });
            } else {
                categoryButton.html(originalHtml);
                var anyMatch = false;

                productList.each(function () {
                    var product = $(this);
                    var title = product.find("h4:first");
                    if (!title.data("original-text")) {
                        title.data("original-text", title.text());
                    }
                    var originalProductText = title.data("original-text");

                    if (originalProductText.toLowerCase().includes(value)) {
                        var highlighted = originalProductText.replace(regex, function (match) {
                            return `<mark style="background-color: #FFB524; padding:0;">${match}</mark>`;
                        });
                        title.html(highlighted);
                        product.show();
                        anyMatch = true;
                    } else {
                        product.hide();
                    }
                });

                if (anyMatch) {
                    categoryButton.closest(".gap-1").show();
                    $(categoryId).show();
                    hasVisible = true;
                } else {
                    categoryButton.closest(".gap-1").hide();
                    $(categoryId).hide();
                }
            }
        });

        $(".no-data-row").remove();
        if (!hasVisible) {
            $("#addp").show().append(
                "<div class='no-data-row' style='text-align:center; color: #999; padding: 20px;'>🔍 找不到符合的資料</div>"
            );
        } else {
            $("#addp").hide();
        }
    });

    // 防止 enter 提交表單
    $("#myInput").on("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
        }
    });

});
