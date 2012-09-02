# Facebook Connect for nette
> **Upozornění!** Doplněk ještě není připravený na ostré nasazení.

## 1. Instalace
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

## 2. Konfigurace
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

## 3. Registrace služby

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