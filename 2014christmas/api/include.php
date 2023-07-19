<?php
include __DIR__.'/../../../../app/config/global.php';

define('PRIZE_PATH', __DIR__.'/prize/');
define('LOCK_UNUSED_FILE', PRIZE_PATH.'lock_unused');
define('LOCK_USED_FILE', PRIZE_PATH.'lock_used');
define('DREW_FILE', PRIZE_PATH.'drew');
define('USED_FOLDER', PRIZE_PATH.'used/');
define('UNUSED_FOLDER', PRIZE_PATH.'unused/');
define('LOG_FOLDER', PRIZE_PATH.'log/');
define('LOG_DRAW_FILE', LOG_FOLDER.'draw.log');
define('LOG_SIGN_FILE', LOG_FOLDER.'sign.log');
define("TIMEOUT", 330);

$prize_list = include 'prize_list.php';

$member_uuid = $cookie->getCookie(COOKIE_NAME_FOR_MEMBER_UUID);

function check_end_datetime()
{
    if (date("Y-m-d H:i:s") > date('Y-m-d H:i:s', strtotime('2014-12-26'))) {
        $response = array(
                'error' => true,
                'message' => '抽獎時間已過',
                );
        response($response);
    }
}

function check_member_uuid($member_uuid)
{
    if ($member_uuid == '' || !isUuid($member_uuid)) {
        $response = array(
                'error' => true,
                'message' => '未預期錯誤',
                );
        response($response);
    }
}

function response($data)
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function analyze_prize_file($prize_file)
{
    $is_match = preg_match('/(?<id>[0-9]{2})-(?<number>[0-9]{2})-(?<member_uuid>[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12})-(?<status>.*)/i', $prize_file, $matches);
    if ($is_match) {
        $prize = array(
            'id' => $matches['id'],
            'number' => $matches['number'],
            'member_uuid' => $matches['member_uuid'],
            'status' => $matches['status'],
            );
        return $prize;
    }
    return false;
}

function count_unused_prize()
{
    $dir = UNUSED_FOLDER;
    $unused = array_diff(scandir($dir), array('.', '..')); // remove current and parent dir
    return count($unused);
}

function log_draw($member_uuid, $info_array)
{
    $fp = fopen(LOG_DRAW_FILE, 'a');
    $log = array_merge(array($member_uuid, date('Y-m-d H:i:s')), $info_array);
    fwrite($fp, implode("\t", $log)."\n");
    fclose($fp);
}

function log_sign($member_uuid, $sing_info)
{
    $fp = fopen(LOG_SIGN_FILE, 'a');
    $log = array_merge(array($member_uuid, date('Y-m-d H:i:s')), $sing_info);
    fwrite($fp, implode("\t", $log)."\n");
    fclose($fp);
}
