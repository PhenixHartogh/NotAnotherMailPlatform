<?php
require_once __DIR__ . '/vendor/autoload.php'; )
$env = parse_ini_file(__DIR__ . '/.env');

$cpanelUser = $env['CPANEL_USER'];
$cpanelToken = $env['CPANEL_API_TOKEN'];
$cpanelDomain = $env['CPANEL_HOST'];

$allowedDomains = [
  'domain',
  'domain',
  'domain',
];

$email = preg_replace('/[^a-zA-Z0-9.]/', '', $_POST['email'] ?? '');
$domain = $_POST['domain'] ?? '';
$oldpw = $_POST['old_password'] ?? '';
$newpw = $_POST['new_password'] ?? '';
$captcha = $_POST['h-captcha-response'] ?? '';

if (!$email || !$domain || !$oldpw || !$newpw || !$captcha) {
  http_response_code(400);
  exit("Missing fields.");
}

if (!in_array($domain, $allowedDomains)) {
  http_response_code(403);
  exit("Invalid domain.");
}

$secret = $env['HCAPTCHA_SECRET'];
$verify = file_get_contents("https://hcaptcha.com/siteverify?secret=$secret&response=" . urlencode($captcha));
$verify = json_decode($verify, true);
if (!$verify['success']) {
  http_response_code(403);
  exit("Captcha failed.");
}

$imapServer = "localhost";
$fullEmail = "$email@$domain";
$imap = @imap_open("{".$imapServer.":993/imap/ssl}INBOX", $fullEmail, $oldpw);
if (!$imap) {
  http_response_code(403);
  exit("Current password incorrect.");
}
imap_close($imap);

$apiUrl = "https://$cpanelDomain:2083/execute/Email/passwd_pop?email=$email&domain=$domain&password=" . urlencode($newpw);
$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: cpanel $cpanelUser:$cpanelToken"]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
$info = curl_getinfo($curl);
curl_close($curl);

if ($info['http_code'] === 200) {
  echo "success";
} else {
  http_response_code(500);
  echo "Failed to change password.";
}
