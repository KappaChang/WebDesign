$(function(){

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


	//mobile用選單
	$('#NavMobile').click(function(){
		$('#MobileMenuGroup').slideToggle("fast");
	});

	//sing-up
	$('.SingUpBtn').click(function(){
		$('#SingUpDesc').hide();
		$('#SingUpForm').slideDown("fast");
	});
	$('#SingUpSendBtn').click(function(){
        var formData = $('.sing-up-form').serialize();
        sendAjax(formData, function(data) {
            if (data.error) {
                if (data.required_field.length > 0) {
                    for (var i = data.required_field.length - 1; i >= 0; i--) {
                        $('[name='+data.required_field[i]+']').addClass('error');
                    };
                    alert(data.message);
                    return;
                };
            } else {
                $('#SingUpForm').hide();
                $('#SingUpComplete').slideDown("fast");
            }
        });
    });

    // experience心得分享
    $('.ExperienceBtn').click(function(){
        $('.ExperienceBtn').hide();
        $('#ExperienceDesc').hide();
        $('.share-block').hide();
        $('#ExperienceForm').slideDown("fast");
    });
    $('#ExperienceSendBtn').click(function(){
        var formData = $('.experience-form').serialize();
        formData += '&content='+ encodeURIComponent(CKEDITOR.instances.editor.getData());
        sendAjax(formData, function(data) {
            if (data.error) {
                if (typeof(data.required_field) != 'undefined' && data.required_field.length > 0) {
                    console.log(data.required_field)
                    for (var i = data.required_field.length - 1; i >= 0; i--) {
                        $('[name='+data.required_field[i]+']').addClass('error');
                    };
                    alert(data.message);
                    return;
                } else if (data.status_code == '10001') {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                };
                return;
            } else {
                if (typeof data.redirect !== 'undefined' && data.redirect != '') {
                    location.href = data.redirect;
                } else {
                    $('#ExperienceForm').hide();
                    $('#ExperienceComplete').slideDown("fast");
                }
            }
        });
    });
	$('#BackShareBlockBtn').click(function(){
		$('#ExperienceComplete').hide();
		$('.share-block').slideDown("fast");
	});

    $('form').delegate('.error', 'blur', function(event) {
        if ($(this).val() != '') {
            $(this).removeClass('error');
        };

    });

	// 閱讀更多
	// $('.shareDesc').readmore({
 //        moreLink: '<a href="#">閱讀全文</a>',
 //        lessLink: '<a href="#">收合</a>',
 //        maxHeight: 104,
 //        afterToggle: function(trigger, element, expanded) {
 //          if(! expanded) { // The "Close" link was clicked
 //            $('html, body').animate( { scrollTop: element.offset().top }, {duration: 100 } );
 //          }
 //        }
 //    });

    //預覽網頁
    $('#PreviewHtml').click(function(e) {
        e.preventDefault();
        console.log(winRef);
        var _cke_htmlToLoad = window.CKEDITOR.instances.editor.getData();
        var winRef = window.open($(this).attr('href')+'?12312', "preview", 'height=500, width=850, top=' + ($(window).height() / 2 - 225) + ', left=' + $(window).width() / 2 + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        $(winRef.document.body).html(_cke_htmlToLoad);
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
