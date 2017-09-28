<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>LINE Login v2.1 Sample</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <h1>LINE Login v2.1 Sample</h1>
<?php

require_once __DIR__ . '/vendor/autoload.php';

$session_factory = new \Aura\Session\SessionFactory;
$session = $session_factory->newInstance($_COOKIE);
$segment = $session->getSegment('Some\Package');

$csrf_value = $session->getCsrfToken()->getValue();

$callback = urlencode('https://' . $_SERVER['HTTP_HOST']  . '/line_callback.php');

$url = 'https://access.line.me/oauth2/v2.1/authorize?scope=profile&response_type=code&client_id=' . getenv('LOGIN_CHANNEL_ID') . '&redirect_uri=' . $callback . '&state=' . $csrf_value;
echo '<a href=' . $url . ' class="btn btn-primary btn-block">Login v2.1</a>' . PHP_EOL;

$url = 'https://access.line.me/oauth2/v2.1/authorize?scope=profile&prompt=consent&bot_prompt=normal&response_type=code&client_id=' . getenv('LOGIN_CHANNEL_ID') . '&redirect_uri=' . $callback . '&state=' . $csrf_value;
echo '<a href=' . $url . ' class="btn btn-primary btn-block">Add Friend(Normal)</a>' . PHP_EOL;
echo '<p class="text-muted text-center">Show add friend option on granting permission page</p>';

$url = 'https://access.line.me/oauth2/v2.1/authorize?scope=profile&prompt=consent&bot_prompt=aggressive&response_type=code&client_id=' . getenv('LOGIN_CHANNEL_ID') . '&redirect_uri=' . $callback . '&state=' . $csrf_value;
echo '<a href=' . $url . ' class="btn btn-primary btn-block">Add Friend(Aggressive)</a>' . PHP_EOL;
echo '<p class="text-muted text-center">Show add friend option after granting permission</p>';

$url = 'https://access.line.me/oauth2/v2.1/authorize?scope=openid%20profile&response_type=code&client_id=' . getenv('LOGIN_CHANNEL_ID') . '&redirect_uri=' . $callback . '&state=' . $csrf_value;
echo '<a href=' . $url . ' class="btn btn-primary btn-block">Login using OpenID Connect</a>' . PHP_EOL;
echo '<p class="text-muted text-center">User data will be passed in ID_TOKEN</p>';

$url = 'https://access.line.me/oauth2/v2.1/authorize?scope=profile&prompt=consent&response_type=code&client_id=' . getenv('LOGIN_CHANNEL_ID') . '&redirect_uri=' . $callback . '&state=' . $csrf_value;
echo '<a href=' . $url . ' class="btn btn-primary btn-block">Force Consent Screen</a>' . PHP_EOL;
echo '<p class="text-muted text-center">No matter if user has agreed</p>';

?>
</div>
</body>
</html>
