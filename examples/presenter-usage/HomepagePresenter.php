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
