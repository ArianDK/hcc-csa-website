<?php
/**
 * Minimal PHPMailer implementation for CSA website
 * Simplified version focusing on SMTP sending
 */

class PHPMailer {
    private $host = '';
    private $port = 587;
    private $username = '';
    private $password = '';
    private $from = '';
    private $fromName = '';
    private $to = [];
    private $subject = '';
    private $body = '';
    private $altBody = '';
    private $isHTML = true;
    
    public function __construct() {
        // Initialize
    }
    
    public function isSMTP() {
        // Set to use SMTP
        return $this;
    }
    
    public function setHost($host) {
        $this->host = $host;
        return $this;
    }
    
    public function setPort($port) {
        $this->port = $port;
        return $this;
    }
    
    public function setSMTPAuth($auth) {
        // Always use auth for this implementation
        return $this;
    }
    
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }
    
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    public function setSMTPSecure($secure) {
        // TLS is assumed
        return $this;
    }
    
    public function setFrom($email, $name = '') {
        $this->from = $email;
        $this->fromName = $name;
        return $this;
    }
    
    public function addAddress($email, $name = '') {
        $this->to[] = ['email' => $email, 'name' => $name];
        return $this;
    }
    
    public function isHTML($isHTML = true) {
        $this->isHTML = $isHTML;
        return $this;
    }
    
    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }
    
    public function setBody($body) {
        $this->body = $body;
        return $this;
    }
    
    public function setAltBody($altBody) {
        $this->altBody = $altBody;
        return $this;
    }
    
    public function send() {
        try {
            $socket = fsockopen($this->host, $this->port, $errno, $errstr, 30);
            if (!$socket) {
                throw new Exception("Could not connect to SMTP server: $errstr ($errno)");
            }
            
            // Read initial response
            $this->readResponse($socket);
            
            // EHLO
            fwrite($socket, "EHLO " . $_SERVER['SERVER_NAME'] . "\r\n");
            $this->readResponse($socket);
            
            // STARTTLS
            fwrite($socket, "STARTTLS\r\n");
            $this->readResponse($socket);
            
            // Enable crypto
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new Exception("Failed to enable TLS");
            }
            
            // EHLO again after TLS
            fwrite($socket, "EHLO " . $_SERVER['SERVER_NAME'] . "\r\n");
            $this->readResponse($socket);
            
            // AUTH LOGIN
            fwrite($socket, "AUTH LOGIN\r\n");
            $this->readResponse($socket);
            
            fwrite($socket, base64_encode($this->username) . "\r\n");
            $this->readResponse($socket);
            
            fwrite($socket, base64_encode($this->password) . "\r\n");
            $this->readResponse($socket);
            
            // MAIL FROM
            fwrite($socket, "MAIL FROM: <{$this->from}>\r\n");
            $this->readResponse($socket);
            
            // RCPT TO
            foreach ($this->to as $recipient) {
                fwrite($socket, "RCPT TO: <{$recipient['email']}>\r\n");
                $this->readResponse($socket);
            }
            
            // DATA
            fwrite($socket, "DATA\r\n");
            $this->readResponse($socket);
            
            // Headers and body
            $message = $this->buildMessage();
            fwrite($socket, $message . "\r\n.\r\n");
            $this->readResponse($socket);
            
            // QUIT
            fwrite($socket, "QUIT\r\n");
            fclose($socket);
            
            return true;
            
        } catch (Exception $e) {
            error_log('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    private function readResponse($socket) {
        $response = fgets($socket, 512);
        if (substr($response, 0, 1) == '4' || substr($response, 0, 1) == '5') {
            throw new Exception("SMTP Error: $response");
        }
        return $response;
    }
    
    private function buildMessage() {
        $boundary = uniqid('boundary_');
        $headers = [];
        
        // Basic headers
        $headers[] = "From: {$this->fromName} <{$this->from}>";
        $headers[] = "To: " . implode(', ', array_map(function($r) {
            return $r['name'] ? "{$r['name']} <{$r['email']}>" : $r['email'];
        }, $this->to));
        $headers[] = "Subject: {$this->subject}";
        $headers[] = "Date: " . date('r');
        $headers[] = "Message-ID: <" . uniqid() . "@" . $_SERVER['SERVER_NAME'] . ">";
        
        if ($this->isHTML && $this->altBody) {
            // Multipart message
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: multipart/alternative; boundary=\"$boundary\"";
            $headers[] = "";
            
            $body = "--$boundary\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $this->altBody . "\r\n\r\n";
            
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $this->body . "\r\n\r\n";
            
            $body .= "--$boundary--";
            
        } else if ($this->isHTML) {
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/html; charset=UTF-8";
            $headers[] = "";
            $body = $this->body;
        } else {
            $headers[] = "Content-Type: text/plain; charset=UTF-8";
            $headers[] = "";
            $body = $this->altBody ?: $this->body;
        }
        
        return implode("\r\n", $headers) . "\r\n" . $body;
    }
}

