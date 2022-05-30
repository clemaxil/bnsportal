<?php

require_once(ROOT.'app/libraries/phpmailer/src/PHPMailer.php');	
require_once(ROOT.'app/libraries/phpmailer/src/Exception.php');	
require_once(ROOT.'app/libraries/phpmailer/src/SMTP.php');	
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//TODO jouter les tableaux cc bcc et attachement
function appHelperEmail_send($userMail,$userName,$subject,$body,$altBody){
    global $app_config;
   
    try {        
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = $app_config['smtp_host'];
        $mail->Port = $app_config['smtp_port'];
        $mail->SMTPSecure = $app_config['smtp_secure'];
        $mail->SMTPAutoTLS = false;
        $mail->SMTPAuth = $app_config['smtp_auth'];
        $mail->Username = $app_config['smtp_user'];
        $mail->Password = $app_config['smtp_password'];
        $mail->setFrom($app_config['smtp_from_email'], $app_config['smtp_from_name']);
        $mail->addReplyTo($app_config['smtp_from_noreply_email'], $app_config['smtp_from_noreply_name']);
        $mail->addAddress($userMail,$userName);       
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $body;    
        $mail->AltBody = $altBody;

        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

}