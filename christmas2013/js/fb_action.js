// window.fbAsyncInit = function() {
//   FB.init({
//     appId: '339999192774080', // App ID
//     // channelUrl: 'http://www.muzik-online.com/channel.html',
//     status: true, // check login status
//     cookie: true, // enable cookies to allow the server to access the session
//     xfbml: true // parse XFBML
//   });

//   // Additional initialization code here
// };

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


function FeedAction(group) {

  FB.api('/me', function(response) {
    var name = '享受典藏 耶誕跨年古典樂 | MUZIK ONLINE';
    var caption = location.protocol + '//' + location.host + location.pathname;
    var picture = location.protocol + '//' + location.host + '/images/fb_share-500.jpg';
    var description = '今年耶誕節準備好與 MUZIK ONLINE 一起同樂了嗎？我們準備多項精緻的禮物想與大家分享歡樂。只需動動你的手指，即可參加 MUZIK ONLINE 耶誕贈獎活動唷！';

    console.log(caption);

    FB.ui({
      method: 'feed',
      link: location.protocol + '//' + location.host + location.pathname,
      name: name,
      picture: picture,
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
        })

    })
}
