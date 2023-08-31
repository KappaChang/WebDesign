$(function(){

    
    $(window).bind('resize', function(){
        var mW = $(window).width();
        var mH = $(window).height();
        console.log("Widht: "+mW, "Height: "+mH);
    });  

    //跳到指定位置
    $('.goToBtn').click(function (){
        var target = $(this).data('goto');
        var scrollTo = 0;
        var mW = $(window).width();

        //假如不是TOP鈕
        if (target != 'TOP') {
            if (mW <= 979) {
                scrollTo = $('#'+target).offset().top-45;
                $('#menuMobile ul').slideToggle("fast");
            } else {
                scrollTo = $('#'+target).offset().top; 
            }
        }
        $('html,body').animate({
            scrollTop:scrollTo
        }, 1000);
        
        return false;
    });    
    
    
  

    $(window).scroll(function() {
        //用來取得目前的位置距離網頁頂端有多遠
        if ( $(this).scrollTop() > 100){
            $('.go-top').fadeIn(250);
        } else {
            $('.go-top').stop().fadeOut(250);
        }
    });

	//mobile用選單
	$("#menuMobile").click(function(){
		$("#menuMobile ul").slideToggle("fast");
	});

    //fb-share
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1&appId=339999192774080";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    //google-plus
    (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();

    //twitter
    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');

});
