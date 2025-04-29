$(document).ready(function () {
    // 當搜尋輸入變化時
    $("#myInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        var hasVisible = false;

        // 恢復原本分類與商品的 HTML
        $(".product-title h4:first-child, .category-toggle").each(function () {
            var $this = $(this);

            if (!$this.data("original-html")) {
                $this.data("original-html", $this.html());
            }

            $this.html($this.data("original-html")); // 回復初始內容
        });

        if (value === "") {
            // 搜尋清空時，恢復所有類別的展開/收起狀態
            $(".product-title").show();
            $(".category-toggle").closest(".gap-1").show();
            $(".collapse").show();
            $(".no-data-row").remove();
            $(".category-toggle").each(function () {
                var categoryId = $(this).attr("href").replace("#", "");
                var collapseEl = $("#" + categoryId);
                var arrow = $(this).find(".arrow");
                if (categoryState[categoryId]) {
                    collapseEl.addClass("show");
                    arrow.text("▲");
                } else {
                    collapseEl.removeClass("show");
                    arrow.text("▼");
                }
            });
            return;
        }

        // 搜尋結果
        $(".category-toggle").each(function () {
            var categoryButton = $(this);
            var categoryId = categoryButton.attr("href");
            var productList = $(categoryId).find(".product-title");
            var originalHtml = categoryButton.data("original-html");
            var originalText = $("<div>").html(originalHtml).text().replace("▲", "").trim();
            var regex = new RegExp(value, "ig");
            var matchedCategory = false;
            var hasProductVisible = false;

            if (originalText.toLowerCase().indexOf(value) > -1) {
                // 類別名稱有符合
                var highlightedCategory = originalText.replace(regex, function (match) {
                    return `<mark style="background-color: #FFB524; padding:0;">${match}</mark>`;
                });

                categoryButton.html('<span class="arrow">▲</span> ' + highlightedCategory);
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
                        return `<mark style="background-color: #FFB524; padding:0;">${match}</mark>`;
                    });
                    title.html(highlightedProduct);
                });

            } else {
                // 分類不符，檢查商品
                categoryButton.html(categoryButton.data("original-html"));
                var anyMatch = false;

                productList.each(function () {
                    var product = $(this);
                    var title = product.find("h4:first");

                    if (!title.data("original-text")) {
                        title.data("original-text", title.text());
                    }

                    var originalProductText = title.data("original-text");

                    if (originalProductText.toLowerCase().indexOf(value) > -1) {
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

    // 防止 enter 觸發表單送出
    $("#myInput").on("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
        }
    });

    // 在頁面加載時，根據 collapse 的狀態來更新箭頭
    $(".category-toggle").each(function () {
        var categoryId = $(this).attr("href").replace("#", "");
        var collapseEl = $("#" + categoryId);
        var arrow = $(this).find(".arrow");

        // 根據 collapse 是否有 show 顯示來設置箭頭
        if (collapseEl.hasClass("show")) {
            arrow.text("▲");  // 如果是展開，箭頭顯示為▲
        } else {
            arrow.text("▼");  // 如果是收起，箭頭顯示為▼
        }
    });

    // 確保點擊事件正確更新箭頭
    $(".category-toggle").on("click", function () {
        var categoryId = $(this).attr("href").replace("#", "");
        var arrow = $(this).find(".arrow");
        var collapseEl = $("#" + categoryId);

        // 根據 collapse 的展開/收起狀態更新箭頭
        if (collapseEl.hasClass("show")) {
            arrow.text("▲");
        } else {
            arrow.text("▼");
        }
    });

    // 收合展開時更新箭頭
    $(".collapse").on("shown.bs.collapse", function () {
        var categoryId = $(this).attr("id");
        var arrow = $(`.category-toggle[href="#${categoryId}"]`).find(".arrow");
        arrow.text("▲"); // 展開後顯示▲
    });

    $(".collapse").on("hidden.bs.collapse", function () {
        var categoryId = $(this).attr("id");
        var arrow = $(`.category-toggle[href="#${categoryId}"]`).find(".arrow");
        arrow.text("▼"); // 收起後顯示▼
    });

    // 手動收起類別
    $(".category-toggle").on("click", function (e) {
        var categoryId = $(this).attr("href").replace("#", "");
        var collapseEl = $("#" + categoryId);

        // 判斷當前是否手動收起
        if (!collapseEl.hasClass("show")) {
            manuallyCollapsed.push(categoryId);
        } else {
            var index = manuallyCollapsed.indexOf(categoryId);
            if (index > -1) {
                manuallyCollapsed.splice(index, 1);
            }
        }
    });

    // 處理搜尋欄清空時的收回邏輯
    $("#myInput").on("keyup", function () {
        if ($(this).val() === "") {
            $(".collapse").each(function () {
                var categoryId = $(this).attr("id");
                // 檢查該類別是否是手動收起的，若是則強制保持收起
                if (manuallyCollapsed.indexOf(categoryId) > -1) {
                    $(this).collapse('hide');
                } else {
                    $(this).collapse('show');
                }
            });
        }
    });
});
