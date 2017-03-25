<?php

namespace dantux\helpers;

use \Yii;

class Email
{

    public static function send_email($to = 'office@trupa-piscotel.ro',$subject = '',$message = '',$from = 'website@trupa-piscotel.ro', $fromName='Online Site', $is_html='true', $reply_to ='website@trupa-piscotel.ro') {
        $mail = new PHPMailer();
        $mail->isSendmail();
        $mail->From = $from;
        $mail->FromName = $fromName;
        $mail->AddAddress($to, $to);
        $mail->AddReplyTo($reply_to, $fromName);
        $mail->IsHTML($is_html);
        $mail->Subject = $subject;
        $mail->Body = $message;

        if($mail->Send()) {
            Logger::event("user", "Email Sent", "Email sent to {$to} with subject \"{$subject}\".");
            return true;
        } else {
            Logger::event("user", "Email Sent FAILED!", "Could not send email");
            return false;
        }
    }
}
