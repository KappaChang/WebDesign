<?php
// session_set_cookie_params(0, ' '.muzik-online.com');
session_start();

// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
// ini_set('display_startup_errors', TRUE);
include __DIR__.'/global.php';

$action = '';
$data = array();
foreach ($_POST as $key => $value) {
    $$key = filterXSS($value);
}

// $action = 'login';
// $account = 'gospeller';
// $password = '69870722';

switch ($action) {
    case 'checkLogin':
        checkLogin();
        break;
    case 'login':
        login($account, $password);
        break;
    case 'getParticipant':
        getParticipant();
        break;
    case 'facebookLogin':
        facebookLogin($signedRequest, $name, $email);
        break;
    case 'register':
        register($account, $password, $password_confirm);
        break;
    case 'signUp':
        signUp();
        break;
    case 'forgot':
        send($account, $password, $password_confirm);
        break;
    case 'payment':
        payment($member_uuid, $result);
        break;
    case 'checkServerTime':
        chkDate();
        break;
    case 'checkPayment':
        checkPayment();
        break;
    case 'chkPreBuyCount':
        checkPreBuyCount();
        break;
    case 'refund':
        refund($citizen_id, $bank_code, $bank_account, $account_name);
        break;
}
responseJSON(1, 'Internal Error!'.$action, $data);

function checkLogin(){
    $isLogin = isLogin();
    if (!$isLogin) {
        $data['error'] = 1;
        $data['data'] = '未登入';
    } else {
        $data['error'] = 0;
        $data['data'] = '登入成功';
        //$host_name = 'http://www.muzik-online.com';
        //$member = $isLogin;
        //if (strpos($member['head_photo'] ,'https://graph.facebook.com') !== false) {
        //    $head_photo = $member['head_photo'];
        //} else {
        //    $member['head_photo'] = $host_name . $member['head_photo'];

        //}
        //$data['data'] = '<div class="logout">
        //        <span class="user-image"><img src="'. $member['head_photo'].'"></span>
        //        <span class="user-name">'.$member['nickname'].'</span>
        //        <span class="user-logout"><a href="logout.php">登出</a></span>
        //    </div>';
        //$data['data'] = $member;
    }
    responseJson(0, '', $data);

}

function filterXSS($str) {
    return filter_var(strip_tags($str), FILTER_SANITIZE_STRING);
}

function login($account, $password)
{
    $rs = ORM::for_table('member')
        ->join('category', array('member.category_id', '=', 'category.category_id'))
        ->join('member_role', array('member.member_uuid', '=', 'member_role.member_uuid'))
        ->join('role', array('member_role.role_id', '=', 'role.role_id'))
        ->select_many('member.member_id','member.member_uuid','member.password','member.old_password','member.access_token','member.nickname','member.head_photo','member.coupon_fb','member.coupon_samsung', 'member.fbid')
        ->select_many('category.category_name')
        ->select_many('member_role.end_date_at')
        ->select_many('role.role_name')
        ->where('member.login_account',$account)
        ->where('member.fbid','')
        ->where('member.locked', 0)
        ->where('member.deleted_by', '')
        ->find_one();
    if (!$rs) {
        responseJson(1,'查無此會員', '');
        exit();
    }
    $hash = hash_hmac('sha256', $password, $account);

    if ($hash !== $rs->password){
        responseJson(1,'密碼錯誤', '');
        exit();
    }




    setSession($rs);
    responseJson(0, '登入成功', '');
}

