<?php
$cfg = @include __DIR__ . '/contactform/config.php';
$recaptcha_site_key = '';
if (is_array($cfg) && !empty($cfg['recaptcha']['site_key'])) {
  $recaptcha_site_key = $cfg['recaptcha']['site_key'];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Nett Solutions</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="keywords" />
    <meta content="" name="description" />

    <!-- Favicons -->
    <link href="img/favicon.png" rel="icon" />
    <link href="img/apple-touch-icon.png" rel="apple-touch-icon" />

    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Montserrat:300,400,500,700"
      rel="stylesheet"
    />

    <!-- Bootstrap CSS File -->
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Libraries CSS Files -->
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="lib/animate/animate.min.css" rel="stylesheet" />
    <link href="lib/ionicons/css/ionicons.min.css" rel="stylesheet" />
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet" />

    <!-- Main Stylesheet File -->
    <link href="css/style.css" rel="stylesheet" />

    <?php if (!empty($recaptcha_site_key)) echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>'; ?>

    <!-- =======================================================
    Theme Name: NewBiz
    Theme URL: https://bootstrapmade.com/newbiz-bootstrap-business-template/
    Author: BootstrapMade.com
    License: https://bootstrapmade.com/license/
  ======================================================= -->
  </head>

  <body>
<!-- the rest of the file is identical to index.html except the contact form area where reCAPTCHA is conditionally rendered -->
<?php
// Include the rest of the HTML from the original index.html and inject reCAPTCHA widget when site key is configured.
$html = file_get_contents(__DIR__ . '/index.html');
if (!empty($recaptcha_site_key)) {
  $widget = '<div class="g-recaptcha mb-3" data-sitekey="' . htmlspecialchars($recaptcha_site_key) . '"></div>';
  // Replace the placeholder comment block with the widget
  $html = str_replace('<!-- Optional reCAPTCHA: add this script in <head> and your site key in config.php to enable -->\n                <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->', $widget, $html);
} else {
  // Remove the placeholder comment if no key
  $html = str_replace('<!-- Optional reCAPTCHA: add this script in <head> and your site key in config.php to enable -->\n                <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->', '', $html);
}
echo $html;
?>
  </body>
</html>
