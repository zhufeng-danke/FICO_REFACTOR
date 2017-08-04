$(function () {
    $('#txt_search').on('input propertychange',function (evt) {
        ChangeCoords(); //控制查询结果div坐标
        var k = window.event ? evt.keyCode : evt.which;
        //输入框的id为txt_search，这里监听输入框的keyup事件
        //不为空 && 不为上箭头或下箭头或回车
        if ($("#txt_search").val() != "" && k != 38 && k != 40 && k != 13) {
                var keyword = $("#txt_search").val();
            $.ajax({
                type: 'GET',
                //async: false, //同步执行，不然会有问题
                dataType: "json",
                url: keyword_url,
                data: {'keyword':keyword},
                contentType: "application/json; charset=utf-8",
                error: function (msg) {//请求失败处理函数
                    alert("数据加载失败");
                },
                success: function (data) { //请求成功后处理函数。
                    var objData = eval("(" + data.message + ")");
                    if (data.status == 'success') {
                        var layer = "";
                        layer = "<table id='aa'>";
                        $.each(objData, function (idx, item) {
                            layer += "<tr class='line'><td class='std'>" + item + "</td></tr>";
                        });
                        layer += "</table>";
                        //将结果添加到div中
                        $("#searchresult").empty();
                        $("#searchresult").append(layer);
                        $(".line:first").addClass("hover");
                        $("#searchresult").css("display", "");
                        //鼠标移动事件
                        $(".line").hover(function () {
                            $(".line").removeClass("hover");
                            $(this).addClass("hover");
                        }, function () {
                            $(this).removeClass("hover");
                            //$("#searchresult").css("display", "none");
                        });
                        //鼠标点击事件
                        $(".line").click(function () {
                            $("#txt_search").val($(this).text());
                            $("#searchresult").css("display", "none");
                        });
                    } else {
                        $("#searchresult").empty();
                        $("#searchresult").css("display", "none");
                    }
                }
            });
        }
        else if (k == 38) {//上箭头
            $('#aa tr.hover').prev().addClass("hover");
            $('#aa tr.hover').next().removeClass("hover");
            $('#txt_search').val($('#aa tr.hover').text());
        } else if (k == 40) {//下箭头
            $('#aa tr.hover').next().addClass("hover");
            $('#aa tr.hover').prev().removeClass("hover");
            $('#txt_search').val($('#aa tr.hover').text());
        }
        else if (k == 13) {//回车
            $('#txt_search').val($('#aa tr.hover').text());
            $("#searchresult").empty();
            $("#searchresult").css("display", "none");
        }
        else {
            $("#searchresult").empty();
            $("#searchresult").css("display", "none");
        }
    });
    $("#searchresult").bind("mouseleave", function () {
        $("#searchresult").empty();
        $("#searchresult").css("display", "none");
    });
});
//设置查询结果div坐标

function ChangeCoords() {
    //    var left = $("#txt_search")[0].offsetLeft; //获取距离最左端的距离，像素，整型
    //    var top = $("#txt_search")[0].offsetTop + 26; //获取距离最顶端的距离，像素，整型（20为搜索输入框的高度）
    var left = $("#txt_search").position().left; //获取距离最左端的距离，像素，整型
    var top = $("#txt_search").position().top + 20; ; //获取距离最顶端的距离，像素，整型（20为搜索输入框的高度）
    $("#searchresult").css("left", left + "px"); //重新定义CSS属性
    $("#searchresult").css("top", top + "px"); //同上
}