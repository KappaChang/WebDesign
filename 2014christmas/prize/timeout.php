<?php

define("TIMEOUT", 30);

$t1 = microtime();
$dir = "used";
$fp = fopen("lock_used", "r+");
if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
    $used = array_diff(scandir($dir), array('.', '..')); // remove current and parent dir
    // there are prized to be picked
    if (count($used)) {
        foreach ($used as $key => $val) {
            $run_file = 'used/'.$val;
            if (file_exists($run_file)
                // add additional condition here to test if prize is filled in with user info!!!
                && (round(microtime(true)) - filectime($run_file)) > TIMEOUT) {
                rename('used/'.$val, 'unused/'.$val); // move file to unsed
            }
        }
    }
    flock($fp, LOCK_UN);    // release the lock
} else {
    echo "Couldn't get the lock!";
}

fclose($fp);

echo microtime() - $t1 . "\n"; // time spent on picking prize
