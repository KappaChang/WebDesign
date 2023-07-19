$(function(){

	//到指定錨點+動態
	//範例: <a href="#BLOCK02">選單一</a> 至 <id="BLOCK02">
	// $('a').click(function(){
	//     $('html, body').animate({
	//         scrollTop: $( $(this).attr('href') ).offset().top
	//     }, 1000);
	//     return false;
	// });
	
	//按鈕跳至指定區塊
	$('.NAV01').click(function(){
		$('#MobileMenuGroup').slideToggle("fast");
		$('html,body').animate({
			scrollTop:$('#BLOCK02').offset().top
		}, 1000);
		return false;
	});
	$('.NAV02').click(function(){
		$('#MobileMenuGroup').slideToggle("fast");
		$('html,body').animate({
			scrollTop:$('#BLOCK03').offset().top
		}, 1000);
		return false;
	});
	$('.NAV03').click(function(){
		$('#MobileMenuGroup').slideToggle("fast");
		$('html,body').animate({
			scrollTop:$('#BLOCK04').offset().top
		}, 1000);
		return false;
	});

	//回到網頁最上端
	$('#TOP').click(function(){
	    $('html, body').animate({scrollTop:0}, 1000);
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

});