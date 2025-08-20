<?php
/**
 * CAPTCHA Implementation (reCAPTCHA v3 or hCaptcha)
 */

$config = require __DIR__ . '/../config/config.php';
$captchaProvider = $config['security']['captcha_provider'] ?? 'recaptcha';
$siteKey = $config['security']['captcha_site_key'] ?? '';

if (empty($siteKey)) {
    echo '<!-- CAPTCHA not configured -->';
    return;
}
?>

<?php if ($captchaProvider === 'recaptcha'): ?>
    <!-- reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo htmlspecialchars($siteKey); ?>"></script>
    <script>
        window.csaCaptcha = {
            provider: 'recaptcha',
            siteKey: '<?php echo htmlspecialchars($siteKey); ?>',
            
            execute: function(action, callback) {
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?php echo htmlspecialchars($siteKey); ?>', {action: action}).then(function(token) {
                        callback(token);
                    });
                });
            },
            
            addTokenToForm: function(formElement, action) {
                action = action || 'submit';
                this.execute(action, function(token) {
                    // Remove any existing token inputs
                    var existingTokens = formElement.querySelectorAll('input[name="captcha_token"]');
                    existingTokens.forEach(function(input) {
                        input.remove();
                    });
                    
                    // Add new token
                    var tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = 'captcha_token';
                    tokenInput.value = token;
                    formElement.appendChild(tokenInput);
                });
            }
        };
    </script>

<?php elseif ($captchaProvider === 'hcaptcha'): ?>
    <!-- hCaptcha -->
    <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <div class="h-captcha" 
         data-sitekey="<?php echo htmlspecialchars($siteKey); ?>" 
         data-size="invisible"></div>
    
    <script>
        window.csaCaptcha = {
            provider: 'hcaptcha',
            siteKey: '<?php echo htmlspecialchars($siteKey); ?>',
            
            execute: function(action, callback) {
                hcaptcha.execute();
                // hCaptcha doesn't support custom actions like reCAPTCHA
                // The callback will be handled by the global hcaptcha callback
                window.hcaptchaCallback = callback;
            },
            
            addTokenToForm: function(formElement, action) {
                var self = this;
                this.execute(action, function(token) {
                    // Remove any existing token inputs
                    var existingTokens = formElement.querySelectorAll('input[name="captcha_token"]');
                    existingTokens.forEach(function(input) {
                        input.remove();
                    });
                    
                    // Add new token
                    var tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = 'captcha_token';
                    tokenInput.value = token;
                    formElement.appendChild(tokenInput);
                });
            }
        };
        
        // Global callback for hCaptcha
        function hcaptchaCallback(token) {
            if (window.hcaptchaCallback) {
                window.hcaptchaCallback(token);
            }
        }
    </script>

<?php endif; ?>

<?php
/**
 * Server-side CAPTCHA verification function
 */
class CaptchaVerifier {
    private static $config = null;
    
    public static function verify($token, $remoteIP = null) {
        self::loadConfig();
        
        if (empty($token)) {
            return false;
        }
        
        $provider = self::$config['security']['captcha_provider'] ?? 'recaptcha';
        $secret = self::$config['security']['captcha_secret'] ?? '';
        
        if (empty($secret)) {
            error_log('CAPTCHA secret not configured');
            return false;
        }
        
        if ($provider === 'recaptcha') {
            return self::verifyRecaptcha($token, $secret, $remoteIP);
        } elseif ($provider === 'hcaptcha') {
            return self::verifyHcaptcha($token, $secret, $remoteIP);
        }
        
        return false;
    }
    
    private static function verifyRecaptcha($token, $secret, $remoteIP) {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secret,
            'response' => $token
        ];
        
        if ($remoteIP) {
            $data['remoteip'] = $remoteIP;
        }
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        if ($result === false) {
            error_log('Failed to verify reCAPTCHA');
            return false;
        }
        
        $response = json_decode($result, true);
        
        // For reCAPTCHA v3, also check score
        if (isset($response['score'])) {
            return $response['success'] && $response['score'] >= 0.5;
        }
        
        return $response['success'] ?? false;
    }
    
    private static function verifyHcaptcha($token, $secret, $remoteIP) {
        $url = 'https://hcaptcha.com/siteverify';
        $data = [
            'secret' => $secret,
            'response' => $token
        ];
        
        if ($remoteIP) {
            $data['remoteip'] = $remoteIP;
        }
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        if ($result === false) {
            error_log('Failed to verify hCaptcha');
            return false;
        }
        
        $response = json_decode($result, true);
        return $response['success'] ?? false;
    }
    
    private static function loadConfig() {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../config/config.php';
        }
    }
}
?>
