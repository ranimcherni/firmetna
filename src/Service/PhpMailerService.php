<?php

namespace App\Service;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerService
{
    public function __construct(
        private string $mailerHost,
        private string $mailerPort,
        private string $mailerUser,
        private string $mailerPassword,
        private string $mailerFromEmail,
        private string $mailerFromName = 'FIRMETNA',
    ) {
    }

    /**
     * Envoie un email HTML.
     *
     * @param string $to      Email du destinataire
     * @param string $subject Sujet de l'email
     * @param string $htmlBody Corps HTML de l'email
     *
     * @return bool True si l'envoi a réussi, false sinon
     */
    public function sendHtmlEmail(string $to, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Configuration SMTP (si host configuré)
            if (!empty($this->mailerHost) && $this->mailerHost !== 'null') {
                $mail->isSMTP();
                $mail->Host = $this->mailerHost;
                $mail->SMTPAuth = !empty($this->mailerUser);
                $mail->Username = $this->mailerUser;
                $mail->Password = $this->mailerPassword;
                $port = (int) $this->mailerPort ?: 587;
                $mail->Port = $port;
                $mail->SMTPSecure = $port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
                $mail->CharSet = PHPMailer::CHARSET_UTF8;
            } else {
                // Fallback: fonction mail() PHP (tests locaux sans SMTP)
                $mail->isMail();
                $mail->CharSet = PHPMailer::CHARSET_UTF8;
            }

            $mail->setFrom($this->mailerFromEmail, $this->mailerFromName);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $htmlBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
