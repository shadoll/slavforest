<?php

require __DIR__.'/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class sender
{
    public function send($data)
    {
        $dotenv = \Dotenv\Dotenv::create(__DIR__);
        $dotenv->load();

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host = getenv('SMTP_SERVER');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USER');
            $mail->Password = getenv('SMTP_PASSWORD');
            $mail->SMTPSecure = 'tls';

            $mail->From = getenv('SENDER_EMAIL');
            $mail->FromName = getenv('SENDER_NAME');
            $mail->addAddress(getenv('SMTP_RECEIVER'));

            $mail->isHTML(true);
            $mail->Subject = $data->subject;
            $mail->Body = $data->body;

            $mail->send();
            echo 'Message has been sent';

            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

            return false;
        }
    }
}
