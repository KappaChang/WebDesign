
$(document).ready(function() {
    var nav;
    var dataObject = {
        action: 'checkLogin',
    };
    // sendAjax(dataObject, function(data) {
    //  if (data.data.error) {
    //      nav = '<a href="#myModal1" data-toggle="modal"><div class="login">登入</div></a>';
    //      $('#MAIN').delegate('#sign-up', 'click', function(event) {
    //          event.preventDefault();
    //          alert('請先登入');
    //          $('html, body').animate({
    //              scrollTop: 0
    //          }, 600);

    //      });
    //  } else {
    //      nav = '<div class="logout"><span class="user-image"><img src="'+ data.data.data.head_photo +'"></span><span class="user-name">'+data.data.data.nickname+'</span><span class="user-logout"><a href="logout.php">登出</a></span></div>';
    //  }
    //  $('.nav').append(nav);
    // })

    //登入
    $('.forgetBtn').click(function() {
        $('#myModal1').modal('hide')
        $('#myModal2').modal('show')
        $('#myModal3').modal('hide')
    });
    $('.loginBtn, .haveAccountBtn').click(function() {
        $('#myModal1').modal('show')
        $('#myModal2').modal('hide')
        $('#myModal3').modal('hide')
    });
    $('.newUserBtn').click(function() {
        $('#myModal1').modal('hide')
        $('#myModal2').modal('hide')
        $('#myModal3').modal('show')
    });

    $('#login').on('click', function(event) {
        event.preventDefault();
        var loginTable = $('#login-table');
        var account = loginTable.find('[name=account]').val();
        var password = loginTable.find('[name=password]').val();

        var dataObject = {
            action: 'login',
            account: account,
            password:password
        };
        sendAjax(dataObject, function(data){
            if(data.error){
                alert(data.message);
            } else {
                location.reload();
            }
        }, '/SONY01/login')
    });

    $('.fb-login-btn').click(function(event) {
        FacebookLogin();
    });

    $('#register').click(function(event) {
        event.preventDefault();
        var registerTable = $('#register-table');
        var account = registerTable.find('[name=account]').val();
        var password = registerTable.find('[name=password]').val();
        var password_confirm = registerTable.find('[name=password_confirm]').val();
        if (account === '') {
            alert('帳號沒輸入');
        }
        if (password === '' || password_confirm === '' || password !== password_confirm) {
            alert('密碼有誤');
        }
        var dataObject = {
            action: 'register',
            account: account,
            password: password,
            password_confirm: password_confirm
        };
        sendAjax(dataObject, function(data) {
            if (data.error) {
                alert(data.message);
            } else {
                location.href = '';
            }
        }, '/SONY01/register');
    });
    $('#forgot').click(function(event) {
        event.preventDefault();
        var forgotTable = $('#forgor-table');
        var account = forgotTable.find('[name=account]').val();
        var password = forgotTable.find('[name=password]').val();
        var password_confirm = forgotTable.find('[name=password_confirm]').val();

        var dataObject = {
            action: 'forgot',
            account: account,
            password: password,
            password_confirm: password_confirm
        };
        sendAjax(dataObject, function(data) {
            alert(data.message);
            if (!data.error) {
                $('#myModal2').modal('hide')
            }
        }, '/SONY01/forget');
    });
});

function sendAjax(obj, handleData, url) {
    console.log(obj)
    var result="";
    jQuery.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: obj,
        success: function(data, textStatus, xhr) {
            handleData(data);
        }
    });
}
