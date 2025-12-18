Contact form setup

1. Install PHPMailer (recommended)

   - From the project root run: `composer install` (this will install PHPMailer into `vendor/`)

2. Configure

   - Edit `contactform/config.php` and set:
     - `smtp` settings if you want to use SMTP (recommended for reliable delivery)
     - `recaptcha.site_key` and `recaptcha.secret_key` if you want reCAPTCHA protection
     - `to_email` if you'd like contact emails sent elsewhere

3. reCAPTCHA

   - If `recaptcha.site_key` is set in `config.php`, the contact form will render the widget automatically
   - The server-side verification uses `recaptcha.secret_key` to validate submissions

4. Mail logging

   - All delivery attempts are appended to `contactform/mail.log` (timestamp, IP, sanitized email, subject, message length, result)

5. Testing
   - Run the site on a PHP-enabled server (e.g., XAMPP, WAMP). Submit the contact form and check the `mail.log` for details.

Notes

- If you prefer not to use Composer, you may vendor the PHPMailer `src/` files into `contactform/PHPMailer/src/` and set `phpmailer_autoload` in `config.php` accordingly.
