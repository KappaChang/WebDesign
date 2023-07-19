<?php
namespace App\Controllers;

use Event\Simple\Service as Service;
use Intervention\Image\ImageManagerStatic as Image;
use Swift_SmtpTransport          as Swift_SmtpTransport;
use Swift_Mailer          as Swift_Mailer;
use Swift_Message          as Swift_Message;
use FB;
use ORM;

class IndexController
{
    // login user
    public $user = null;

    public $event_name = "SONY01";

    // view variables array
    public $data = array();

    public function __construct()
    {
        $base_uri = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']));

        $this->user = self::getUserInfo(getMemberUuid());

        $open_graph = array(
            'site_name'     => OG_SITE_NAME,
            'title'         => OG_TITLE,
            'description'   => OG_DESCRIPTION,
            'url'           => OG_URL,
            'image'         => OG_IMAGE,
            );

        $this->data['user'] = $this->user;

        $this->data['base_uri'] = $base_uri;

        $this->data['site_url'] = $this->siteURL();

        $this->data['open_graph'] = $open_graph;

        // $this->data['aa'] = $this->app->request->getUrl();
    }

    public function __get($name)
    {
        # NOTE: This example only use service object
        return Service::get($name);
    }

    public function indexAction()
    {
        $this->data['action'] = 'index';
        echo $this->view->render('index.twig', $this->data);
    }

    public function vipAction()
    {
        $vip = array();
        $publish_time = date('Y-m-d H:i:s', strtotime('2014-12-17'));
        if (date('Y-m-d H:i:s') >= $publish_time) {
            $vip = ORM::for_table('event')
                ->select_many('event.member_uuid', 'member.nickname', 'member.fbid')
                ->join('member', array('event.member_uuid', '=', 'member.member_uuid'))
                ->where('event.event_name', EVENT_NAME)
                ->where('event.check', 1)
                ->find_array();
            foreach ($vip as $key => $value) {
                $head_photo = '';
                if ($value['fbid'] !== '') {
                    $head_photo = 'https://graph.facebook.com/'.$value['fbid'].'/picture?width=200&height=200';
                } else {
                    $head_photo = getHeadPhoto($value['member_uuid'], 200);
                    $head_photo = ROOT_URL.$head_photo;
                }
                $vip[$key]['head_photo'] = $head_photo;
            }
        }
        $this->data['vip'] = $vip;
        echo $this->view->render('vip.twig', $this->data);
    }


    public function singupAction()
    {
        $already = false;
        $ending = false;
        if ($this->app->request->isPost() == true) {
            $required_field = array('name', 'mail', 'phone', 'address');
            $errorMsg = array();
            $name = $this->app->request->post('name');
            $mail = $this->app->request->post('mail');
            $phone = $this->app->request->post('phone');
            $address = $this->app->request->post('address');
            foreach ($required_field as $key => $value) {
                if (${$value} !== '') {
                    unset($required_field[$key]);
                } else {
                    switch ($value) {
                        case 'name':
                            $errorMsg[] = '姓名欄位必填';
                            break;
                        case 'mail':
                            $errorMsg[] = 'E-mail欄位必填';
                            break;
                        case 'phone':
                            $errorMsg[] = '手機欄位必填';
                            break;
                        case 'address':
                            $errorMsg[] = '地址欄位必填';
                            break;
                    }
                }

            }
            if (!in_array('mail', $required_field) && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $errorMsg[] = 'E-mail格式錯誤';
                $required_field = $required_field + array('mail');
            }
            if (count($errorMsg) > 0) {
                $response = array(
                    'error' => true,
                    'message' => implode("\n", $errorMsg),
                    'required_field' => array_values($required_field)
                    );
                $this->responseJSON($response);
            }
            $content = $this->app->request->post('content');
            $event = ORM::for_table('event')->create();
            $event->event_name = EVENT_NAME;
            $event->member_uuid = $this->data['user']['member_uuid'];
            $event->name = $name;
            $event->phone = $phone;
            $event->dummy = trim(htmlspecialchars(preg_replace('/\s\s+/', ' ', $content)));
            $event->address = $address;
            $event->mail = $mail;
            if (!$event->save()) {
                $response['error'] = true;
                $response = array(
                    'error' => true,
                    'message' => '未預期錯誤'
                    );
            } else {
                $response = array(
                    'error' => false,
                    'message' => '報名成功'
                    );
            }

            $this->responseJSON($response);
        } else {
            $closing_time = date('Y-m-d H:i:s', strtotime('2014-12-17'));
            if (date('Y-m-d H:i:s') >= $closing_time) {
                $ending = true;
            } else if (getMemberUuid()) {
                $rs = ORM::for_table('event')
                    ->select_many('event.event_id')
                    ->where('event.event_name', EVENT_NAME)
                    ->where('event.member_uuid', getMemberUuid())
                    ->find_one();
                if ($rs) {
                    $already = true;
                }
            }
            $this->data['already'] = $already;
            $this->data['ending'] = $ending;
            echo $this->view->render('sing-up.twig', $this->data);
        }
    }