function getParticipant()
{
    $host_name = 'https://www.muzik-online.com';

    $rs = ORM::for_table('playlist')
        ->join('member', array('member.member_uuid', '=', 'playlist.member_uuid'))
        ->join('playlist_track', array('playlist.playlist_uuid', '=', 'playlist_track.playlist_uuid'))
        ->select_many('member.member_uuid', 'member.nickname', 'member.fbid', 'member.head_photo')
        ->select_many('playlist.playlist_title')
        ->select_expr('COUNT(playlist_track.id)', 'track_count')
        ->where_like('playlist.playlist_title','%耶誕典藏家%')
        ->where('playlist.deleted_by', '')
        ->where('playlist_track.deleted_by', '')
        ->group_by('member.member_uuid')
        ->find_array();
    foreach ($rs as $key => $value) {
        if ($value['track_count'] < 5) {
            unset($rs[$key]);
            continue;
        }
        if ($value['fbid'] === '') {
            if(file_exists(getcwd().'/../../files/member/thumbs/' . $value['head_photo'] )){
                $rs[$key]['head_photo'] = $host_name . '/files/member/thumbs/'. $value['head_photo'];
            }else{
                $rs[$key]['head_photo'] = $host_name . '/files/member/thumbs/default.png';
            }
        } else {
            $rs[$key]['head_photo'] = 'https://graph.facebook.com/'. $value['fbid'] .'/picture?width=140&height=140';
        }
        $rs[$key]['href'] = $host_name . '/listen/member/' . $value['member_uuid'];
    }

    $rs2 = ORM::for_table('member_subscribe')
        ->join('member', array('member.member_uuid', '=', 'member_subscribe.member_uuid'))
        ->select_many('member.member_uuid', 'member.nickname', 'member.fbid', 'member.head_photo')
        ->where('member_subscribe.deleted_by', '')
        ->where('member_subscribe.subscribe_uuid', '3e1ad682-e53c-b793-087e-df2498305b0c')
        ->group_by('member.member_uuid')
        ->find_array();
    foreach ($rs2 as $key => $value) {
        if ($value['fbid'] === '') {
            if(file_exists(getcwd().'/../../files/member/thumbs/' . $value['head_photo'] )){
                $rs2[$key]['head_photo'] = $host_name . '/files/member/thumbs/'. $value['head_photo'];
            }else{
                $rs2[$key]['head_photo'] = $host_name . '/files/member/thumbs/default.png';
            }
        } else {
            $rs2[$key]['head_photo'] = 'https://graph.facebook.com/'. $value['fbid'] .'/picture?width=140&height=140';
        }
        $rs2[$key]['href'] = $host_name . '/listen/member/' . $value['member_uuid'];
    }
    $data['collect'] = $rs;
    $data['microsoft'] = $rs2;
    responseJson(0, '', $data);

}

