<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

class Mailer {
    
    protected PHPMailer $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['SMTP_HOST'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $_ENV['SMTP_USERNAME'];
        $this->mail->Password   = $_ENV['SMTP_PASSWORD'];
        
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $port = (int)$_ENV['SMTP_PORT'];
        $secure = $_ENV['SMTP_SECURE'] ?? 'tls'; // Padrão para TLS

        $this->mail->SMTPSecure = $secure === 'ssl' 
                                ? PHPMailer::ENCRYPTION_SMTPS 
                                : PHPMailer::ENCRYPTION_STARTTLS;
                                
        $this->mail->Port = $port;  
        
        $this->mail->setFrom($_ENV['SMTP_USERNAME'], $_ENV['MAIL_FROM_NAME'] ?? 'API de Login');
        
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }

    public function to(string $toEmail, string $subject): self {
        $this->mail->addAddress($toEmail);
        $this->mail->Subject = $subject;
        return $this;
    }

    public function body(string $htmlContent, string $altText = ''): self {
        $this->mail->Body    = $htmlContent;
        $this->mail->AltBody = $altText ?: strip_tags($htmlContent); 
        return $this;
    }
    
    public function send(): bool {
        try {
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}