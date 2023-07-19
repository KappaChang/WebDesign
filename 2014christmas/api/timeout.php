<?php

include __DIR__.'/include.php';

$dir = USED_FOLDER;
$fp = fopen(LOCK_USED_FILE, "r+");
if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
    $used = array_diff(scandir($dir), array('.', '..')); // remove current and parent dir
    // there are prized to be picked
    if (count($used)) {
        foreach ($used as $key => $val) {
            $prize_file = $val;
            $run_file = USED_FOLDER.$val;
            $rs = analyze_prize_file($run_file);
            if ($rs['status'] === 'wait') {
                if (file_exists($run_file)
                    // add additional condition here to test if prize is filled in with user info!!!
                    && (round(microtime(true)) - filectime($run_file)) > TIMEOUT) {
                    rename(USED_FOLDER.$val, UNUSED_FOLDER.$rs['id'].'-'.$rs['number']); // move file to unsed
                }
            }
        }
    }
    flock($fp, LOCK_UN);    // release the lock
} else {
    echo "Couldn't get the lock!";
}
fclose($fp);
