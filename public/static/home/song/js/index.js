/**
 * Created by Administrator on 2018/7/2.
 */
(function (doc_01, win_01) {
    var docEl_01 = doc_01.documentElement;
    var resizeEvt_01 = 'orientationchange' in window ? 'orientationchange' : 'resize';
    var recalc_01 = function () {
        var clientWidth_01 = docEl_01.clientWidth;
        var clientheight_01 = docEl_01.clientHeight;
        if (!clientWidth_01) return;
        if(clientWidth_01<641)
        {
            docEl_01.style.fontSize = (clientWidth_01/640*100).toFixed(1)+'px';
        }
        else
        {
            docEl_01.style.fontSize = 100+'px';
        }
    };
    recalc_01();
    if (!doc_01.addEventListener) return;
    win_01.addEventListener(resizeEvt_01, recalc_01, false);
})(document, window);

$(function(){
    //获取短信验证码
    var validCode=true;
    $(".msgs").click (function  () {
        if($('#mobile').val().length!=11){
            return false;
        }
        $(this).prop('disabled',true)
        var time=60;
        var code=$(this);
        if (validCode) {
            validCode=false;
            code.addClass("msgs1");
            var t=setInterval(function  () {
                time--;
                code.html(time+"秒");
                if (time==0) {
                    clearInterval(t);
                    code.html("重新获取");
                    $('.msgs').prop('disabled',false)
                    validCode=true;
                    code.removeClass("msgs1");
                }
            },1000)
        }
    });


});