/**
 * Email utility class for CSA website
 */
class EmailService {
    private static $config = null;
    
    public static function sendVerificationEmail($email, $firstName, $token) {
        self::loadConfig();
        
        $mail = new PHPMailer();
        $mail->isSMTP()
             ->setHost(self::$config['smtp']['host'])
             ->setPort(self::$config['smtp']['port'])
             ->setSMTPAuth(true)
             ->setUsername(self::$config['smtp']['user'])
             ->setPassword(self::$config['smtp']['pass'])
             ->setSMTPSecure('tls');
        
        $mail->setFrom(self::$config['smtp']['from_email'], self::$config['smtp']['from_name'])
             ->addAddress($email, $firstName)
             ->isHTML(true)
             ->setSubject('Confirm your CSA membership');
        
        $verifyUrl = self::$config['app']['base_url'] . "/verify.php?token=" . urlencode($token);
        
        $htmlBody = self::getVerificationEmailHTML($firstName, $verifyUrl);
        $textBody = self::getVerificationEmailText($firstName, $verifyUrl);
        
        $mail->setBody($htmlBody)
             ->setAltBody($textBody);
        
        return $mail->send();
    }
    
    public static function sendAdminNotification($memberData) {
        self::loadConfig();
        
        if (!self::$config['features']['admin_notifications']) {
            return true; // Feature disabled
        }
        
        $mail = new PHPMailer();
        $mail->isSMTP()
             ->setHost(self::$config['smtp']['host'])
             ->setPort(self::$config['smtp']['port'])
             ->setSMTPAuth(true)
             ->setUsername(self::$config['smtp']['user'])
             ->setPassword(self::$config['smtp']['pass'])
             ->setSMTPSecure('tls');
        
        $mail->setFrom(self::$config['smtp']['from_email'], self::$config['smtp']['from_name'])
             ->addAddress(self::$config['app']['admin_email'])
             ->isHTML(false)
             ->setSubject('New CSA Member Registration');
        
        $body = "New member registration:\n\n";
        $body .= "Name: {$memberData['first_name']} {$memberData['last_name']}\n";
        $body .= "Email: {$memberData['email']}\n";
        $body .= "Major: {$memberData['major']}\n";
        $body .= "Campus: {$memberData['campus']}\n";
        $body .= "Time: " . date('Y-m-d H:i:s') . "\n";
        
        $mail->setBody($body);
        
        return $mail->send();
    }
    
    private static function loadConfig() {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/config.php';
        }
    }
    
    private static function getVerificationEmailHTML($firstName, $verifyUrl) {
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Confirm your CSA membership</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1a365d; color: white; padding: 20px; text-align: center; }
        .content { background: #f7fafc; padding: 30px; }
        .button { display: inline-block; background: #3182ce; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background: #edf2f7; padding: 20px; font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Welcome to CSA!</h1>
        </div>
        <div class='content'>
            <p>Hi {$firstName},</p>
            
            <p>Thanks for joining the Computer Science Association at Houston Community College! We're excited to have you as part of our community.</p>
            
            <p>To complete your registration, please verify your email address by clicking the button below:</p>
            
            <p style='text-align: center;'>
                <a href='{$verifyUrl}' class='button'>Verify Your Email</a>
            </p>
            
            <p>This link will expire in 7 days. If you didn't sign up for CSA, you can safely ignore this email.</p>
            
            <p>Once verified, you'll receive updates about:</p>
            <ul>
                <li>Weekly meetings and workshops</li>
                <li>Hackathons and coding competitions</li>
                <li>Career preparation events</li>
                <li>Networking opportunities</li>
                <li>Study groups and project collaborations</li>
            </ul>
            
            <p>Questions? Reply to this email or contact us at president@hccs.edu</p>
            
            <p>Welcome aboard!<br>
            The CSA Team</p>
        </div>
        <div class='footer'>
            <p>Computer Science Association - Houston Community College<br>
            You can unsubscribe from future emails at any time by contacting us.</p>
        </div>
    </div>
</body>
</html>";
    }
    
    private static function getVerificationEmailText($firstName, $verifyUrl) {
        return "Hi {$firstName},

Thanks for joining the Computer Science Association at Houston Community College! We're excited to have you as part of our community.

To complete your registration, please verify your email address by visiting:
{$verifyUrl}

This link will expire in 7 days. If you didn't sign up for CSA, you can safely ignore this email.

Once verified, you'll receive updates about weekly meetings, workshops, hackathons, career events, and more!

Questions? Reply to this email or contact us at president@hccs.edu

Welcome aboard!
The CSA Team

Computer Science Association - Houston Community College
You can unsubscribe from future emails at any time by contacting us.";
    }
}
?>
