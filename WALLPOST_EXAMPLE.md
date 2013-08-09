# Jak poslat wallpost na zeď uživatele

Vaše aplikace musí po uživateli vyžadovat [extended_permission](https://developers.facebook.com/docs/reference/login/extended-permissions/) s povolením zasílání příspěvků na zeď. Do `config.neon` tedy přidejte:

```
scope: 'publish_stream'
```

Po úspěšné autorizaci uživatele vytvořte `attachment`, která bude poslána na zeď:

```php
$attachment = array(
                'message'     => "An Identicon is a visual representation of a hash value, usually of an IP address, that serves to identify a user of a computer system as a form of avatar while protecting the users' privacy. The original Identicon was a 9-block graphic, and the representation has been extended to other graphic forms by third parties.",
                'name'        => 'This is the url with Technical support',
                'caption'     => "A Globally Recognized Avatar",
                'link'        => 'http://cs.gravatar.com/support/',
                'description' => "Gravatar Support: Welcome! How can we help you with Gravatar?",
                'picture'     => 'http://www.gravatar.com/avatar/94d093eda664addd6e450d7e9881bcad?s=256&d=identicon&r=PG'
);
```

Tu následně přes standardní API odešleme:

```php
// Pozn.: "me" značí "na zeď aktuálně přihlášeného uživatele", je ale možné uvést i FACEBOOK_ID kamaráda
try
{
	$wallpostStatus = $this->facebookConnect->api('/me/feed', 'POST', $attachment);
	Debugger::dump($wallpostStatus);
}
catch (FacebookApiException $e)
{
	// @todo Handle error
}
```

V proměnné `$wallpostStatus` se buď nachází zpráva o chybě nebo v případě úspěchu ID vytvořeného wallpostu:

```php
array(1) {
   id => "1224987137_1020175085xxxxxxx" (28)
}
```

Výsledek na Facebooku:

![](https://raw.github.com/illagrenan/nette-facebook-connect/master/images/wallpost_example.png)