<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!function_exists('appHelperMail_send')) {

    /**
     * 
     * @param string $userMail 
     * @param string $userName 
     * @param string $subject 
     * @param string $body 
     * @param string $altBody 
     * @param string $cc 
     * @param string $bcc 
     * @param string[] $attachments 
     * @return string
     */
    function appHelperMail_send($userMail, $userName, $subject, $body, $altBody, $cc = '', $bcc = '', $attachments = array())
    {
        $app_config = include 'config.php';

        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = $app_config['smtp_host'];
            $mail->Port = $app_config['smtp_port'];
            $mail->SMTPSecure = $app_config['smtp_secure'];
            $mail->SMTPAuth = $app_config['smtp_auth'];
            $mail->Username = $app_config['smtp_user'];
            $mail->Password = $app_config['smtp_password'];
            $mail->setFrom($app_config['smtp_from_email'], $app_config['smtp_from_name']);
            $mail->addReplyTo($app_config['smtp_from_noreply_email'], $app_config['smtp_from_noreply_name']);
            $mail->addAddress($userMail, $userName);

            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $body;
            $mail->AltBody = $altBody;

            if (!empty($cc)) {
                $mail->addCC($cc);
            }
            if (!empty($bcc)) {
                $mail->addCC($bcc);
            }

            if (count($attachments) >= 1) {
                foreach ($attachments as $key => $val) {
                    if (!empty($val)) {
                        $mail->addAttachment($val);
                    }
                }
            }

            $mail->send();
        } catch (Exception $e) {
            $errorMessage = str_replace('https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting', '', $mail->ErrorInfo);
            return 'Message could not be sent. ' . $errorMessage;
        }

        return 'OK';
    }
}
