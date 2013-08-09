# Facebook Connect pro nette

```json
	{
	    "require": {
	        "illagrenan/nette-facebook-connect": "dev-master"
	    }
	}
```

* [Stránka doplňku na addons.nette.org](http://addons.nette.org/cs/facebook-connect-for-nette)
* [Diskuze na forum.nette.org](http://forum.nette.org/cs/12105-facebook-connect-pro-nette)


## Závislosti
* **Facebook SDK v3.2.0**
* **nette v2.0.x** pro PHP 5.3 nebo 5.4 bez prefixů
* Požadavky na PHP: přítomné **rozšíření cURL**


## 0. Changelog
* 9.8.2013 - Přidána [ukázka Wallpostu](WALLPOST_EXAMPLE.md)
* 15.3.2013 - verze 0.0.2 (BC break!)
* 2.9.2012 - verze 0.0.1

### TODOs
1. Podpora pro aplikace v záložce Facebook stránky
2. `FacebookConnect::setRedirectUri()` a `IframeRedirect::redirectUrl()` by měly přijímat jako parametr nette zápis odkazů
3. Vytvořit Facebook [autentikátor](http://doc.nette.org/cs/security#toc-vlastni-autentikator), aby bylo možné používat "nette-way" přihlašování uživatelů

### Známé problémy
1. Metoda `FacebookConnect::getLoginUrl` resp. její předek v knihovně generuje a do session ukládá CSFR token, kterým zabezpečuje přihlášení. Pokud zavoláme `getLoginUrl()` na jedné stránce dvakrát a uživatel se pokusí přihlásit přes odkaz, který byl vygenerovaný jako první, autorizace selže. Knihovna totiž považuje vždy poslední vygenerovaný přihlašovací odkaz (resp. k němu přiřazený CSFR token) za validní.

## 1. Představení
### Funkce doplňku
* Integrace [Facebook PHP SDK](https://github.com/facebook/facebook-php-sdk) do nette
* Umožňuje přihlášení uživatelů pomocí Facebooku, pomocí doplňku tedy můžete vytvořit:
	* Klasický web s Facebook Connectem
	* Canvas aplikaci, běžící na apps.facebook.com
	* _Aplikaci v záložce na Facebook stránce_ **(zatím nepodporováno)**

### Co budete potřebovat?
1. Developer účet na Facebooku, viz: [facebook.com/developers](https://www.facebook.com/developers)
2. Pro produkční nasazení aplikace (tedy Facebook Connect webu se toto netýká) je **nezbytné vlastnit SSL certifikát** (aplikace běží pod `https://`) 

### Facebook PHP SDK - Dokumentace a zdroje
* [developers.facebook.com/docs/](https://developers.facebook.com/docs/) - Oficiální dokumentace
* [developers.facebook.com/docs/guides/canvas/](https://developers.facebook.com/docs/guides/canvas/) - Představení aplikací na Facebooku
* [developers.facebook.com/apps](https://developers.facebook.com/apps) - Založení a správa aplikací
* [developers.facebook.com/docs/reference/php/](http://developers.facebook.com/docs/reference/php/) - PHP SDK Dokumentace
* [developers.facebook.com/docs/appsonfacebook/tutorial/](https://developers.facebook.com/docs/appsonfacebook/tutorial/) - Tutoriál autoriace canvas aplikace

#### Ostatní zdroje
* [Zdroják.cz - Aplikace pro Facebook od základů - díl I.](http://www.zdrojak.cz/clanky/aplikace-pro-facebook-od-zakladu-dil-i/)
* [Zdroják.cz - Aplikace pro Facebook, díl II. - autorizace](http://www.zdrojak.cz/clanky/aplikace-pro-facebook-dil-ii-autorizace/)

*Disclaimer: Jsem spoluautor tutoriálů na Zdrojáku.*

## 2. Instalace
Preferovaný způsob instalace [pomocí Composeru](http://doc.nette.org/cs/composer):

#### `composer.json`
```json
	{
	    "require": {
	        "illagrenan/nette-facebook-connect": "dev-master"
	    }
	}
```
```bash
$ php composer.phar install
```

> [Stránka doplňku na packagist.com](http://packagist.org/packages/illagrenan/nette-facebook-connect)

## 3. Konfigurace a registrace

Kód naleznete v `examples/configure-and-load-extension`.

#### `config.neon`
```yml
common:

	# ...

	facebookConnect:
	    appName: 'Facebook Connect for nette'
	    # appID a secret: https://developers.facebook.com/apps
	    appId: '2262xxxxxx'
	    secret: 'ea2cxxxxxxxxxxaa32'
	    canvasUrl: 'http://myapp.local/path/to/your/app'
	    description: 'Vítr skoro nefouká a tak by se na první pohled mohlo zdát'
	    scope: 'email,user_likes'

	# ...
```

#### `bootstrap.php`
```php
\Illagrenan\Facebook\DI\FacebookConnectExtension::register($configurator);
```

#### `BasePresenter.php` - získání služby
```php
<?php
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{     
    /**
     * @var \Illagrenan\Facebook\FacebookConnect
     */
    protected $facebookConnect;
     
    public function injectFacebookConnectClient(\Illagrenan\Facebook\FacebookConnect $facebookConnect)
    {        
        $this->facebookConnect = $facebookConnect;
    }

}
?>
```

### Popis konfiguračních souborů
<table>
	<thead>
		<tr>
			<th>Klíč</th>
			<th>Typ</th>
			<th>Popis</th>
		</tr>

	</thead>

	<tr>
		<th>appName</th>
		<td>string; nepovinné</td>
		<td>Název aplikace pro použití v Requestech, Wallpostech atd.</td>
	</tr>
	<tr>
		<th>appId</th>
		<td>int; povinné</td>
		<td>ID aplikace, které získáte po registraci na facebook.com/developers</td>
	</tr>
	<tr>
		<th>secret</th>
		<td>int; povinné</td>
		<td>SECRET KEY aplikace, které získáte po registraci na facebook.com/developers</td>
	</tr>
	<tr>
		<th>canvasUrl</th>
		<td>string; povinné</td>
		<td>URL vaší aplikace (webu).</td>
	</tr>
	<tr>
		<th>appNamespace</th>
		<td>string; nepovinné</td>
		<td>V případě, že app_namespace nevyplníte, poběží doplněk v režimu Facebook Connect (místo Facebook App). Přihlašovací Facebook URL tedy nebude přesměrovávat na apps.facebook.com/app-namespace ale na www.my-canvas-page.com.</td>
	</tr>
	<tr>
		<th>description</th>
		<td>string; nepovinné</td>
		<td>Popis aplikace pro stejné použití jako app_name. Tyto dvě položky aktuálně nejsou povinné, nicméně s postupným rozšířováním knihovny o generování Requestů a Wallpostů se budou hodit.</td>
	</tr>
	<tr>
		<th>scope</th>
		<td>string, string, string....; nepovinné</td>
		<td>Extended permissions pro aplikaci, viz http://developers.facebook.com/docs/authentication/permissions/#extended_perms</td>
	</tr>
</table>

## 4. Použití

Kód naleznete v `examples/presenter-usage`.

#### `HomepagePresenter.php`
```php
<?php

use Nette\Diagnostics\Debugger;

class HomepagePresenter extends BasePresenter
{

    public function renderDefault()
    {
        // Autorizoval uživatel naši aplikaci?
        if ($this->facebookConnect->isLoggedIn() === FALSE)
        {
            // Volitelně můžeme změnit URL, na kterou bude uživatel z Facebooku navrácen
            // Přijímá buď nette zápis odkazů nebo absolutní URL
            $this->facebookConnect->setRedirectUri("Homepage:default");


            // Přihlásíme ho přesměrováním na Login_URL
            $this->facebookConnect->login();
        }
        else // Uživatel je přihlášený v aplikaci
        {
            /* @var $user Illagrenan\Facebook\FacebookUser */
            $user                 = $this->facebookConnect->getFacebookUser();
            $this->template->user = $user;

            Debugger::dump($user);
        }
    }

    /**
     * Přesměruje uživatele na přihlašovací stránku aplikace (na facebook.com)
     */
    public function handleFacebookLogin()
    {
        $this->facebookConnect->login();
    }

    /**
     * Odhlásí uživatele z aplikace A z Facebooku
     */
    public function handleFacebookLogout()
    {
        $this->facebookConnect->logout();
    }

}
?>
```

#### `default.latte`

Kód naleznete v `examples/latte-usage`.

```html
{block #content}
    <h1>Nette FacebookConnect</h1>

    {ifset $user}
    <p>
        <strong>
            Ahoj {$user->getFirstName()}, jak se dnes měl
            {if $user->getGender() == \Illagrenan\Facebook\UserGender::FEMALE}a{/if}
        </strong>
    </p>
    {/ifset}

    <ul>
        <li><a href="{link facebookLogin!}">Přihlásit se do aplikace Facebookem</a></li>
        <li><a href="{link facebookLogout!}">Odhlásit se z aplikace (ale i z Facebooku)</a></li>
    </ul>
{/block}
```

## 6. Licence
Copyright (c) 2013, Václav Dohnal (http://www.vaclavdohnal.cz)
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
Neither the name of the <ORGANIZATION> nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

##### Facebook PHP SDK (v.3.2.0)
The Facebook Platform is a set of APIs that make your app more social.

This repository contains the open source PHP SDK that allows you to access Facebook Platform from your PHP app. Except as otherwise noted, the Facebook PHP SDK is licensed under the Apache Licence, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0.html).