function facebookLogin($signedRequest, $name, $email){

        $fb_extend_month = 1;
        $samsung_extend_month = 2;
        $extend_date = 0;

        list($encoded_sig, $payload) = explode('.', $signedRequest, 2);

        $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
        $request_data = json_decode(base64_decode(strtr($payload, '-_', '+/')), false);

        $expected_sig = hash_hmac('sha256', $payload, FACEBOOK_APP_SECRET, true);

        if($sig != $expected_sig ){
            responseJSON(1, '驗證失敗', '');
            exit();
        }
        $fbid = $request_data->user_id;

        $rs = ORM::for_table('member')
            ->join('category', array('member.category_id', '=', 'category.category_id'))
            ->join('member_role', array('member.member_uuid', '=', 'member_role.member_uuid'))
            ->join('role', array('member_role.role_id', '=', 'role.role_id'))
            ->select_many('member.member_id','member.member_uuid','member.password','member.old_password','member.access_token','member.nickname','member.head_photo','member.coupon_fb','member.coupon_samsung', 'member.fbid')
            ->select_many('category.category_name')
            ->select_many('member_role.end_date_at')
            ->select_many('role.role_name')
            ->where('member.fbid',$fbid)
            ->where('member.locked', 0)
            ->where('member.deleted_by', '')
            ->find_one();

    if ($rs) {
        ORM::get_db()->beginTransaction();

        $rs_role_update = false;
        $rs_role = ORM::for_table('member_role')
            ->select_many('id','role_id','end_date_at')
            ->where('member_uuid', $rs->member_uuid)
            ->find_one();

        if (!$rs_role) {
            ORM::get_db()->rollBack();
            responseJson(1, '未預期錯誤', '');
        }

        if ($rs->coupon_fb == '') {
            $extend_date = $extend_date + $fb_extend_month;
            $rs_role_update = true;
            $rs->coupon_fb = 'FB登入/註冊免費試用方案';

            $rs2 = ORM::for_table('transaction')->create();
            $rs2->transaction_uuid = \J20\Uuid\Uuid::v4();
            $rs2->member_uuid = $rs->member_uuid;
            $rs2->created_by = $rs->member_uuid;
            $rs2->username = $rs->nickname;
            $rs2->type = 0;
            $rs2->method = 2;
            $rs2->gateway = '99';
            $rs2->created_at = date('Y-m-d H:i:s');
            if(!$rs2->save()){
                ORM::get_db()->rollBack();
                responseJson(1, '未預期錯誤', '');
            }

            if ($rs_role->role_id === '2' || $rs_role->role_id === '4') {
                $rs_role->role_id = '4';
            }
        }

        if ($os_type === 3 && $rs->coupon_samsung == '') {
            $extend_date = $extend_date + $samsung_extend_month;
            $rs_role_update = true;
            $rs->coupon_samsung = 'Samsung';

            $rs2 = ORM::for_table('transaction')->create();
            $rs2->transaction_uuid = \J20\Uuid\Uuid::v4();
            $rs2->member_uuid = $rs->member_uuid;
            $rs2->created_by = $rs->member_uuid;
            $rs2->username = $rs->nickname;
            $rs2->type = 0;
            $rs2->method = 2;
            $rs2->gateway = '98';
            $rs2->created_at = date('Y-m-d H:i:s');
            if(!$rs2->save()){
                ORM::get_db()->rollBack();
                responseJson(1, '未預期錯誤', '');
            }

            if ($rs_role->role_id === '2' || $rs_role->role_id === '4') {
                $rs_role->role_id = '4';
            }
        }

        $role = 'member';
        $expired_date = '';
        if ($rs_role->role_id === '1') {
            $role = 'paying_member';
            $expired_date = '2038-01-19 11:14:07';
        }
        else if ($rs_role->role_id === '4' && $rs_role->end_date_at > date('Y-m-d H:i:s')) {
            $role = 'paying_member';
            $expired_date = $rs_role->end_date_at;
        }

        $rs->login_account = $email;
        $rs->mail = $email;
        $rs->nickname = $name;
        $rs->os_type = $os_type;
        if ($birthday !== '') {
            $rs->birthday = $birthday;
        }
        if ($gender >= 0) {
            $rs->gender = $gender;
        }
        $rs->access_token = md5(uniqid('',true));
        $rs->last_login_at = current_time();

        if (!$rs->save()) {
            ORM::get_db()->rollBack();
                responseJson(1, '未預期錯誤', '');
        }
        if ($rs_role_update) {
            if (isset($rs_role->end_date_at) && $rs_role->end_date_at < '2038-00-19 11:14:07') {
                $rs_role->end_date_at = date( "Y-m-d H:i:s", strtotime( " +$extend_date month" , strtotime($rs_role->end_date_at)));
            }
            else {
                $rs_role->end_date_at = date( "Y-m-d H:i:s", strtotime( " +$extend_date month"));
            }
            if (!$rs_role->save()) {
                ORM::get_db()->rollBack();
                    responseJson(1, '未預期錯誤', '');
            }
            $expired_date = $rs_role->end_date_at;
        }

        ORM::get_db()->commit();

        setSession($rs);

        responseJson(0, '成功' );
    }
    else {
        ORM::get_db()->beginTransaction();

        $rs = ORM::for_table('member')->create();
        $rs->member_uuid = \J20\Uuid\Uuid::v4();
        $rs->login_account = $email;
        $rs->mail = $email;
        // $rs->password = hash_hmac('sha256', $password, $account);
        // $rs->access_token = md5(uniqid('',true));
        $rs->nickname = $name;
    //     if ($birthday !== '') {
    //         $rs->birthday = $birthday;
    //     }
    //     if ($gender >= 0) {
    //         $rs->gender = $gender;
    //     }
        $rs->data_from = 'christmas2013';
        $rs->created_at = current_time();
        $rs->fbid = $fbid;
        $rs->last_login_at = current_time();
        $rs->coupon_fb = 'FB登入/註冊免費試用方案';

        $rs_role = ORM::for_table('member_role')->create();
        $rs_role->member_uuid = $rs->member_uuid;
        $rs_role->role_id = '4';
        $extend_date = $extend_date + $fb_extend_month;
        $rs_role->end_date_at = date( "Y-m-d H:i:s", strtotime( " +" . $extend_date ." month" , strtotime(current_time())));

        // $rs2 = ORM::for_table('transaction')->create();
        // $rs2->transaction_uuid = \J20\Uuid\Uuid::v4();
        // $rs2->member_uuid = $rs->member_uuid;
        // $rs2->created_by = $rs->member_uuid;
        // $rs2->username = $nickname;
        // $rs2->type = 0;
        // $rs2->method = 2;
        // $rs2->gateway = '99';
        // $rs2->created_at = date('Y-m-d H:i:s');
        // if(!$rs2->save()){
        //     ORM::get_db()->rollBack();
        //         responseJson(1, '未預期錯誤', '');
        // }

    //     if ($os_type === 3) {
    //         $extend_date = $extend_date + $samsung_extend_month;
    //         $rs->coupon_samsung = 'Samsung';

    //         $rs2 = ORM::for_table('transaction')->create();
    //         $rs2->transaction_uuid = \J20\Uuid\Uuid::v4();
    //         $rs2->member_uuid = $rs->member_uuid;
    //         $rs2->created_by = $rs->member_uuid;
    //         $rs2->username = $nickname;
    //         $rs2->type = 0;
    //         $rs2->method = 2;
    //         $rs2->gateway = '98';
    //         $rs2->created_at = date('Y-m-d H:i:s');
    //         if(!$rs2->save()){
    //             ORM::get_db()->rollBack();
    //                 responseJson(1, '未預期錯誤', '');
    //         }

    //         if ($rs_role->role_id === '2' || $rs_role->role_id === '4') {
    //             $rs_role->role_id = '4';
    //         }
    //     }

        if (!$rs->save()) {
            ORM::get_db()->rollBack();
                responseJson(1, '未預期錯誤', '');
        }

    //     if (isset($rs_role->end_date_at) && $rs_role->end_date_at < '2038-00-19 11:14:07') {
    //         // $log->info("aaa");
    //         // $log->info("extend_date = $extend_date");
    //         // $log->info("rs_role->end_date_at = $rs_role->end_date_at");
    //         $rs_role->end_date_at = date( "Y-m-d H:i:s", strtotime( " +$extend_date month" , strtotime($rs_role->end_date_at)));
    //         // $log->info("rs_role->end_date_at = $rs_role->end_date_at");
    //     }
    //     else {
    //         // $log->info("bbb");
    //         // $log->info("extend_date = $extend_date");
    //         // $log->info("rs_role->end_date_at = $rs_role->end_date_at");
    //         $rs_role->end_date_at = date( "Y-m-d H:i:s", strtotime( " +$extend_date month"));
    //         // $log->info("rs_role->end_date_at = $rs_role->end_date_at");
    //     }
        if (!$rs_role->save()) {
            ORM::get_db()->rollBack();
                responseJson(1, '未預期錯誤', '');
        }

    //     $role = 'paying_member';

        ORM::get_db()->commit();
        setSession($rs);

    //     $expired_date = $rs_role->end_date_at;
        responseJson(0, '註冊步驟');
    }


}

