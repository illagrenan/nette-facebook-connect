# Facebook Connect for nette (v0.0.1)

## 0. Changelog
* 2.9.2012 - verze 0.0.1

### TODOs
1. Podpora pro aplikace v záložce Facebook stránky
2. `FacebookConnect::setRedirectUri()` a `IframeRedirect::redirectUrl()` by měly přijímat jako parametr nette zápis odkazů
3. Komponenty pro vytváření dialogů (Wallpost, Request...) pomocí Facebook JS SDK (podobně jako [FBTools](http://addons.nette.org/cs/fb-tools)?)
4. Vytvořit Facebook [autentikátor](http://doc.nette.org/cs/security#toc-vlastni-autentikator), aby bylo možné používat "nette-way" přihlašování uživatelů
5. Značné vylepšení dokumentace.

### Známé problémy
1. Metoda `FacebookConnect::getLoginUrl` resp. její předek v knihovně generuje a do session ukládá CSFR token, kterým brání přihlášení. Pokud zavoláme `getLoginUrl()` na jedné stránce dvakrát a uživatel se pokusí přihlásit přes odkaz, který byl vygenerovaný jako první, autorizace selže. Knihovna totiž považuje vždy poslední vygenerovaný přihlašovací odkaz (resp. k němu přiřazený CSFR token) za validní.

## 1. Představení
### Funkce doplňku
* Integrace [Facebook PHP SDK](https://github.com/facebook/facebook-php-sdk) do nette
	* Verze Facebook SDK: 3.2.0
	* Verze nette 2.0.5
* Umožňuje přihlášení uživatelů pomocí Facebooku, pomocí doplňku tedy můžete vytvořit:
	* Klasický web s Facebook Connectem
	* Canvas aplikaci, běžící na apps.facebook.com
	* _Aplikaci v záložce na Facebook stránce_ **(zatím nepodporováno)**

### Co budete potřebovat?
1. Developer účet na Facebooku, viz: [facebook.com/developers](https://www.facebook.com/developers)
2. Pro produkční nasazení aplikace (tedy Facebook Connect webu se toto netýká) je **nezbytné vlastnit SSL certifikát** (aplikace běží pod `https://`) 

### Zdroje informací a odkazy
* [developers.facebook.com/docs/](https://developers.facebook.com/docs/) - Oficiální dokumentace
* [developers.facebook.com/docs/guides/canvas/](https://developers.facebook.com/docs/guides/canvas/) - Představení aplikací na Facebooku
* [developers.facebook.com/apps](https://developers.facebook.com/apps) - Založení a správa aplikací
* [developers.facebook.com/docs/reference/php/](http://developers.facebook.com/docs/reference/php/) - PHP SDK Dokumentace
* [developers.facebook.com/docs/appsonfacebook/tutorial/](https://developers.facebook.com/docs/appsonfacebook/tutorial/) - Tutoriál autoriace canvas aplikace

<br>

* [Zdroják.cz - Aplikace pro Facebook od základů - díl I.](http://www.zdrojak.cz/clanky/aplikace-pro-facebook-od-zakladu-dil-i/)
* [Zdroják.cz - Aplikace pro Facebook, díl II. - autorizace](http://www.zdrojak.cz/clanky/aplikace-pro-facebook-dil-ii-autorizace/)

*Disclaimer: Jsem spoluautor tutoriálů na Zdrojáku.*.

### Podpora

> Doplněk je v současné době funkční, nicméně na počátku svého vývoje. Počítejte tedy, prosím, s možnými bugy a změnami.

Na nette fóru se nevyskytuji, nechť tedy jako podpora slouží GitHub a event. můj mail `info (a) vaclavdohnal.cz`.

## 2. Instalace
Stáhněte zdrojový kód do své nette aplikace buď pomocí GITu nebo jako ZIP. Nezapomeňte, aby se k doplňku dostal [RobotLoader](http://doc.nette.org/cs/auto-loading).

Volitelně můžete doplněk nainstalovat [pomocí Composeru](http://doc.nette.org/cs/composer):

#### `composer.json`
```json
	{
		"minimum-stability": "dev",
	    "require": {
	        "illagrenan/nette-facebook-connect": "dev-master"
	    }
	}
```
```bash
$ php composer.phar install
```

> [Stránka doplňku na packagist.com](http://packagist.org/packages/illagrenan/nette-facebook-connect)

## 3. Konfigurace
1. Ve složce `app/config`, vytvořte nový adresář `facebook`
2. Upravte `config.neon`, aby načítal konfigurační soubory podle aktuálního prostředí:

#### `config.neon`
```yml
	common:
		includes:
			- facebook/facebook.neon

	production < common:
		includes:
			- facebook/facebook_production.neon

	development < common:
		includes:
			- facebook/facebook_dev.neon

```
Soubory `facebook.neon`, `facebook_production.neon` a `facebook_dev.neon` naleznete ve složce `install/config`.

### Popis konfiguračních souborů
<table>
	<tr>
		<th>app_name (string; nepovinné)</th>
		<td>Název aplikace - použití pro requesty, wallpost apod.</td>
	</tr>
	<tr>
		<th>description (string; nepovinné)</th>
		<td>Popis aplikace - použití pro requesty, wallpost apod.</td>
	</tr>
	<tr>
		<th>scope (string,string,...; nepovinné)</th>
		<td>Extended permissions pro aplikaci, viz http://developers.facebook.com/docs/authentication/permissions/#extended_perms</td>
	</tr>
	<tr>
		<th>app_id (int; povinné)</th>
		<td>ID aplikace, které získáte po registraci na facebook.com/developers</td>
	</tr>
	<tr>
		<th>app_secret (int; povinné)</th>
		<td>SECRET KEY aplikace, které získáte po registraci na facebook.com/developers</td>
	</tr>
	<tr>
		<th>app_namespace (string; nepovinné)</th>
		<td>V případě, že app_namespace nevyplníte, poběží doplněk v režimu Facebook Connect (místo Facebook App). Přihlašovací Facebook URL tedy nebude přesměrovávat na apps.facebook.com/app-namespace ale na www.my-canvas-page.com.</td>
	</tr>
	<tr>
		<th>canvas_url (string, povinné)</th>
		<td>URL vaší aplikace (webu).</td>
	</tr>
	<tr>
		<th>tab_url (string, nepovinné)</th>
		<td>URL na aplikaci v případě, že je vložená na Facebook PAGE. Tato funkcionalita zatím není podporována.</td>
	</tr>
</table>

## 4. Registrace služby

Doplněk zaregistrujte jako novou [službu](http://doc.nette.org/cs/configuring#toc-definice-sluzeb) v `config.neon` do [systémového kontejneru](http://doc.nette.org/cs/dependency-injection):

#### `config.neon`
```yml
common:
		# ...
		services:

			# ...

			facebook:
				class: \Illagrenan\Facebook\FacebookConnect([appId: %facebook.app_id%, secret: %facebook.app_secret%],@container)
				setup:
					- setHeaders()

			# ...

```

`setHeaders()` - Pakliže používáte doplněk pro autorizaci klasické Facebook aplikace (tedy na apps.facebook.com), nastaví metoda hlavičky pro a) funkčnost cookies v IFRAMe pro IE, b) povolí vložení celého webu (aka aplikace) do IFRAMe. V případě, že vytváříte web s Facebook Connect - můžete volání metody zakomentovat.

## 3. Použití

#### `HomepagePresenter.php`
```php
<?php

	use Nette\Diagnostics\Debugger;

	/**
	 * Homepage presenter.
	 */
	class HomepagePresenter extends BasePresenter
	{

	    public function renderDefault()
	    {
	        /* @var $fb Illagrenan\Facebook\FacebookConnect */
	        $fb = $this->context->facebook;

	        // Autorizoval uživatel naši aplikaci?
	        if ($fb->isLoggedIn() === FALSE)
	        {
	            // Volitelně můžeme změnit URL, na kterou bude uživatel z Facebooku navrácen
	            $redirectUri = $this->link("//Homepage:default");
	            $fb->setRedirectUri($redirectUri);

	            // Přihlásíme ho přesměrováním na Login_URL
	            $fb->login();
	        }
	        else // Uživatel je přihlášený v aplikaci
	        {
	            /* @var $user Illagrenan\Facebook\FacebookUser */
	            $user = $this->template->user = $fb->getFacebookUser();
	            Debugger::dump($user);
	        }
	    }

	    /**
	     * Přesměruje uživatele na přihlašovací stránku aplikace (na facebook.com)
	     */
	    public function handleFacebookLogin()
	    {
	        $this->context->facebook->login();
	    }
	    
	    /**
	     * Odhlásí uživatele z aplikace A z Facebooku
	     */
	    public function handleFacebookLogout()
	    {
	        $this->context->facebook->logout();
	    }

	}
```

#### `default.latte`
```html
	{block #content}
	    <h1>Nette FacebookConnect</h1>

	    <table>
	        <tr>
	            <td>
	                <a href="{link facebookLogin!}">
	                    Facebook login
	                </a>
	            </td>
	            <td>
	                <a href="{link facebookLogout!}">
	                    Facebook logout
	                </a>
	            </td>
	        </tr>
	    </table>

	    {ifset $user}
	        <hr>
	        <p>
	            <strong>
	                Ahoj {$user->getFirstName()}, jak se dnes měl
	                {if $user->getGender() == "female"}
	                a
	                {/if}        
	            </strong>
	        </p>
	    {/ifset}
	{/block}
```

## 5. Licence
Copyright (c) 2012, Václav Dohnal (http://www.vaclavdohnal.cz)
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
Neither the name of the <ORGANIZATION> nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

##### Facebook PHP SDK (v.3.2.0)
The Facebook Platform is a set of APIs that make your app more social.

This repository contains the open source PHP SDK that allows you to access Facebook Platform from your PHP app. Except as otherwise noted, the Facebook PHP SDK is licensed under the Apache Licence, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0.html).