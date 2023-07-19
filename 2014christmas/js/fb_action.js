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


// var user_name, user_fbid;

// function FacebookShare(group) {
//   FB.login(function(response) {
//     // console.log(response.authResponse);
//     if (response.authResponse) {
//       access_token = response.authResponse.accessToken; //get access token
//       user_fbid = response.authResponse.userID; //get FB UID
//       FeedAction(group)
//     } else {
//       console.log('User cancelled login or did not fully authorize.');
//     }
//   }, {
//     scope: 'publish_stream'
//   });
// }


// function FacebookLogin() {
//   FB.getLoginStatus(function(response) {
//     if (response.status === "connected") {
//       register(response.authResponse.signedRequest)
//     } else {
//       FB.login(function(response) {
//         if (response.authResponse)
//           register(response.authResponse.signedRequest)
//         else
//           console.log("授權失敗");
//       }, {
//         scope: 'publish_stream,email,user_about_me,user_birthday,user_website,user_location'
//       });
//     }
//   });
// }

// function register(signedRequest) {
//     FB.api('/me', function(data) {
//         console.log(data);
//         var dataObject = {
//             action: 'facebookLogin',
//             signedRequest: signedRequest,
//             name: data.name,
//             email: data.email,
//         };
//         sendAjax(dataObject, function(data) {
//             if (data.error) {
//                 alert(data.message);
//             } else {
//                 location.href = '';
//             }
//         }, '/SONY01/login')
//     })
// }


function FeedAction(group) {

  FB.api('/me', function(response) {

    switch (group) {

      case 'group0':
        var name = '2014 hoomia x MUZIK ONLINE 幸福耶誕。襪藏驚喜';
        var caption = location.protocol + '//' + location.host + location.pathname;
        var description = '歡樂的耶誕節即將來臨，hoomia 與 MUZIK ONLINE 聯手將於 12/25 當天免費送出耶誕禮物喔！不管你是用 MUZIK ONLINE App 還是 Web，只要在耶誕節登入，我們將免費贈送多樣好禮。不用擔心會抽到銘謝惠顧，因為我們保證人人有獎！';
        break;
    };



    FB.ui({
      method: 'feed',
      link: location.protocol + '//' + location.host + location.pathname,
      name: name,
      picture: location.protocol + '//' + location.host + location.pathname + 'images/share.png',
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
