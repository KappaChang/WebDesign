  window.fbAsyncInit = function() {
      FB.init({
          appId: '339999192774080',
          channelUrl: 'http://www.muzik-online.com/channel.html',
          oauth: true,
          status: true,
          // check login status
          cookie: true,
          // enable cookies to allow the server to access the session
          xfbml: true // parse XFBML
      });
      //建立Like Button 點擊呈現 link 的eventhandle
      FB.Event.subscribe('edge.create', function(response) {
          //將傳回來的資訊顯示在 div id="result"內

// console.log(response);
          // is_fan(function() {
          //         if(result == true) {
          //             // console.log()
          //             // alert("您已經加入MUZIK ONLINE粉絲團，請到步驟三。");
          //             $('.step2').hide();
          //             $('.step3').show();

          //         }
          //     });

          // if(result != false) {
          //     // $('.step2').hide();
          //     // $('.step3').show();
          // }

      });
      //建立Like Button 點擊呈現 unlike 的eventhandle
      FB.Event.subscribe('edge.remove', function(response) {
// console.log(response);

          // // $('#tab2 h4').fadeOut('fast', function() {
          // //     $(this).html('請按讚加入粉絲團').fadeIn('fast');
          // // })
          // is_fan();
          // if(result != false) {
          //     // $('.step2').hide();
          //     // $('.step3').show();
          // }
      });
  };



  var user_name, user_fbid;

  function fb_login() {
      FB.login(function(response) {
          // console.log(response.authResponse);
          if(response.authResponse) {
              $('.step1').hide();
              $('.step2').show();
              $('select').val([""]);
              // $('.step2 h4').after("<img src=\"http://event.muzik-online.com/LesMiserables/images/loading10.gif\" id=\"loading\"\>");

              // $('.fb-like-box').hide();


              // $('.tabs li').removeClass('active').eq(1).addClass('active');
              // $('#tab1').fadeOut(function() {
              //     $('#tab2').fadeIn(function() {
              //         FB.XFBML.parse();
              //     });

              // });
              access_token = response.authResponse.accessToken; //get access token
              user_id = response.authResponse.userID; //get FB UID
              FB.api('/me', function(response) {
                  // console.log(response);
                  user_email = response.email; //get user email
                  user_name = response.name;
                  user_fbid = response.id;
                  // you can store this data into your database
              });
              AjaxResponse();


          } else {
              //user hit cancel button
              console.log('User cancelled login or did not fully authorize.');

          }
      }, {
          scope: 'publish_stream,email,user_about_me,user_birthday,user_website,user_location'
      });
  }

  function AjaxResponse() {
      var myData = 'connect=1'; //For demo, we will pass a post variable, Check process_facebook.php
      // console.log('v25');
      jQuery.ajax({
          type: "POST",
          url: "process_facebook.php",
          dataType: 'json',
          data: myData,
          success: function(response) {

          },
          error: function(xhr, ajaxOptions, thrownError) {
              // console.log(xhr);
              // console.log(ajaxOptions);
              // console.log(thrownError);
              alert('fb response error : 000x5');
              //$("#results").html('<fieldset style="padding:20px;color:red;">'+thrownError+'</fieldset>'); //Error
          }
      });
  }

  function is_fan(callback) {
    // console.log("loading");
    // $('.fb-like-box').hide();
    // $('.step2 h4').after("<img src=\"http://event.muzik-online.com/LesMiserables/images/loading10.gif\" id=\"loading\"\>");

      FB.api({
          method: 'pages.isFan',
          page_id: '418096944902924' //
      }, function(response) {
          // console.log(response);
          // $('#loading').remove();
          // $('.fb-like-box').show();
          result = response;
          if(response) {
              // console.log(response);
              if(typeof(callback) == 'function'){
                callback();
              }

          } else {

          }
        // return response;
      });


  }

  function FacebookShare() {
      FB.ui({
          method: 'feed',
          display: 'popup',
          link: 'http://event.muzik-online.com/LesMiserables/index.html',
          name: 'MUZIK ONLINE 《悲慘世界》 網路留言贈原著',
          picture: 'http://www.muzik-online.com/event/LesMiserables/images/fb_share_image.jpg',
          caption: 'MUZIK ONLINE 《悲慘世界》 網路留言贈原著',
          description: '喜歡《悲慘世界》電影版的朋友們，快來MUZIK ONLINE告訴大家「你最喜歡哪一位角色和最喜歡哪一首音樂」，只要在2月8號到22號完成留言，你就有機會獲得《悲慘世界》原著小說乙套！'
      }, function(response) {
          if(response && response.post_id) {
              // alert('Post was published.');
          } else {
              // alert('Post was not published.');
          }
      });
  }

  function requestCallback(response) {
      console.log(response);
  }

  function comment() {
      // console.log(user_name);
      var role = $('#role').val();
      var music = $('#music').val();
      if(user_name == undefined) {
          alert('您尚未授權登入');

          $('#tab2').fadeOut(function() {
              $('#tab1').fadeIn();
          });

      } else {
          if(role == "" || music == "") {
              alert('您尚未作出選擇!');
          } else {


                  var comment = '我最喜歡《悲慘世界》電影版的「' + role + '」和電影版中的「' + music + '」這首音樂。';
                  // console.log(comment);
                  // console.log(user_name);
                  jQuery.ajax({
                      url: 'ajax_comment.php',
                      type: 'POST',
                      dataType: 'json',
                      data: {
                          action: 'comment',
                          comment: comment,
                          user_name: user_name
                      },
                      complete: function(xhr, textStatus) {
                          //called when complete
                          // console.log(textStatus);
                      },
                      success: function(data, textStatus, xhr) {
                          //called when successful
                          // console.log(textStatus);
                          // console.log(data);
                          if(data.error == 1) {
                            alert(data.msg);
                            $('.step2').fadeOut(100);
                            $('.step3').fadeIn(100);

                          } else if(data.error == 2){
                            alert(data.msg);
                          }else if(data.error == 3){
                            alert(data.msg);

                          }
                          else{
                              alert(data.msg);
                                $('.step2').hide();
                                $('.step3').show();
                              var d = new Date();
                              var curr_date = d.getDate();
                              var curr_month = d.getMonth() + 1;
                              var curr_year = d.getFullYear();
                              var curr_hour = d.getHours();
                              var curr_min = d.getMinutes();
                              var curr_sec = d.getSeconds();

                              var comment_datetime = curr_year + '-' + curr_month + '-' + curr_date + ' ' + curr_hour + ':' + curr_min + ':' + curr_sec;
                              // console.log(comment_datetime);

                              $('#comments').prepend($('<li><i class="arrow"></i><div class="message_text"><div class="main"><div class="avatar"><img src="https://graph.facebook.com/' + user_fbid + '/picture?width=50&amp;height=50"></div><div class="header"><div class="name"><a href="">' + user_name + '</a></div><div class="time">' + comment_datetime + '</div></div><div class="text">我最喜歡《悲慘世界》電影版的「' + role + '」和電影版中的「' + music + '」這首音樂。</div></div></div></li>').fadeIn('fast', function() {
                                  $('.tabs li').removeClass('active');
                                  $('.tabs li:eq(3)').addClass('active');
                                  $('#tab3').fadeOut('fast', function() {
                                      $('#tab4').fadeIn('fast');
                                  });

                              }));
                              $('.mCSB_dragger:last').resize()


                          }
                      },
                      error: function(xhr, textStatus, errorThrown) {
                          //called when there is an error
                          console.log(textStatus);
                      }
                  });

          }
      }

  }

$(document).ready(function() {
  $.each($('.winner_with_people'),function(index, el){
    var fbid = $(this).attr('fbid');
      FB.api('/'+$(this).attr('fbid')+'?fields=name', function(response) {
        $('.winner_with_people').eq(index).find('.name').html('<div class="name"><a href="http://www.facebook.com/'+fbid+'" target="_blank">'+response.name+'</a></div>');
      });
  });
});