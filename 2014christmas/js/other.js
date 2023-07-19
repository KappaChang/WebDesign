$(function(){

    // setInterval(function(){myTimer();}, 1000);
    var myVar = setInterval(function(){ myTimer() }, 1000);

    function myTimer() {
        //活動按鈕時間
        //現在時間
        var nowTime = new Date();
        //活動開始時間
        var startTime = new Date("2014/12/25 00:00:00");
        //活動結束時間
        var endTime = new Date("2014/12/25 23:59:59");


        //活動尚未開始
        if (nowTime < startTime) {
            $(".btn-not-start").show();
            //console.log("活動尚未開始");
        //活動結束
        } else if (nowTime > endTime) {
            $(".btn-go-event").hide()
            $(".btn-not-start").text("活動時間已截止").show();
            //console.log("活動時間已截止");
            clearInterval(myVar);
        //活動ING
        } else {
            $(".btn-not-start").hide();
            $(".btn-go-event").show();
            //console.log("活動ING");
        }

        //console.log(nowTime);

    };

    // jQuery mCustomScrollbar
    $.mCustomScrollbar.defaults.scrollButtons.enable=true; //enable scrolling buttons by default
    $.mCustomScrollbar.defaults.axis="y";
    $(".scrollbar").mCustomScrollbar({theme:"dark-2"});


    // jQuery bPopup
    // Binding a click event
    // From jQuery v.1.7.0 use .on() instead of .bind()
    $(".eventPopBtn").bind("click", function(e) {

        // Prevents the default action to be triggered. 
        e.preventDefault();

        // Triggering bPopup when click event is fired
        $("#eventPopBlock").bPopup({
            modalClose: false,
            opacity: 0.6,
            positionStyle: "fixed",
            //position: ["auto",50],
            amsl: [0],
        });

    });
    $(".hoomiaPopBtn").bind("click", function(e) {

        // Prevents the default action to be triggered. 
        e.preventDefault();

        // Triggering bPopup when click event is fired
        $("#hoomiaPopBlock").bPopup({
            modalClose: false,
            opacity: 0.6,
            positionStyle: "fixed",
            //position: ["auto",50],
            amsl: [0],
        });

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
    }(document, "script", "facebook-jssdk"));

    //google-plus
    (function() {
        var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
        po.src = "https://apis.google.com/js/plusone.js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
    })();

    //twitter
    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");

});
