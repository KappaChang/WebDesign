window.fbAsyncInit = function() {
  FB.init({
    appId: '339999192774080', // App ID
    // channelUrl: 'http://www.muzik-online.com/channel.html',
    status: true, // check login status
    cookie: true, // enable cookies to allow the server to access the session
    xfbml: true // parse XFBML
  });

  // Additional initialization code here
};

// Load the SDK Asynchronously
(function(d) {
  var js, id = 'facebook-jssdk',
    ref = d.getElementsByTagName('script')[0];
  if (d.getElementById(id)) {
    return;
  }
  js = d.createElement('script');
  js.id = id;
  js.async = true;
  js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1";
  ref.parentNode.insertBefore(js, ref);
}(document));


var user_name, user_fbid;

function FacebookShare(group) {
  FB.login(function(response) {
    // console.log(response.authResponse);
    if (response.authResponse) {
      access_token = response.authResponse.accessToken; //get access token
      user_fbid = response.authResponse.userID; //get FB UID
      FeedAction(group)
    } else {
      console.log('User cancelled login or did not fully authorize.');
    }
  }, {
    scope: 'publish_stream'
  });
}


function FacebookLogin() {
  FB.getLoginStatus(function(response) {
    if (response.status === "connected") {
      register(response.authResponse.signedRequest)
    } else {
      FB.login(function(response) {
        if (response.authResponse)
          register(response.authResponse.signedRequest)
        else
          console.log("授權失敗");
      }, {
        scope: 'publish_stream,email,user_about_me,user_birthday,user_website,user_location'
      });
    }
  });
}

function register(signedRequest) {
    FB.api('/me', function(data) {
        console.log(data);
        var dataObject = {
            action: 'facebookLogin',
            signedRequest: signedRequest,
            name: data.name,
            email: data.email,
        };
        sendAjax(dataObject, function(data) {
            if (data.error) {
                alert(data.message);
            } else {
                location.href = '';
            }
        }, '/SONY01/login')
    })
}


function FeedAction(group) {

  FB.api('/me', function(response) {

    switch (group) {

      case 'group0':
        var name = 'MUZIK ONLINE - 體驗高解析音質 Sony大獎等你拿';
        var caption = location.protocol + '//' + location.host + location.pathname;
        var description = '你對耳機的產品了解有多少？不管是業餘還是專家的您不可錯過的Sony耳機體驗會！我們邀請前《音響論壇》雜誌主編劉名振先生及《台北爵士夜》廣播節目DJ沈鴻元先生來為大家分享了解Sony Hi-Res Audio高解析音質如何帶給你如臨現場的感動，與2014年所發表的一系列頂級規格產品！現場也將開放自由體驗產品及美味下午茶茶點無限享用。於體驗會後回來發表體驗心得就有機會獲得當天體驗耳機大獎喔！';
        break;
    };



    FB.ui({
      method: 'feed',
      link: location.protocol + '//' + location.host + location.pathname,
      name: name,
      picture: location.protocol + '//' + location.host + location.pathname + 'images/share.jpg?a=20141124',
      //picture: 'http://dev.muzik-online.com:8082/event/MUZIK86/images/fb_share.jpg',
      caption: caption,
      description: description,
    }, function(response) {

      if (response && response.post_id) {


      } else {
        alert('Post was not published.');
      }
    });
  });

}
