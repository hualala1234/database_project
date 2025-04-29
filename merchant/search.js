$(document).ready(function () {
    $("#myInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        var hasVisible = false;

        // 恢復乾淨文字
        $(".product-title h4:first-child, .category-toggle").each(function () {
            var $this = $(this);
            var arrowSpan = $this.find("span").prop('outerHTML') || '';
            var originalText = $this.data("original-text");

            if (!originalText) {
                originalText = $this.text().replace("▲", "").trim();
                $this.data("original-text", originalText);
            }
            $this.html(arrowSpan + ' ' + originalText);
        });

        if (value === "") {
            $(".product-title").show();
            $(".category-toggle").closest(".gap-1").show();
            $(".collapse").show();
            $(".no-data-row").remove();
            return;
        }

        $(".category-toggle").each(function () {
            var categoryButton = $(this);
            var categoryId = categoryButton.attr("href");
            var productList = $(categoryId).find(".product-title");

            var arrowSpan = categoryButton.find("span").prop('outerHTML') || '';
            var categoryText = categoryButton.data("original-text");
            var matchedCategory = false;
            var hasProductVisible = false;

            if (categoryText.toLowerCase().indexOf(value) > -1) {
                // 分類名稱有符合
                var regex = new RegExp(value, "ig");
                var highlightedCategory = categoryText.replace(regex, function (match) {
                    return `<mark style="background-color: #FFB524; padding:0;">${match}</mark>`;
                });
                categoryButton.html(arrowSpan + ' ' + highlightedCategory);

                categoryButton.closest(".gap-1").show();
                $(categoryId).show();
                productList.show();  // ←★ 重點：分類符合時，底下全部產品都顯示！
                hasVisible = true;
                matchedCategory = true;

                // ★★★ 新增：即使分類中，底下商品也要一個一個高亮！
                productList.each(function () {
                    var product = $(this);
                    var titleElement = product.find("h4:first");
                    var originalProductText = titleElement.data("original-text");

                    if (!originalProductText) {
                        originalProductText = titleElement.text();
                        titleElement.data("original-text", originalProductText);
                    }

                    var highlightedProduct = originalProductText.replace(regex, function (match) {
                        return `<mark style="background-color: #FFB524; padding:0;">${match}</mark>`;
                    });
                    titleElement.html(highlightedProduct);
                });
            } else {
                categoryButton.html(arrowSpan + ' ' + categoryText);

                // 如果分類沒符合，再檢查底下每個產品
                productList.each(function () {
                    var product = $(this);
                    var titleElement = product.find("h4:first");
                    var originalProductText = titleElement.data("original-text");

                    if (!originalProductText) {
                        originalProductText = titleElement.text();
                        titleElement.data("original-text", originalProductText);
                    }

                    if (originalProductText.toLowerCase().indexOf(value) > -1) {
                        var regex = new RegExp(value, "ig");
                        var highlightedProduct = originalProductText.replace(regex, function (match) {
                            return `<mark style="background-color: #FFB524; padding:0;">${match}</mark>`;
                        });
                        titleElement.html(highlightedProduct);
                        product.show();
                        hasProductVisible = true;
                    } else {
                        product.hide();
                    }
                });

                // 如果底下有產品符合
                if (hasProductVisible) {
                    categoryButton.closest(".gap-1").show();
                    $(categoryId).show();
                    hasVisible = true;
                } else {
                    // 分類和產品都沒中，整塊隱藏
                    categoryButton.closest(".gap-1").hide();
                    $(categoryId).hide();
                }
            }
        });

        $(".no-data-row").remove();
        if (!hasVisible) {
            $("#addp").show();  // 顯示 #addp 元素
            $("#addp").append(
                "<div class='no-data-row' style='text-align:center; color: #999; padding: 20px;'>🔍 找不到符合的資料</div>"
            );
        } else {
            $("#addp").hide();  // 如果有資料符合，隱藏 #addp
        }
    });
});

$(document).ready(function () {
    $("#myInput").on("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault(); // 只阻止按下 Enter 時
        }
    });

    $("#myInput").on("keyup", function () {
        // 你原本的搜尋邏輯...
    });
});
