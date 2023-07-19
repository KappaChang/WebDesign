<?php
include __DIR__.'/include.php';

$t1 = microtime();

if (!file_exists(PRIZE_PATH)) {
    mkdir(PRIZE_PATH, 0770);
}
if (!file_exists(LOCK_UNUSED_FILE)) {
    touch(LOCK_UNUSED_FILE);
}

if (!file_exists(LOCK_USED_FILE)) {
    touch(LOCK_USED_FILE);
}

if (!file_exists(DREW_FILE)) {
    touch(DREW_FILE);
}

if (!file_exists(USED_FOLDER)) {
    mkdir(USED_FOLDER, 0770);
}

if (!file_exists(UNUSED_FOLDER)) {
    mkdir(UNUSED_FOLDER, 0770);
}

if (!file_exists(LOG_FOLDER)) {
    mkdir(LOG_FOLDER, 0770);
}

if (!file_exists(LOG_DRAW_FILE)) {
    touch(LOG_DRAW_FILE);
}

if (!file_exists(LOG_SIGN_FILE)) {
    touch(LOG_SIGN_FILE);
}


$fp = fopen(LOCK_UNUSED_FILE, "r+");
if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
    $num = 1;
    foreach ($prize_list as $key => $prize) {
        if ($prize['virtual'] === false) {
            for ($i=0; $i < $prize['count']; $i++) {
                $prize_file = UNUSED_FOLDER.str_pad($num, 2, '0', STR_PAD_LEFT).'-'.$key;
                if (!touch($prize_file)) {
                    echo 'Create prize ' . $prize_file . ' failed';
                }
                $num++;
            }
        }
    }
    flock($fp, LOCK_UN);    // release the lock
} else {
    echo "Couldn't get the lock!";
}
fclose($fp);
echo microtime() - $t1 . "\n"; // time spent on picking prize
