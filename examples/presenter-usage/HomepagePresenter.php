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

    public function handleFacebookLogin()
    {
        $this->context->facebook->login();
    }
    
    public function handleFacebookLogout()
    {
        $this->context->facebook->logout();
    }

}
