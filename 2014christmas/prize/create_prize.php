<?php

$t1 = microtime();

$fp = fopen("lock_unused", "r+");
if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock

    $i = 0;
    $prize_num = 80;
    while ($i++ < $prize_num) {
        if (!touch("unused/" . str_pad($i, 2, '0', STR_PAD_LEFT))) {
            echo 'Create prize ' . $i . ' failed';
        }
    }
    flock($fp, LOCK_UN);    // release the lock
} else {
    echo "Couldn't get the lock!";
}

fclose($fp);

echo microtime() - $t1 . "\n"; // time spent on picking prize
