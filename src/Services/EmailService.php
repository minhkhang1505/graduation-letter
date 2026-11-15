<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;

class EmailService
{
    public function sendWelcomeEmail(array $user): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();

            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;

            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) $_ENV['MAIL_PORT'];

            $mail->CharSet = 'UTF-8';

            $mail->setFrom(
                $_ENV['MAIL_FROM_ADDRESS'],
                $_ENV['MAIL_FROM_NAME']
            );

            $mail->addAddress(
                $user['email'],
                $user['name']
            );

            $mail->isHTML(true);

            $mail->Subject = 'Chào mừng bạn đến với ' . APP_NAME;

            ob_start();

            require dirname(__DIR__, 2)
                . '/templates/welcome-email.php';

            $mail->Body = ob_get_clean();

            $mail->AltBody =
                'Xin chào ' . $user['name']
                . ', cảm ơn bạn đã đăng nhập vào '
                . APP_NAME;

            $mail->send();

        } catch (\Throwable $e) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            throw new Exception(
                'Email sending failed: ' . $e->getMessage()
            );
        }
    }
}
