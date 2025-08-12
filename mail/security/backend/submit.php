<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    http_response_code(500);
    exit('.env file not found.');
}
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($key, $value) = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

$cpanelUser      = $_ENV['CPANEL_USER'];
$cpanelToken     = $_ENV['CPANEL_TOKEN'];
$cpanelHost      = $_ENV['CPANEL_HOST'];
$hcaptchaSecret  = $_ENV['HCAPTCHA_SECRET'];


$username = preg_replace("/[^a-zA-Z0-9.]/", "", $_POST['email'] ?? '');
$domain   = $_POST['domain'] ?? '';
$password = $_POST['password'] ?? '';
$captcha  = $_POST['h-captcha-response'] ?? '';

if (!$username || !$domain || !$password || !$captcha) {
    http_response_code(400);
    exit("Missing required fields.");
}

$allowedDomains = [
    'domain',
    'domain',
    'domain',
];

if (!in_array($domain, $allowedDomains)) {
    http_response_code(400);
    exit("Invalid domain.");
}

if (strlen($password) < 8) {
    http_response_code(400);
    exit("Password must be at least 8 characters long.");
}

$verify = file_get_contents("https://hcaptcha.com/siteverify", false, stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-type: application/x-www-form-urlencoded",
        'content' => http_build_query([
            'secret'   => $hcaptchaSecret,
            'response' => $captcha,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ])
    ]
]));

$responseData = json_decode($verify, true);
if (empty($responseData['success'])) {
    http_response_code(403);
    exit("hCaptcha failed.");
}

$headers = ["Authorization: cpanel $cpanelUser:$cpanelToken"];
$listUrl = "$cpanelHost/execute/Email/list_pops?api.version=1";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $listUrl,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_RETURNTRANSFER => true,
]);
$listResponse = curl_exec($ch);
curl_close($ch);

$listData = json_decode($listResponse, true);
if (!empty($listData['data'])) {
    foreach ($listData['data'] as $acct) {
        if ($acct['email'] === "$username@$domain") {
            http_response_code(409);
            exit("That email already exists.");
        }
    }
}

$createUrl = "$cpanelHost/execute/Email/add_pop";
$postFields = http_build_query([
    'email'    => $username,
    'domain'   => $domain,
    'password' => $password,
    'quota'    => 1024
]);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $createUrl,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $postFields,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_RETURNTRANSFER => true,
]);
$createResponse = curl_exec($ch);
curl_close($ch);

$createData = json_decode($createResponse, true);
if (!$createData['status']) {
    http_response_code(500);
    $msg = isset($createData['errors']) ? implode(', ', $createData['errors']) : 'Unknown error';
    exit("Failed to create email: $msg");
}

echo 'success';
