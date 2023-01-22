<?php

namespace App\Service;

use PHPMailer\PHPMailer\{PHPMailer, Exception};

use App\Models\{NewUserEmail, User};

class MailService
{
    static function sendMail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.ukr.net';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'oleksiyyy882@ukr.net';
            $mail->Password   = 'EYH7dnhhzOh2UVQp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 2525;             
        
            //Recipients
            $mail->setFrom('oleksiyyy882@ukr.net', 'Matcha');
            $mail->addAddress($to);
        
            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->msgHTML($body);

            $mail->send();
        
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    static function sendActivationMail($user)
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../public/mail_templates');
        $twig = new \Twig\Environment($loader);

        $body = $twig->render('activationMail.twig', ['activation_token' => $user->pending_user->token]);

        self::sendMail($user->email, 'The Matcha', $body);
    }

    static function sendRecoveryMail($user, $new_password)
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../public/mail_templates');
        $twig = new \Twig\Environment($loader);

        $body = $twig->render('recoveryMail.twig', ['username' => $user->username, 'new_password' => $new_password]);

        self::sendMail($user->email, 'The Matcha', $body);
    }

    static function sendChangeMail(User $user, NewUserEmail $new_email)
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../public/mail_templates');
        $twig = new \Twig\Environment($loader);

        $body = $twig->render('changeMail.twig', ['username' => $user->username, 'activation_token' => $new_email->token]);

        self::sendMail($new_email->email, 'The Matcha', $body); 
    }
}