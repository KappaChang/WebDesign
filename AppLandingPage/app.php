<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if(strpos($userAgent,"iPhone") || strpos($userAgent,"iPod")){

echo "<script>window.location.href='https://itunes.apple.com/tw/app/muzik-online/id683134818?mt=8'</script>";

}else if(strpos($userAgent,"Android")){

echo "<script>window.location.href='https://play.google.com/store/apps/details?id=com.muzik&hl=zh-TW'</script>";

}else if(strpos($userAgent,"Windows Phone")){

echo "<script>window.location.href='http://www.windowsphone.com/zh-tw/store/app/muzik-radio/55771854-0fa7-4b50-8a66-4ab623b13c2e'</script>";

}
else {
echo "<script>alert('敬請期待')</script>";
echo "<script>window.location.href='https://www.muzik-online.com/app/index.html'</script>";
}