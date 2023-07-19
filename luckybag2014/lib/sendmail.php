<?php
// reference: http://swiftmailer.org/docs/messages.html

// include getcwd() . '/vendor/autoload.php';

/* *
    $email_subject = '[Muzik-Online] Your password';
    $email_to = array(
        'yftzeng@gmail.com' => 'Yi-Feng Tzeng',
    );
    $email_body = 'Here is the message itself';
    $email_reply_to = array(
        'muzikonline.tw@gmail.com' => 'Muzik-Online Service',
    );
/* */


function sendmail($email_subject, $email_to, $email_body, $email_reply_to = '') {
    $isDebug = false;

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
    
    if ($isDebug) {
        echo $logger->dump();
    }
    
    return array($result, $error);
}