    public function experienceAction()
    {
        $experience = false;
        $now = date('Y-m-d H:i:s');
        $start_time = date('Y-m-d H:i:s', strtotime('2014-12-20 13:30:00'));
        $end_time = date('Y-m-d H:i:s', strtotime('2015-01-04'));
        if ($now >= $start_time && $now < $end_time) {
            if (getMemberUuid() === false) {
                $experience = false;
            } else {
                $rs = ORM::for_table('event')
                    ->select_many('event_id')
                    ->where('event.member_uuid', getMemberUuid())
                    ->where('event.event_name', EVENT_NAME)
                    ->where('event.check', 1)
                    ->find_one();
                if ($rs) {
                    $rs_content = ORM::for_table('event')
                        ->select_many('event_id')
                        ->where('event.member_uuid', getMemberUuid())
                        ->where('event.event_name', EVENT_NAME."_POST")
                        ->find_one();
                    if (!$rs_content) {
                        $experience = true;
                    }
                }
            }
        }

        $experiences = ORM::for_table('event')
            ->select_many('event.title', 'event.content', 'event.name', 'event.event_id', 'event.dummy', 'event.created_at')
            ->select('member.nickname', 'name')
            ->join('member', array('member.member_uuid', '=', 'event.member_uuid'))
            ->where('event.event_name', EVENT_NAME."_POST")
            ->order_by_desc('event.event_id')
            ->find_many();
        foreach ($experiences as $key => $value) {
            // $value->content = strip_tags(html_entity_decode($value->content, ENT_QUOTES, 'UTF-8'));
            $value->content = mb_substr(strip_tags(html_entity_decode(html_entity_decode($value->content, ENT_QUOTES, 'UTF-8'))), 0, 500, 'UTF-8');
            // exit();
            $value->created_at = date('Y-m-d', strtotime($value->created_at));

        }
        $this->data['experience'] = $experience;
        $this->data['experiences'] = $experiences;
        echo $this->view->render('experience.twig', $this->data);
            // echo $this->view->render('experience_complete.twig', $this->data);
    }

