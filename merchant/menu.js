document.addEventListener("DOMContentLoaded", function () {
    $(function () {
        // 啟用拖曳排序
        $("#sortableCategoryList").sortable();

        // 儲存排序
        $("#saveCategoryOrder").click(function () {
            var orderedIDs = [];
            $("#sortableCategoryList li").each(function () {
                orderedIDs.push($(this).data("id"));
            });

            // 發送到後端更新
            $.post("../process.php", { order: orderedIDs })
            .done(function () {
                location.reload(); // 成功後直接重新整理
            })
            .fail(function (xhr, status, error) {
                alert("發生錯誤：" + error); // 只有錯誤才提示
            });

        });

    });

    

    window.onclick = function(event) {
        var dropdown = document.getElementById("myDropdown");
        if (!event.target.closest('.dropdown') && dropdown && dropdown.style.display === "block") {
            dropdown.style.display = "none";
            console.log('...get');
        }
    }
});