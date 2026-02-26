<?php
$cfg = @include __DIR__ . '/contactform/config.php';
$recaptcha_site_key = '';
if (is_array($cfg) && !empty($cfg['recaptcha']['site_key'])) {
  $recaptcha_site_key = $cfg['recaptcha']['site_key'];
}

// Read the template
$html = file_get_contents(__DIR__ . '/index.html');

if (!empty($recaptcha_site_key)) {
  // Inject Script into <head>
  $script = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
  $html = str_replace('</head>', $script . "\n</head>", $html);

  // Inject Widget
  $widget = '<div class="g-recaptcha mb-3" data-sitekey="' . htmlspecialchars($recaptcha_site_key) . '"></div>';
  $placeholder = '<!-- Optional reCAPTCHA: add this script in <head> and your site key in config.php to enable -->';
  $html = str_replace($placeholder, $widget, $html);
}

// Serve the modified HTML
echo $html;
?>