    public function createExperienceAction()
    {
        $title = $this->app->request->post('title');
        $content = $this->app->request->post('content');

        $required_field = array();
        $errorMsg = array();
        if ($title == '') {
            $errorMsg[] = '標題欄位必填';
            $required_field[] = 'title';
        }
        if ($content == '') {
            $errorMsg[] = '心得欄位必填';
            $required_field[] = 'content';
        }
        if (count($errorMsg) > 0) {
            $response = array(
                'error' => true,
                'message' => implode("\n", $errorMsg),
                'required_field' => array_values($required_field)
                );
            $this->responseJSON($response);
        }

        $check = ORM::for_table('event')
            ->where('event.member_uuid', $this->user['member_uuid'])
            ->where('event.event_name', EVENT_NAME)
            ->where('event.check', 1)
            ->find_one();
        if (!$check) {
            $response = array(
                'error' => true,
                'message' => '您沒有體驗會VIP資格',
                );
            $this->responseJSON($response);
        }
        $experience = ORM::for_table('event')
            ->where('event.member_uuid', $this->user['member_uuid'])
            ->where('event.event_name', EVENT_NAME."_POST")
            ->find_one();
        if ($experience) {
            $response = array(
                'error' => true,
                'message' => '您已經分享過了',
                'status_code' => '10001'
                );
            $this->responseJSON($response);
        }
        if (!$experience) {
            $experience = ORM::for_table('event')->create();
            $experience->member_uuid = $this->user['member_uuid'];
            $experience->event_name = EVENT_NAME."_POST";
            $experience->name = $check->name;
            $experience->phone = $check->phone;
            $experience->title = htmlspecialchars($title);
            $experience->content = htmlspecialchars($content);
            $experience->dummy = date('YmdHis').str_pad(ORM::for_table('event')->where('event.event_name', EVENT_NAME."_POST")->count() + 1, 2, STR_PAD_LEFT).rand(1, 999);
            $experience->content_uuid = Uuid();
            $experience->save();
            $response = array(
                'error' => false
                );
            $this->responseJSON($response);
        }

    }

    public function readMoreAction($path = array())
    {
        $now = date('Y-m-d H:i:s');
        $end_time = date('Y-m-d H:i:s', strtotime('2015-01-04'));

        $is_match = preg_match('/(\d*)/', $path, $matches);
        if ($is_match) {
            $post = ORM::for_table('event')
                ->select_many('event.title', 'event.content', 'event.name', 'event.created_at', 'event.dummy', 'event.member_uuid')
                ->select('member.nickname', 'name')
                ->select('event.content_uuid')
                ->join('member', array('member.member_uuid', '=', 'event.member_uuid'))
                ->where('event.event_name', EVENT_NAME."_POST")
                ->where('event.dummy', $matches[1])
                ->find_one();
            if (!$post) {
                $this->app->response->redirect($this->data['base_uri']);
            }
            $post->created_at = date('Y-m-d', strtotime($post->created_at));
            $post->content = htmlspecialchars_decode($post->content);

            // $is_match = preg_match("/[a-z-0-9-]*_\d*.jpg/", $post->content, $matches);
            $is_match = preg_match("/src=\"?\'?(http(.*).jpg)/", $post->content, $matches);
            if ($is_match) {
                $this->data['open_graph']['image'] = $matches[1];
            }
            $this->data['post'] = $post;
            $this->data['open_graph']['title'] = $post->title;
            $this->data['open_graph']['url'] = 'http://'.$_SERVER['HTTP_HOST'].$this->data['base_uri'].'/experience/'.$post->content_uuid;
            $this->data['open_graph']['canonical'] = 'http://'.$_SERVER['HTTP_HOST'].$this->data['base_uri'].'/experience/'.$post->content_uuid;
            $this->data['open_graph']['description'] = trim(preg_replace('/\s\s+/', ' ', strip_tags($post->content)));
            $editable = false;
            if ($now < $end_time) {
                if ($this->user && $this->user['member_uuid'] == $post->member_uuid) {
                    $editable = true;
                }
            }
            $this->data['editable'] = $editable;
        }
        echo $this->view->render('read-more.twig', $this->data);
    }

