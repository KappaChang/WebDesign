<?php
include __DIR__.'/include.php';

check_end_datetime();

check_member_uuid($member_uuid);

$check = check_drew($member_uuid);

if ($check) {
    $response['error'] = true;
    response($response);
}

write_drew($member_uuid);

if (!checkTaiwanIp(get_client_ip())) {
    $prize = virtual_draw($prize_list, $member_uuid);
} else {
    if (count_unused_prize() == 0) {
        $prize = virtual_draw($prize_list, $member_uuid);
    } else {
        $i = 0;
        $prize = false;

        $r = array_merge(array_fill(0, 19, 'virtual'), array_fill(0, 1, 'reality'));
        $rand_key = array_rand($r);
        if ($r[$rand_key] === 'virtual') {
            $prize = virtual_draw($prize_list, $member_uuid);
        } else {
            do {
                $prize = draw($member_uuid);
                $i ++ ;
            } while ($prize === false && $i < 3);
        }
    }
}

if ($prize === false) {
    $prize = virtual_draw($prize_list, $member_uuid);
}

$response = array(
    'error' => false,
    'data' => array(
        'number' => $prize['number'],
        'name' => $prize_list[$prize['number']]['name']
        )
    );
log_draw($member_uuid, $response['data']);
response($response);

function check_drew($member_uuid)
{
    $rs = file(DREW_FILE, FILE_IGNORE_NEW_LINES);
    if (in_array($member_uuid, $rs)) {
        return true;
    }
}

function write_drew($member_uuid)
{
    $drew = fopen(DREW_FILE, 'a');
    do {
        $rs = fwrite($drew, $member_uuid."\n");
    } while ($rs === false);
    fclose($drew);

}


function draw($member_uuid)
{
    $prize = false;
    $fp = fopen(LOCK_UNUSED_FILE, "r+");
    if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
        $dir = UNUSED_FOLDER;  // pick prize from dir
        $unused = array_diff(scandir($dir), array('.', '..')); // remove current and parent dir
        // there are prized to be picked
        if (count($unused)) {
            $pick = array_rand($unused); // random and pick a prize
            $prize = $unused[$pick]; // get prize file name
            $new_prize_file = $prize.'-'.$member_uuid .'-wait';
            rename(UNUSED_FOLDER.'/'.$prize, USED_FOLDER.'/'.$new_prize_file); // move file to unsed
            $prize = analyze_prize_file($new_prize_file);
        }
        flock($fp, LOCK_UN);    // release the lock
    }
    fclose($fp);
    return $prize;
}

function virtual_draw($prize_list, $member_uuid)
{
    $virtual_prize_list = array_filter($prize_list, function ($prize) {
        return $prize['virtual'];
    });
    $fill_prize = array();
    foreach ($virtual_prize_list as $key => $value) {
        $fill_prize = array_merge($fill_prize, array_fill(0, $value['count'], $key));
    }
    $rand = array_rand($fill_prize);
    $number = $fill_prize[$rand];
    switch ($number) {
        case '06':
            // 增加365天權限
            extendMemberDeadline($member_uuid, 1, '+1 year');
            break;
        case '07':
            // 增加90天權限
            extendMemberDeadline($member_uuid, 1, '+3 month');
            break;
        case '08':
            extendMemberDeadline($member_uuid, 1, '+1 month');
            // 增加30天權限
            break;
        case '09':
            extendMemberDeadline($member_uuid, 1, '+7 days');
            // 增加7天權限
            break;
    }
    $prize = array(
        'number' => (string) $number
        );
    return $prize;
}
