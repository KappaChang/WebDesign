$(function(){

	//跳到指定位置
    $('.goToBtn').click(function (){
        var target = $(this).data('goto');
        var scrollTo = 0;
        var mW = $(window).width();
        //假如不是TOP鈕
        if (target != 'TOP') {
            if (mW <= 979) {
                scrollTo = $('#'+target).offset().top-60;
                //scrollTo = $('#'+target).offset().top;
                $('#menuMobile').slideToggle("fast");
            } else {
                scrollTo = $('#'+target).offset().top;
            }
        }
        console.log(scrollTo);
        console.log(Math.floor(scrollTo));

		$('.menu li, #menuMobile li').removeClass('active');
		$(this).addClass('active');
		$('html,body').animate({scrollTop:scrollTo}, 1000);

        return false;
    });

    $(window).scroll(function() {
        //用來取得目前的位置距離網頁頂端有多遠
        if ( $(this).scrollTop() > 100){
            $('.go-top').fadeIn(250);
        } else {
            $('.go-top').stop().fadeOut(250);
        }

        //Adding fixed position to header
	    if ($(document).scrollTop() >= 559) {
	      $('header').addClass('header-fixed-top');
	      //$('html').addClass('has-fixed-nav');
	    } else {
	      $('header').removeClass('header-fixed-top');
	      //$('html').removeClass('has-fixed-nav');
	    }
    });

	//mobile用選單
	$("#menuMobileBtn").click(function(){
		$("#menuMobile").slideToggle("fast");
	});

});