    public function editPageAction($path)
    {
        if (!$this->user) {
            $this->app->notFound();
        }
        $experience = ORM::for_table('event')
                ->select_many('event.title', 'event.content', 'event.name', 'event.created_at', 'event.dummy', 'event.member_uuid')
                ->select('member.nickname', 'name')
                ->select('event.content_uuid')
                ->join('member', array('member.member_uuid', '=', 'event.member_uuid'))
                ->where('event.event_name', EVENT_NAME."_POST")
                ->where('event.dummy', $path)
                ->find_one();
        if (!$experience) {
            $this->app->notFound();
        }
        if ($this->user['member_uuid'] != $experience->member_uuid) {
            $this->app->notFound();
        }
        $this->data['experience'] = $experience;
        echo $this->view->render('experience_eidt.twig', $this->data);
    }

    public function editAction($path)
    {
        $title = $this->app->request->post('title');
        $content = $this->app->request->post('content');
        $required_field = array();
        $errorMsg = array();
        if ($title == '') {
            $errorMsg[] = '標題欄位必填';
            $required_field[] = 'title';
        }
        if ($content == '') {
            $errorMsg[] = '心得欄位必填';
            $required_field[] = 'content';
        }
        if (count($errorMsg) > 0) {
            $response = array(
                'error' => true,
                'message' => implode("\n", $errorMsg),
                'required_field' => array_values($required_field)
                );
            $this->responseJSON($response);
        }

        $check = ORM::for_table('event')
            ->where('event.member_uuid', $this->user['member_uuid'])
            ->where('event.event_name', EVENT_NAME)
            ->where('event.check', 1)
            ->find_one();
        if (!$check) {
            $response = array(
                'error' => true,
                'message' => '您沒有體驗會VIP資格',
                );
            $this->responseJSON($response);
        }

        $experience = ORM::for_table('event')
            ->where('event.member_uuid', $this->user['member_uuid'])
            ->where('event.event_name', EVENT_NAME."_POST")
            ->where('event.dummy', $path)
            ->find_one();
        if ($experience) {
            $experience->title = htmlspecialchars($title);
            $experience->content = htmlspecialchars($content);
            $experience->save();
        }
        $response = array(
            'error' => false,
            'redirect' => ''
        );
        $response['redirect'] = $this->data['base_uri'].'/experience/'.$path;
        $this->responseJSON($response);
    }

    public function winnerAction()
    {
        $announced = false;
        if (date('Y-m-d') >= date('Y-m-d', strtotime('2015-01-09'))) {
            $announced = true;
        }
        $this->data['announced'] = $announced;

        echo $this->view->render('winner.twig', $this->data);
    }

    public function getUserInfo($member_uuid = '')
    {
        if ($member_uuid == '') {
            return false;
        }
        $rs = ORM::for_table('member')
            ->select_many('member.member_id', 'member.nickname', 'category.category_name')
            ->join('category', array('member.category_id', '=', 'category.category_id'))
            ->where('member.member_uuid', $member_uuid)
            ->find_one();
        if (!$rs) {
            return false;
        }
        $head_photo = getHeadPhoto($member_uuid);
        if (strpos($head_photo, "http")  === false) {
            $head_photo = 'http://www.muzik-online.com'.$head_photo;
        }
        $_user = array(
            'member_uuid' => $member_uuid,
            'member_id' => $rs->member_id,
            'nickname' => $rs->nickname,
            'category_name' => $rs->category_name,
            'role_id' => getMemberRoleFix($member_uuid),
            'head_photo' => $head_photo,
            );
        return $_user;
    }

    public function uploadAction()
    {

        $upload_path = APP_PATH.'/../../files/event/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path);
        }
        $upload_path = realpath($upload_path).'/'.EVENT_NAME;
        if (!is_dir($upload_path)) {
            mkdir($upload_path);
        }
        $count = count(glob($upload_path.'/'.getMemberUuid().'_*')) + 1;
        $file_name = getMemberUuid().'_'.$count.'.jpg';
        $pathToSave = realpath($upload_path).'/'.getMemberUuid().'_'.$count.'.jpg';

        $img = Image::make($_FILES['upload']['tmp_name'])->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save($pathToSave);

