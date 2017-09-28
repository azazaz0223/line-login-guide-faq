# LINE Login v2.1を体験するためのサンプルプロジェクトです。

事前に[LINE Devlopers](https://developers.line.me/ja/)にてLINE Loginのチャネルを作成して下さい。

# 解説

<img width="405" alt="line_login_v2.1" src="https://qiita-image-store.s3.amazonaws.com/0/164153/fd395b24-89f2-32f1-bd46-5355e6ed7afe.png">

本日発表されたLINE Login v2.1の特徴と使い方をまとめました。

## 概要
* 新しいエンドポイント
* OpenID Connectに対応
* Loginと同時にBOTと友だちにさせるオプションの追加
* 他のオプション

一つずつ確認していきましょう。

## 新しいエンドポイント
エンドポイントが変更になっています。

### 旧
```shell-session
https://access.line.me/dialog/oauth/weblogin
https://api.line.me/v2/oauth/accessToken
https://api.line.me/v2/oauth/revoke
```
### 新
```shell-session
https://acces.line.me/oauth2/v2.1/authorize
https://api.line.me/oauth2/v2.1/token
https://api.line.me/oauth2/v2.1/revoke
```
## OpenID Connectに対応

OpenIDに対応した事で、scopeパラーメーターにopenidを指定するとID_TOKENが得られるようになりました。

```php
$url = 'https://access.line.me/oauth2/v2.1/authorize?scope=openid%20profile&response_type=code&client_id=' . getenv('LOGIN_CHANNEL_ID') . '&redirect_uri=' . $callback . '&state=' . $csrf_value;
echo '<a href=' . $url . '><button class="btn">Login using OpenID Connect</button></a>' . PHP_EOL;
```

このような形でログインさせると、コールバック先でアクセストークンを取得する際にID_TOKENが得られます。

```php
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
　　 if(isset($result['id_token'])) {
      $val = explode(".", $result['id_token']);
      $data_json = base64UrlDecode($val[1]);
      error_log($data_json);
    }
  }

// 略

function base64UrlDecode($data) {
  $replaced = str_replace(array('-', '_'), array('+', '/'), $data);
  $lack = strlen($replaced) % 4;
  if ($lack > 0) {
      $replaced .= str_repeat("=", 4 - $lack);
  }
  return base64_decode($replaced);
}
```

```shell-session
{"iss":"https://access.line.me","sub":"userIdXXXXXXXXXXXXX","aud":"1508850331","exp":1506342586,"iat":1506338986,"name":"立花","picture":""}
```

[公式リファレンス](#)

## Loginと同時にBOTと友だちにさせるオプションの追加

Loginの際に、BOTのIDを指定して友だちに追加させるオプションが追加されました。

2通りの表示方法があり、bot_promptパラメーターにnormalを指定するとログインの権限確認画面上にチェックボタンが表示されます。

```php
$url = 'https://access.line.me/oauth2/v2.1/authorize?scope=profile&bot_prompt=normal&response_type=code&client_id=' . getenv('LOGIN_CHANNEL_ID') . '&redirect_uri=' . $callback . '&state=' . $csrf_value;
echo '<a href=' . $url . '><button class="btn">Login v2.1</button></a>' . PHP_EOL;
```
<img width="405" alt="line_login_v2.1" src="https://qiita-image-store.s3.amazonaws.com/0/164153/fd395b24-89f2-32f1-bd46-5355e6ed7afe.png">

bot_promptパラメーターにaggressiveを指定すると、権限確認画面で同意した後に追加ウィンドウが表示されます。

```php
$url = 'https://access.line.me/oauth2/v2.1/authorize?scope=profile&bot_prompt=aggressive&response_type=code&client_id=' . getenv('LOGIN_CHANNEL_ID') . '&redirect_uri=' . $callback . '&state=' . $csrf_value;
echo '<a href=' . $url . '><button class="btn">Login v2.1</button></a>' . PHP_EOL;
```

<img width="405" alt="line_login_v2.1" src="https://qiita-image-store.s3.amazonaws.com/0/164153/3f6f1b8f-2512-39c9-c51a-f5912a33c179.png">

本機能の実現には紐付けるBOTを選択する機能が必要なのですが、現在は徐々にホワイトリストベースで使えるようにしている段階です。近日中に誰でも使えるようになりますので今しばらくお待ち下さい。

## 他のオプション

promptオプションにconsentを指定することで既にユーザーが権限に同意し連携が済んでいても強制的に権限確認画面を表示させることが出来ます。

```php
$url = 'https://access.line.me/oauth2/v2.1/authorize?scope=profile&prompt=consent&response_type=code&client_id=' . getenv('LOGIN_CHANNEL_ID') . '&redirect_uri=' . $callback . '&state=' . $csrf_value;
echo '<a href=' . $url . '><button class="btn">Login v2.1</button></a>' . PHP_EOL;
```
