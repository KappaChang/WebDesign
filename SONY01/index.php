<?php
define('APP_PATH', __DIR__);
define('EVENT_NAME', 'SONY01');
define('EVENT_UPLOAD_IMAGE_URL', 'http://www.muzik-online.com/files/event/'.EVENT_NAME.'/');

/*========================================
=            Facebook og meta            =
========================================*/
define("OG_SITE_NAME", 'MUZIK ONLINE - 體驗高解析音質 Sony大獎等你拿');
define("OG_TITLE", 'MUZIK ONLINE - 體驗高解析音質 Sony大獎等你拿');
define("OG_DESCRIPTION", '你對耳機的產品了解有多少？不管是業餘還是專家的您不可錯過的Sony耳機體驗會！我們邀請前《音響論壇》雜誌主編劉名振先生及《台北爵士夜》廣播節目DJ沈鴻元先生來為大家分享了解Sony Hi-Res Audio高解析音質如何帶給你如臨現場的感動，與2014年所發表的一系列頂級規格產品！現場也將開放自由體驗產品及美味下午茶茶點無限享用。於體驗會後回來發表體驗心得就有機會獲得當天體驗耳機大獎喔！');
define('OG_URL', 'http://event.muzik-online.com/SONY01/');
define('OG_IMAGE', OG_URL.'images/share.jpg');
/*-----  End of Facebook og meta  ------*/


require '../../../app/config/global.php';
require '../config/service.php';

$loader = require '../vendor/autoload.php';

$loader->add('App', realpath(__DIR__));

// set view
$loader = new Twig_Loader_Filesystem('App/View');

$twig = new Twig_Environment($loader);

Event\Simple\Service::register('view', $twig);

//set router
$app = new \Slim\Slim(array('debug' => false));

Event\Simple\Service::register('app', $app);

//GET
$app->get('/', 'App\Controllers\IndexController:indexAction');

$app->get('/vip', 'App\Controllers\IndexController:vipAction');

$app->get('/experience', 'App\Controllers\IndexController:experienceAction');

// $app->get('/experience/:path+', 'App\Controllers\IndexController:readMoreAction');

$app->get('/experience/:path', function ($path) {
    $r = new App\Controllers\IndexController();
    $r->readMoreAction($path);
});


$app->get('/experience/:path/edit', function ($path) {
    $r = new App\Controllers\IndexController();
    $r->editPageAction($path);
});

$app->get('/winner', 'App\Controllers\IndexController:winnerAction');

$app->get('/sing-up', 'App\Controllers\IndexController:singupAction');

$app->get('/logout', 'App\Controllers\IndexController:logoutAction');

$app->get('/status', 'App\Controllers\IndexController:statusAction');

//POST
$app->post('/experience', 'App\Controllers\IndexController:createExperienceAction');

$app->post('/sing-up', 'App\Controllers\IndexController:singupAction');

$app->post('/upload', 'App\Controllers\IndexController:uploadAction');

$app->post('/login', 'App\Controllers\IndexController:loginAction');

$app->post('/forget', 'App\Controllers\IndexController:forgetAction');

$app->post('/register', 'App\Controllers\IndexController:registerAction');

$app->post('/experience/:path/edit', function ($path) {
    $r = new App\Controllers\IndexController();
    $r->editAction($path);
});


$app->run();