        $errorMsg = '';
        // 這邊記得回傳給ckeditor
        echo "<script>";
        if ($errorMsg=='') {
            // CKEditor 的編號
            $CKEditorFuncNum = isset($_GET['CKEditorFuncNum']) ? $_GET['CKEditorFuncNum'] : 2;
            // $fileUrl是圖片網址 就自己先處理好吧
            $fileUrl = EVENT_UPLOAD_IMAGE_URL.$file_name;
            echo "window.parent.CKEDITOR.tools.callFunction(". $CKEditorFuncNum .",'" . $fileUrl . "','');";
        } else {
            echo "alert('".$errorMsg."');";
        }
        echo "</script>";
    }

    public function logoutAction()
    {
        global $cookie;
        $cookie->deleteCookie(COOKIE_NAME_FOR_MEMBER_UUID, '/', '.muzik-online.com');
        $cookie->deleted = null;
        $cookie->deleteCookie(COOKIE_NAME_FOR_ACCESS_TOKEN, '/', '.muzik-online.com');
        $this->app->response->redirect($this->data['base_uri']);
    }

    public function loginAction()
    {
        $account = $this->app->request->post('account');
        $password = $this->app->request->post('password');

        $email = $this->app->request->post('email');
        $name = $this->app->request->post('name');
        $signedRequest = $this->app->request->post('signedRequest');
        //用FB登入
        if ($signedRequest != '') {

            list($encoded_sig, $payload) = explode('.', $signedRequest, 2);

            $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));

            $_request_data = json_decode(base64_decode(strtr($payload, '-_', '+/')), false);

            $expected_sig = hash_hmac('sha256', $payload, FACEBOOK_APP_SECRET, true);



            if ($sig != $expected_sig) {
                $response['error'] = true;
                $response['message'] = 'FB登入失敗';
                $this->responseJSON($response);
                exit();
            }
            $_fbid = $_request_data->user_id;

            $temp = fbLogin($_fbid, $email);
        } else {
            $temp = officalLogin($account, $password);
        }
        if ($temp['error'] === false) {
            $member = $temp['member'];
            global $cookie;
            $cookie->setCookie(COOKIE_NAME_FOR_MEMBER_UUID, $member['member_uuid'], '', null, '/', '.muzik-online.com');
            $cookie->setCookie(COOKIE_NAME_FOR_ACCESS_TOKEN, $member['access_token'], '', null, '/', '.muzik-online.com');
            $response = array(
                'error' => false,
                'message' => '登入成功'
                );
            $this->responseJSON($response);
        }
    }

    public function statusAction()
    {
        if ($this->user && $this->user['role_id'] == 1) {
            $sing_up = ORM::for_table('event')
                ->select_many('event.name', 'event.dummy', 'event.created_at')
                ->where('event_name', EVENT_NAME)
                ->order_by_desc('event.created_at')
                ->find_array();
            $data = array('sing_up' => $sing_up);
            echo $this->view->render('status.twig', $data);
        } else {
            $this->app->notFound();
        }
    }

    public function forgetAction()
    {
        $account = $this->app->request->post('account');
        $password = $this->app->request->post('password');
        $password_confirm = $this->app->request->post('password_confirm');
        if (!$account || !$password || !$password_confirm) {
            $_data['error'] = true;
            $_data['message'] = "欄位資訊不完全";
            $this->responseJSON($_data);
        }
        if ($password != $password_confirm) {
            $_data['error'] = true;
            $_data['message'] = "新密碼與確認密碼不符合";
            $this->responseJSON($_data);
        }
        $member = ORM::for_table('member')
            ->select_many('member.login_account', 'member.mail', 'member.member_uuid')
            ->where('login_account', $account)
            ->where('deleted_by', '')
            ->where('fbid', '')
            ->find_one();

        if (!$member) {
            $_data['error'] = true;
            $_data['message'] = '帳號不存在';
            $this->responseJSON($_data);
        }

        if (!filter_var($member->login_account, FILTER_VALIDATE_EMAIL)) {
            if (!filter_var($member->mail, FILTER_VALIDATE_EMAIL)) {
                $_data['error'] = true;
                $_data['message'] = '您未註冊合法的 Email';
                $this->responseJSON($_data);
                exit();
            } else {
                $_email_to = $member->mail;
            }
        } else {
            $_email_to = $member->login_account;
        }

        $expired_time = time() + EMAIL_TOKEN_EXPIRED_SECONDS;

        $data = $member->member_uuid.'|||'.$member->login_account.'|||'.$password.'|||'.$expired_time;

        $encrypted_data = openssl_encrypt($data, 'AES-256-CFB', SYSTEM_KEY, false, SYSTEM_KEY_IV16);

        $email_body = "請確認您於 Muzik-Online.com 網站上要求重新設定您的密碼。\n";
        $email_body = $email_body . "點選下列連結將會修改您的密碼。\n";
        $email_body = $email_body . $this->siteURL() . EMAIL_TOKEN_VERIFY_URL.$encrypted_data;
        $email_subject = '[Muzik-Online] Reset your password';
        $email_reply_to = array('muzikonline.tw@gmail.com' => 'Muzik-Online Service');
        list($result, $error) = $this->sendmail($email_subject, $_email_to, $email_body, $email_reply_to);
        if (!$result) {
            $_data['error'] = true;
            $_data['message'] = '發生未預期的錯誤';
            $this->responseJSON($_data);
            exit();
        }

        $_data['error'] = false;
        $_data['message'] = "系統將寄認證信至您所設定的電子信箱，請確認後並點選所附的網路連結";
        $this->responseJSON($_data);
        exit();

    }


    public function sendmail($email_subject, $email_to, $email_body, $email_reply_to = '')
    {

        $isDebug = false;
        // $isDebug = true;

        $transport = Swift_SmtpTransport::newInstance(EMAIL_HOST, EMAIL_PORT, 'tls')
          ->setUsername(EMAIL_USER)
          ->setPassword(EMAIL_PASS)
          ;

        $mailer = Swift_Mailer::newInstance($transport);

        if ($isDebug) {
            $logger = new Swift_Plugins_Loggers_ArrayLogger();
            $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
        }

        $message = Swift_Message::newInstance()
          ->setSubject($email_subject)
          ->setFrom(array(EMAIL_USER => EMAIL_USER_NICKNAME))
          ->setTo($email_to)
          ->setBody($email_body)
          ;

        if ($email_reply_to !== '') {
            $message->setReplyTo($email_reply_to);
        }

        $result = $mailer->send($message, $error);
        // echo $result;
        // var_dump($error);

        if ($isDebug) {
            echo $logger->dump();
        }

        return array($result, $error);
    }

    public function siteURL()
    {

        $protocol    = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName  = $_SERVER['HTTP_HOST'];
        return $protocol.$domainName;
        // return "https://".$domainName;

    }

    public function registerAction()
    {
        $account = $this->app->request->post('account');
        $password = $this->app->request->post('password');
        $password_confirm = $this->app->request->post('password_confirm');
        $temp = officalRegister($account, $password, $password_confirm);
        if ($temp['error']) {
            $data['error'] = true;
            $data['message'] = $temp['message'];
            $this->responseJSON($data);
        }
        $member = $temp['member'];
        global $cookie;
        $cookie->setCookie(COOKIE_NAME_FOR_MEMBER_UUID, $member['member_uuid'], '', null, '/');
        $cookie->setCookie(COOKIE_NAME_FOR_ACCESS_TOKEN, $member['access_token'], '', null, '/');
        $response = array(
            'error' => false,
            'message' => '登入成功'
            );
        $this->responseJSON($response);
    }

    private function responseJSON($data)
    {
        echo json_encode($data);
        exit();
    }
}
