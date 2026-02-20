<?php
// Contact form handler with optional PHPMailer (SMTP), reCAPTCHA verification, and rate limiting
// Expects POST: name, email, subject, message
// Returns 'OK' on success or an error message on failure

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Invalid request method.';
    exit;
}

$config = include __DIR__ . '/config.php';

// Rate limiting by session
$limit = isset($config['rate_limit_seconds']) ? (int) $config['rate_limit_seconds'] : 30;
if (isset($_SESSION['last_submit']) && (time() - $_SESSION['last_submit']) < $limit) {
    $wait = $limit - (time() - $_SESSION['last_submit']);
    echo 'Please wait ' . $wait . ' seconds before sending again.';
    exit;
}

// Basic sanitization
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Basic validation
if (empty($name) || empty($email) || empty($message)) {
    echo 'Please fill all required fields (name, email, message).';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Please enter a valid email address.';
    exit;
}

// Prevent header injection
function has_header_injection($str)
{
    return preg_match("/\r|\n/", $str);
}
if (has_header_injection($name) || has_header_injection($email) || has_header_injection($subject)) {
    echo 'Invalid input.';
    exit;
}

// Optional reCAPTCHA verification if secret is set and response provided
if (!empty($config['recaptcha']['secret_key']) && !empty($_POST['g-recaptcha-response'])) {
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $secret = $config['recaptcha']['secret_key'];
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($secret) . "&response=" . urlencode($recaptchaResponse));
    $captchaSuccess = json_decode($verify, true);
    if (!isset($captchaSuccess['success']) || $captchaSuccess['success'] !== true) {
        echo 'reCAPTCHA verification failed. Please try again.';
        exit;
    }
}

$to = $config['to_email'];
$subject_mail = 'Website Contact: ' . ($subject ? $subject : 'New message');

$body = "You have received a new message from your website contact form:\n\n";
$body .= "Name: " . $name . "\n";
$body .= "Email: " . $email . "\n";
if ($subject) {
    $body .= "Subject: " . $subject . "\n";
}
$body .= "\nMessage:\n" . $message . "\n";

// Try PHPMailer (if enabled and available), otherwise fallback to mail()
$sent = false;
// Prefer Composer autoload if available
$autoload = false;
if (!empty($config['phpmailer_autoload']) && file_exists($config['phpmailer_autoload'])) {
    $autoload = $config['phpmailer_autoload'];
} else if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
}

if (!empty($config['use_phpmailer']) && $autoload) {
    try {
        require_once $autoload;

        // PHPMailer available via Composer autoload
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        // Use SMTP if host provided
        if (!empty($config['smtp']['host'])) {
            $mail->isSMTP();
            $mail->Host = $config['smtp']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp']['username'];
            $mail->Password = $config['smtp']['password'];
            $mail->SMTPSecure = $config['smtp']['secure'];
            $mail->Port = $config['smtp']['port'];
        }

        $fromEmail = !empty($config['smtp']['from_email']) ? $config['smtp']['from_email'] : $email;
        $fromName = !empty($config['smtp']['from_name']) ? $config['smtp']['from_name'] : $name;

        $mail->setFrom($fromEmail, $fromName);
        $mail->addReplyTo($email, $name);
        $mail->addAddress($to);

        $mail->Subject = $subject_mail;
        $mail->Body = $body;
        $mail->AltBody = $body;

        $sent = $mail->send();
    } catch (Exception $e) {
        // fallback to mail(); also capture the exception message for logging
        $sent = false;
        $errorMsg = $e->getMessage();
    }
}

if (!$sent) {
    $headers = "From: " . $name . " <" . $email . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $sent = @mail($to, $subject_mail, $body, $headers);
}

// Log the attempt (sanitized)
$logEntry = date('Y-m-d H:i:s') . " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . " | From: " . filter_var($email, FILTER_SANITIZE_EMAIL) . " | Subject: " . substr(preg_replace('/\s+/', ' ', $subject), 0, 100) . " | MessageLen: " . strlen($message) . " | Result: " . ($sent ? 'OK' : 'FAILED') . "\n";
@file_put_contents(__DIR__ . '/mail.log', $logEntry, FILE_APPEND | LOCK_EX);

if ($sent) {
    $_SESSION['last_submit'] = time();
    echo 'OK';
} else {
    if (isset($errorMsg)) {
        echo 'Mail delivery failed: ' . htmlspecialchars($errorMsg);
    } else {
        echo 'Mail delivery failed. Please try again later.';
    }
}
