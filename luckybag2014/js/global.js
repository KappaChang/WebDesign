var sendAjax = function(obj, handleData, async){
	jQuery.ajax({
		url: 'lib/action.php',
		type: 'POST',
		dataType: 'json',
		async: async,
		data: obj,
		success: function(data){
		    handleData(data);
		}
    });
};

//refund
var refund = function(citizenId, bankCode, bankSubCode, bankAccount, accountName){

    var dataObj = {
        action: 'refund',
        citizen_id: citizenId,
        bank_code: bankCode,
        bank_subcode: bankSubCode,
        bank_account: bankAccount,
        account_name: accountName
    };
    sendAjax(dataObj, function(data){
        alert(data.data.result_msg);
    });  
};

var checkPreBuyCount = function(){
    var dataObj = {action: 'chkPreBuyCount'};

    sendAjax(dataObj, function(data) {
        if(data.data.error == 0){
            if(data.data.count > 1000){
                $('.pre-button').unbind("click");
                $('.pre-button').text('Sorry！預購份數已搶購完了，請於3/29（六）活動當天至現場購買唷！');
                $('.pre-button').removeClass('pre-button').addClass('limited-button');

            }
        }
    })
};

var checkLoginStatus = function(cb){
    var dataObj = { action: 'checkLogin'};

    sendAjax(dataObj, function(data){
       if(data.data.error == 0){
           cb(true);
       } else {
           cb(false);
       }
    }, false);
};

var normalLogin = function(cb){
    var loginTable = $("#login-table");
    var account = loginTable.find("#account").val();
    var password = loginTable.find("#password").val();
    
    if(account == ''){
        alert("請輸入帳號!");
    }else if(password == ''){
        alert("請輸入密碼!");
    }else{
        var obj = {
            action: 'login',
            account: account,
            password: password
        };

        sendAjax(obj, function(data){
            if(data.error == 0){
                forwardToCashflow(true);
            }else{
                alert(data.message);
            }
        });
    }
}

var normalRegister = function(cb){
    var regTable = $("#register-table");
    var account = regTable.find("#account").val();
    var password = regTable.find("#password").val();
    var password_confirm = regTable.find("#password_confirm").val();

    if(account == ''){
        alert("請輸入註冊帳號!");
    }else if((password == '' || password_confirm == '') || (password != password_confirm)){
        alert("密碼輸入錯誤!");
    }else{
        var obj = {
            action: 'register',
            account: account,
            password: password,
            password_confirm: password_confirm
        };

        sendAjax(obj, function(data){
        //    alert("register return = " + JSON.stringify(data));
            if(data.error == 0){
                forwardToCashflow(true);
            }else{
                alert(data.message);
            }
        });
    }
}

var forgot = function () {
    var forgotTable = $('#forgot-table');
    var account = forgotTable.find('#account').val();
    var password = forgotTable.find('#password').val();
    var password_confirm = forgotTable.find('#password_confirm').val();
    
    if(account == ''){
        alert("請輸入帳號!");
    } else if((password == '' || password_confirm == '') || (password != password_confirm)) {
        alert("新密碼輸入錯誤!");
    } else {
        var obj = {
            action : 'forgot',
            account: account,
            password: password,
            password_confirm: password_confirm
        };

        sendAjax(obj, function(data) {
//            alert("forgot return = " + JSON.stringify(data));
            alert(data.message);
            $('#myModal2').modal('hide');
        });
    }
}

var checkServerTime = function() {
    var obj = {
        action: 'checkServerTime'
    };

    sendAjax(obj, function(data) {
        nowTime = new Date(data.data.serverDate);
    }, false);
};
/*
var checkPayment = function() {
    var obj = {
        action: 'checkPayment'
    }
    sendAjax(obj, function(data) {
        //alert('CheckPayment return = ' + JSON.stringify(data));
        if(data.error == 0){
            if(data.data.error == 0){
                alert("恭喜您 預購成功!!\n請記得於 3/50(八) 25:00 於 ___________,\n攜帶身份證至現場領取_________");
            }else {
                alert("請確認是否完成預購流程!! [" + data.data.msg + "]");
            }
        }
    },false);
}
*/
var checkStartTime = function() {
    timer = setInterval(function(){
        if(nowTime < startTime) {
            $('.notStart-button').countdown({
                until: startTime,
                layout: '距離活動開始時間還有:{hnn}小時{mnn}分{sn}秒',
                onExpiry: function() {
                      $('.notStart-button').unbind('click');
                      $('.notStart-button').addClass('pre-button').removeClass('notStart-button');
                      $('.pre-button').text("福袋$999，我要預購");

                      //Pre-order
                      $('.pre-button').click(function() {
                          checkLoginStatus(forwardToCashflow);
                      });
                }
            });
            $('.notStart-button').countdown($.countdown.regional['zh-TW']);
        }  
    }, 1000);
};


var checkID = function(idStr) {
     // 依照字母的編號排列，存入陣列備用。
      var letters = new Array('A', 'B', 'C', 'D',
          'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
          'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
          'W', 'X', 'Y', 'Z');
      // 儲存各個乘數
      var lettervalue = new Array(10, 11, 12, 13, 14, 15, 16, 17,
                               34, 18, 19, 20, 21, 22, 35, 23, 
                               24, 25, 26, 27, 28, 29, 32, 30,
                               31, 33);
      var mulitply = new Array (0, 8, 7, 6, 5, 4, 3, 2, 1, 0);
      var nums = new Array(2);
      var firstChar;
      var firstNum;
      var lastNum;
      var lvalue;
      var total = 0;
      // 撰寫「正規表達式」。第一個字為英文字母，
      // 第二個字為1或2，後面跟著8個數字，不分大小寫。
      var regExpID=/^[A-Z](1|2)\d{8}$/i;
      // 使用「正規表達式」檢驗格式
      if (idStr.search(regExpID)==-1) {
        // 基本格式錯誤
            alert("身份證號碼填寫格式錯誤！");
            return false;
      } else {
            // 取出第一個字元和最後一個數字。
            firstChar = idStr.charAt(0).toUpperCase();
            lastNum = idStr.charAt(9);
      }

      // 找出第一個字母對應的數字，並轉換成兩位數數字。
      for (var i=0; i<26; i++) {
            if (firstChar == letters[i]) {
                firstNum = lettervalue[i].valueOf();
                firstNum = Math.floor(firstNum/10) + (firstNum%10*9);
              break;
            }
      }
      // 計算1~8位數的數值
      for(var n=1; n<=8; n++){
        total = total + idStr.charAt(n).valueOf() * mulitply[n].valueOf();
      }

      // 加上英文字的數值
      total = total + firstNum;

      //檢查是否與第10位相同
      if((total % 10).valueOf() != ((10 - idStr.charAt(9).valueOf()) % 10)) {
                alert("身份證號碼填寫錯誤！");
                return false;
      }
      return true;
};


