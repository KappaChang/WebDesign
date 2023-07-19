var loginFlag = true;
var cashPage = null;
var timer = null;
var response = null;
var startTime = new Date('2014/03/20 10:40:00');
//var startTime = new Date('2014/03/19 20:18:00');
var leftoffTime = new Date('2014/04/18 15:00:00');


var nowTime = new Date();

$(document).ready(function(){
    if(nowTime < startTime) {
        $('.pre-button').text("　");
        $('.pre-button').addClass('notStart-button').removeClass('pre-button');
    }
    if(nowTime > leftoffTime) {
        $('.pre-button').text('活動時間已截止');
        $('.pre-button').addClass('expired-button').removeClass('pre-button');
    }

    //checkStartTime();

    //Check pre-buy count
    checkPreBuyCount();


    $('#myModal5').modal('show'); 


    //FB Login
    $('.fb-login-btn').click(function(e) {
        FacebookLogin(function(s) {
            forwardToCashflow(s);
        }); 
    });

    $('.expired-button').click(function() {
        //Check event time
        if(nowTime > leftoffTime) {
           alert("預購動已截止囉，請於4/20至活動現場購買瘋古典福袋");
        }       
    });

    $('.notStart-button').click(function() {
        if(nowTime < startTime) {
            alert("預購活動時間 103年3月20日 12:00 準時開始!");
        }
    });

    //Normal Login
    $('#login').click(function(e) {
       normalLogin();        
    });
    //Normal Register
    $('#register').click(function(e) {
       normalRegister(); 
    });

    $('#forgot').click(function(e) {
        forgot();
    });

    //Pre-order
    $('.pre-button').click(function() {
        checkLoginStatus(forwardToCashflow);
    });

    //Pre-order
    $('.text-pre-button').click(function() {
        $('#myModal5').modal('hide');
        checkLoginStatus(forwardToCashflow);
    });

    //Refund
    $('.refund-button').click(function() {
        var citizen_id = $('#citizen_id').val();
        var bank_code = $('#bank_code').val();
        var bank_subcode = $('#bank_subcode').val();
        var bank_account = $('#bank_account').val();
        var account_name = $('#account_name').val();

        if(!citizen_id || citizen_id == ''){
            alert("請輸入身份證字號!");
        }else if(citizen_id && !checkID(citizen_id)){
        }else if(!bank_code || bank_code == ''){
            alert("請輸入銀行代碼!");
        }else if(!bank_subcode || bank_subcode == ''){
            alert("請輸入分行代碼!");
        }else if(!bank_account || bank_account ==''){
            alert("請輸入銀行帳號!");
        }else if(!account_name || account_name ==''){
            alert("請輸入帳戶戶名!");
        }else {
            refund(citizen_id, bank_code, bank_subcode, bank_account, account_name);    
        }
        
    });

    $('.limited-button').click(function() {
        alert('Sorry！預購份數已搶購完了，請於 4/20（日）活動當天至現場購買唷！');
    });
    //登入
    $('.forgetBtn').click(function() {
        $('#myModal1').modal('hide');
        $('#myModal2').modal('show');
        $('#myModal3').modal('hide');
    });
    $('.loginBtn, .haveAccountBtn').click(function() {
        $('#myModal1').modal('show');
        $('#myModal2').modal('hide');
        $('#myModal3').modal('hide');
    });
    $('.newUserBtn').click(function() {
        $('#myModal1').modal('hide');
        $('#myModal2').modal('hide');
        $('#myModal3').modal('show');
    });

    $("#paymentLink").click(function() {
        $('#myModal4').modal('hide');
    });

});

var forwardToCashflow = function(result) {
    if(result){
        $('#myModal1').modal('hide'); 
        $('#myModal2').modal('hide'); 
        $('#myModal3').modal('hide'); 
        $('#myModal4').modal('show');
    }else{
        $('#myModal1').modal('show');
    }
};

