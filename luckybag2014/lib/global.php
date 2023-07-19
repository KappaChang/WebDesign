<?php
    date_default_timezone_set('Asia/Taipei');
	//Facebook
	define('FACEBOOK_APP_ID', '339999192774080');
	define('FACEBOOK_APP_SECRET', '542dd7c0d4047c2e64c35d38fc3ecc58');
    
	//System
	define('SYSTEM_KEY', '34kjvfse$#Ggjw4e$@#gjwkedaql3j4!@$vgsdFSEFR');
	define('SYSTEM_KEY_IV16', '$#%fwtj245SDF3F1');
    
    //Email
    define('EMAIL_HOST', 'mail.muzik-online.com');
    define('EMAIL_PORT', 587);
    define('EMAIL_USER', 'crm@muzik-online.com');
    define('EMAIL_USER_NICKNAME', 'Muzik-Online Service');
    define('EMAIL_PASS', '381654729');
    define('EMAIL_TOKEN_EXPIRED_SECONDS', '900');
    define('EMAIL_TOKEN_VERIFY_URL', '/member/verify?token=');

    require_once(__DIR__.'/FirePHPCore/fb.php');
	require_once(__DIR__.'/swiftmailer/lib/swift_required.php');

	include __DIR__.'/../../global_function.php';
	include __DIR__.'/../../config/config.php';
	include __DIR__.'/sendmail.php';

?>