function register($account, $password, $password_confirm){
    $account = filter_var($account, FILTER_VALIDATE_EMAIL);
    if (!$account) {
        responseJson(1, '帳號格式不正確');
        exit();
    }
    if (!$password || !$password_confirm || $password !== $password_confirm) {
        responseJson(1, '密碼有誤');
        exit();
    }

    $rs = ORM::for_table('member')
        // ->join('category', array('member.category_id', '=', 'category.category_id'))
        // ->join('member_role', array('member.member_uuid', '=', 'member_role.member_uuid'))
        // ->join('role', array('member_role.role_id', '=', 'role.role_id'))
        ->select_many('member.member_id','member.member_uuid','member.password','member.old_password','member.access_token','member.nickname','member.head_photo','member.coupon_fb','member.coupon_samsung', 'member.fbid')
        // ->select_many('category.category_name')
        // ->select_many('member_role.end_date_at')
        // ->select_many('role.role_name')
        ->where('member.login_account',$account)
        ->where('member.fbid','')
        ->where('member.locked', 0)
        ->where('member.deleted_by', '')
        ->find_one();
    if ($rs) {
        responseJson(1, '帳號已存在' );
    }

    ORM::get_db()->beginTransaction();

    $rs = ORM::for_table('member')->create();
    $rs->member_uuid = \J20\Uuid\Uuid::v4();
    $rs->login_account = $account;
    $rs->password = hash_hmac('sha256', $password, $account);
    $rs->nickname = explode('@', $account)[0];
    $rs->data_from = 'christmas2013';
    $rs->save();
    if (!$rs) {
        ORM::get_db()->rollBack();
        responseJson(1, '未預期錯誤' );
    }

    $rs2 = ORM::for_table('member_role')->create();
    $rs2->member_uuid = $rs->member_uuid;
    $rs2->role_id = 2;
    $rs2->save();
    if (!$rs2) {
        ORM::get_db()->rollBack();
        responseJson(1, '未預期錯誤' );
    }

    ORM::get_db()->commit();
    setSession($rs);
    responseJson(0, '成功');

}

function signUp(){
    $isLogin = isLogin();
    if (!$isLogin) {
        responseJson(1, '尚未登入' , '');
    } else {
        responseJson(0, '已登入' , $isLogin);
    }
}

