<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>LINE Login v2.1 Sample Callback</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>
<body>
  <div class="container">
    <h1>Logined.</h1>
<?php

require_once __DIR__ . '/vendor/autoload.php';

$unsafe = $_SERVER['REQUEST_METHOD'] == 'POST'
       || $_SERVER['REQUEST_METHOD'] == 'PUT'
       || $_SERVER['REQUEST_METHOD'] == 'DELETE';

$session_factory = new \Aura\Session\SessionFactory;
$session = $session_factory->newInstance($_COOKIE);
$csrf_value = $_GET['state'];
$csrf_token = $session->getCsrfToken();
if ($unsafe || !$csrf_token->isValid($csrf_value)) {
  return;
}

$callback = 'https://' . $_SERVER['HTTP_HOST']  . '/line_callback.php';
if (isset($_GET['code'])) {
  $url = 'https://api.line.me/oauth2/v2.1/token';
  $data = array(
    'grant_type' => 'authorization_code',
    'client_id' => getenv('LOGIN_CHANNEL_ID'),
    'client_secret' => getenv('LOGIN_CHANNEL_SECRET'),
    'code' => $_GET['code'],
    'redirect_uri' => $callback
  );
  $data = http_build_query($data, '', '&');
  $header = array(
    'Content-Type: application/x-www-form-urlencoded'
  );
  $context = array(
    'http' => array(
      'method'  => 'POST',
      'header'  => implode('\r\n', $header),
      'content' => $data
    )
  );
  $resultString = file_get_contents($url, false, stream_context_create($context));
  $result = json_decode($resultString, true);

  if(isset($result['access_token'])) {
    $url = 'https://api.line.me/v2/profile';
    $context = array(
      'http' => array(
      'method'  => 'GET',
      'header'  => 'Authorization: Bearer '. $result['access_token']
      )
    );
    $profileString = file_get_contents($url, false, stream_context_create($context));
    $profile = json_decode($profileString, true);
    echo '<img style="width:100%" src="' . htmlspecialchars($profile["pictureUrl"], ENT_QUOTES) . '" />';
    echo '<h2>displayName</h2>';
    echo '<p class="text-muted">' . htmlspecialchars($profile["displayName"], ENT_QUOTES) . '</p>';
    echo '<h2>userId</h2>';
    echo '<p class="text-muted">' . htmlspecialchars($profile["userId"], ENT_QUOTES) . '</p>';
    echo '<h2>accessToken</h2>';
    echo '<p class="text-muted">' . $result['access_token'] . '</p>';

    if(isset($result['id_token'])) {
      $val = explode(".", $result['id_token']);
      $data_json = base64UrlDecode($val[1]);
      echo '<h2>ID_TOKEN</h2>';
      echo '<p class="text-muted">' . $data_json . '</p>';
    }
  }
}
else {
  echo '<p>Login Failed.</p>';
}

function base64UrlDecode($data) {
    $replaced = str_replace(array('-', '_'), array('+', '/'), $data);
    $lack = strlen($replaced) % 4;
    if ($lack > 0) {
        $replaced .= str_repeat("=", 4 - $lack);
    }
    return base64_decode($replaced);
}
?>
</div>
</body>
</html>
