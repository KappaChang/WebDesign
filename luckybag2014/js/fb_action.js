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
  js.async = false;
  js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1";
  ref.parentNode.insertBefore(js, ref);
}(document));


var user_name, user_fbid;

function FacebookShare(group) {
  FB.login(function(response) {
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
    var name = '「瘋古典春夏祭」音樂會';
    var caption = location.protocol + '//' + location.host + location.pathname;
    var picture = location.protocol + '//' + location.host + '/images/fb-share3.png';
    var description = "誠摯希望各位帶上您最親愛的家人朋友，一同在音樂遊樂場裡享受優美療癒的音符帶來的心靈感受與共鳴。「瘋古典春夏祭」音樂會由希幔數位等贊助商贊助，當日無須入場費，現場活動贊助攤位可參與福袋抽汽車活動。即日起於活動官網參加預購福袋的朋友可再加碼抽PS4。";

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
/*
function FacebookLogin() {
  FB.getLoginStatus(function(response) {
    if (response.status === "connected") {
      return register(response.authResponse.signedRequest);
    } else {
      FB.login(function(response) {
        if (response.authResponse){
            return register(response.authResponse.signedRequest);
        }else{
          console.log("授權失敗");
        }
      }, {
        scope: 'publish_stream,email,user_about_me,user_birthday,user_website,user_location'
      });
    }
  });
}
*/
function FacebookLogin(callbackFn) {
  FB.getLoginStatus(function(response) {
    if (response.status === "connected") {
  //    return register(response.authResponse.signedRequest);
        FB.api('/me', function(data) {
            var r =  register(response.authResponse.signedRequest, data);
            console.log("res = " + r);
            callbackFn(r);
            //return r;
        });
    } else {
      FB.login(function(response) {
        if (response.authResponse){
            FB.api('/me', function(data) {
                var r =  register(response.authResponse.signedRequest, data);
                console.log("res = " + r);
                callbackFn(r);
              //  return r;
            });
        }else{
          console.log("授權失敗");
        }
      }, {
        scope: 'publish_stream,email,user_about_me,user_birthday,user_website,user_location'
      });
    }
  });
  console.log("l 5");
}
function register(signedRequest, data) {
        var registerResult;

        var dataObject = {
            action: 'facebookLogin',
            signedRequest: signedRequest,
            name: data.name,
            email: data.email,
        };
        sendAjax(dataObject, function(data) {
            if (data.error) {
                alert(data.message);
                registerResult = false;
                console.log("-- B --");
            } else {
//                forwardToCashflow(true);
                registerResult = true;
                console.log("-- A --");
            }
        }, false)
        console.log("-- C --");
        return registerResult;
}
/*
function register(signedRequest) {
    FB.api('/me', function(data) {
        result = false;
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
                forwardToCashflow(true);
            }
        })
    })
}
*/
var forwardToCashflow_fb = function(result) {
    if(result){
        alert("登入成功,將開啟付款頁面 FB"); 
        $("#linkPayment").click();
        
        $('#myModal1').modal('hide'); 
        $('#myModal2').modal('hide'); 
        $('#myModal3').modal('hide'); 
    }else{
        $('#myModal1').modal('show');
    }
};


