<?php
// Simple script to POST to the contact form handler and print response
$payload = [
    'name' => 'Automated',
    'email' => 'auto_test@example.com',
    'subject' => 'AutomatedTest',
    'message' => 'Hello from automation'
];
$opts = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($payload),
        'timeout' => 10,
    ],
];
$context = stream_context_create($opts);
$url = 'http://127.0.0.1:8000/contactform/contactform.php';
$result = @file_get_contents($url, false, $context);
if ($result === false) {
    $err = error_get_last();
    echo "Request failed: " . ($err['message'] ?? 'unknown error') . PHP_EOL;
    exit(1);
}
echo "Response: " . $result . PHP_EOL;
