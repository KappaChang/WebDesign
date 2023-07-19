<?php
include __DIR__.'/include.php';

$member_uuid = $_GET['member_uuid'];

$token = $_GET['token'];

$hmac = getMemberHmac($member_uuid);

$new = date('Y-m-d H:i:s');

if ($new > date('Y-m-d H:i:s', strtotime('2014-12-25')) && $new < date('Y-m-d H:i:s', strtotime('2014-12-26'))) {
    if ($hmac === $token) {
        $cookie->setCookie(COOKIE_NAME_FOR_MEMBER_UUID, $member_uuid, '', null, '/', '.muzik-online.com');
        header("Location: ../award.html");
        exit();
    }
}

header("Location: ../");
exit();
