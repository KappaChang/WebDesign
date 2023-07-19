<?php
include __DIR__.'/include.php';

check_member_uuid($member_uuid);

$name = '';
$email = '';
$phone = '';
$address = '';

foreach ($_POST as $key => $value) {
    $$key = $value;
}

// $name = 'test';
// $email = 'test@mail.com.tw';
// $phone = '123456789';
// $address = '123';

// name 姓名
// email 信箱
// phone 電話
// address 地址

if ($name == '') {
    $response = array(
        'error' => true,
        'message' => '姓名未填寫！',
        );
    response($response);
}
if ($email == '') {
    $response = array(
        'error' => true,
        'message' => '信箱未填寫！',
        );
    response($response);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = array(
        'error' => true,
        'message' => '信箱格式錯誤',
        );
    response($response);
}
if ($phone == '') {
    $response = array(
        'error' => true,
        'message' => '電話未填寫！',
        );
    response($response);
}
if ($address == '') {
    $response = array(
        'error' => true,
        'message' => '地址未填寫！',
        );
    response($response);
}

$prize_file = reset(glob(USED_FOLDER.'*-'.$member_uuid.'*'));
if ($prize_file === false) {
    $response = array(
        'error' => true,
        'message' => '沒抽到實體獎品',
        );
    response($response);
}

$prize = analyze_prize_file($prize_file);
if ($prize === false) {
    $response = array(
        'error' => true,
        'message' => '未預期錯誤',
        );
    response($response);
}

if ($prize['status'] == 'done') {
    $response = array(
        'error' => true,
        'message' => '領獎資料已填',
        );
    response($response);
}

$sing_info = array(
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'address' => $address,
    );
$fp = fopen($prize_file, 'w');
$rs = fwrite($fp, implode("\n", $sing_info));
if ($rs === false) {
    $response = array(
        'error' => true,
        'message' => '未預期錯誤',
        );
    response($response);
}
fclose($fp);


$prize['status'] = 'done';
$new_prize_file = USED_FOLDER .implode('-', $prize);
rename($prize_file, $new_prize_file);
$response = array(
    'error' => false,
    );
log_sign($member_uuid, $sing_info);
response($response);