function send($account, $password, $password_confirm){

    if ($password != $password_confirm) {
        responseJson(1, '密碼不符合', '');
    }

    $rs = ORM::for_table('member')
        ->join('category', array('member.category_id', '=', 'category.category_id'))
        ->join('member_role', array('member.member_uuid', '=', 'member_role.member_uuid'))
        ->join('role', array('member_role.role_id', '=', 'role.role_id'))
        ->select_many('member.login_account','member.member_id','member.member_uuid','member.password','member.old_password','member.access_token','member.nickname','member.head_photo','member.coupon_fb','member.coupon_samsung', 'member.fbid', 'member.mail')
        ->select_many('category.category_name')
        ->select_many('member_role.end_date_at')
        ->select_many('role.role_name')
        ->where('member.login_account',$account)
        ->where('member.fbid','')
        ->where('member.locked', 0)
        ->where('member.deleted_by', '')
        ->find_one();
    if (!$rs) {
        responseJson(1, '查無此會員' );
    }


    if (!filter_var($rs->mail, FILTER_VALIDATE_EMAIL)) {
        if (!filter_var($rs->login_account, FILTER_VALIDATE_EMAIL)) {
            responseJson(1, '未預期錯誤' , $rs->mail);
        }
        $email_to = $rs->login_account;
    }
    else {
        $email_to = $rs->mail;
    }

    $expired_time = time() + EMAIL_TOKEN_EXPIRED_SECONDS;
    $data = $rs->member_uuid.'|||'.$rs->login_account.'|||'.$password.'|||'.$expired_time;
    $encrypted_data = openssl_encrypt($data, 'AES-256-CFB', SYSTEM_KEY, false, SYSTEM_KEY_IV16);

    $email_body = "請確認您於 Muzik-Online.com 網站上要求重新設定您的密碼。\n";
    $email_body = $email_body . "點選下列連結將會修改您的密碼。\n";
    $email_body = $email_body . 'https://www.muzik-online.com/member/verify?token='. $encrypted_data;

    $email_subject = '[Muzik-Online] Reset your password';
    $email_reply_to = array('muzikonline.tw@gmail.com' => 'Muzik-Online Service');

    $data = array(EMAIL_TOKEN_EXPIRED_SECONDS, SYSTEM_KEY, SYSTEM_KEY_IV16, $password, $password_confirm);
    
    list($result, $error) = sendmail($email_subject, $email_to, $email_body, $email_reply_to);
 
    if ($error) {
        responseJson(1, '未預期錯誤');
    }else{

    responseJson(0, '系統將寄認證信至您所設定的電子信箱，請確認後並點選所附的網路連結:'.$email_to);
    }
}
/*
function payment($member_uuid, $result){
    ORM::get_db()->beginTransaction();

    $rs = ORM::for_table('event')->create();
    $rs->event_name="luckybag2014";
    $rs->member_uuid = $member_uuid;
    $rs->dummy = '0';
    $rs->save();

    if(!$rs){
        ORM::get_db()->rollback();
        responseJson(1, "未預期的錯誤");
    }
    
    ORM::get_db()->commit();

    $data['result'] = "0000";

    responseJson(0, $data);
}
*/
function checkPayment(){
   $member_uuid = $_SESSION['user']['member_uuid'];
//   $member_uuid = "test member uuid";
   $rs = ORM::for_table('event')
        ->where('event.member_uuid', $member_uuid)
        ->where('event.event_name','luckybag2014')
        ->find_one();        

    if(!$rs){
        $data['error'] = 1;
        $data['msg'] = "查無付款資料";
    }elseif($rs->dummy == "0"){
        $data['error'] = 0;
        $data['msg'] = "已付款";
    }
    responseJson(0, $rs->dummy, $data);
}

function checkPreBuyCount(){
    $rs = ORM::for_table('event')
        ->where('event.event_name', 'luckybag2014')
        ->count();
    if(!$rs) {
        $data['error'] = 1;
        $data['count'] = "未預期錯誤";
    }else {
        $data['error'] = 0;
        $data['count'] = $rs;
    }
    responseJson(0, '', $data);
}

function chkDate() {
    $data['error'] = 0;
    $data['serverDate'] = date("Y/m/d H:i:s");
    responseJson(0, '', $data);
}

function refund($citizen_id, $bank_code, $bank_account, $account_name) {
    $url = "http://payment.muzik-online.com/event/muzik999refund";
   // $url = "http://paganini.muzik-online.com:9102/event/muzik999refund";

    $data = 'citizen_id='.$citizen_id.'&bank_code='.$bank_code.'&bank_account='.$bank_account.'&account_name='.$account_name;

    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $curl = curl_exec($ch);

    $result = json_decode($curl, TRUE);

    responseJson(0, '', $result);
}
