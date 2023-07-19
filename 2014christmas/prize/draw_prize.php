<?php

$t1 = microtime();

$fp = fopen("lock_unused", "r+");
if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
    $dir = 'unused';  // pick prize from dir
    $unused = array_diff(scandir($dir), array('.', '..')); // remove current and parent dir
    // there are prized to be picked
    if (count($unused)) {
        $pick = array_rand($unused); // random and pick a prize
        $prize = $unused[$pick]; // get prize file name
        rename('unused/'.$prize, 'used/'.$prize); // move file to unsed
    }

    flock($fp, LOCK_UN);    // release the lock
} else {
    echo "Couldn't get the lock!";
}

fclose($fp);

echo microtime() - $t1 . "\n"; // time spent on picking prize
