<?php

/**
 * Tento soubor je součástí rozšíření "Nette Facebook Connect"
 * @link https://github.com/illagrenan/nette-facebook-connect
 * 
 * Copyright (c) 2013 Václav Dohnal, http://www.vaclavdohnal.cz
 */

namespace Illagrenan\Facebook;

use Nette\Utils\Validators;
use Nette\Diagnostics\Debugger;

/**
 * Třída rozšíření.
 * 
 * @author Vašek Dohnal http://www.vaclavdohnal.cz
 */
final class FacebookConnect extends \Facebook
{

    /**
     * @var \Nette\Http\Response
     */
    private $httpResponse;

    /**
     * @var \Nette\Application\Application
     */
    private $application;

    /**
     * @var string 
     */
    private $redirectUri;

    /**
     * @var \Illagrenan\Facebook\FacebookUser
     */
    private $loggedUser;

    /**
     * @var string
     */
    private $FBAPPS_URL_PREFIX = "http://apps.facebook.com/";

    /**
     * @var string
     */
    private $FBAPPS_URL_SUFFIX = "/";

    /**
     *
     * @var array
     */
    private $config;

    /**
     * @param array $config
     * @param \Nette\Application\Application $application
     * @param \Nette\Http\Response $httpResponse
     */
    public function __construct(array $config, \Nette\Application\Application $application, \Nette\Http\Response $httpResponse)
    {
        parent::__construct($config);

        $this->config       = $config;
        $this->application  = $application;
        $this->httpResponse = $httpResponse;

        $this->setHeaders();
    }

    /**
     * Povolí zápis cookies do IFRAMe a pokud má aplikace appNamespace, povolí vložení aplikace do IFRAMe
     */
    private function setHeaders()
    {
        $this->httpResponse->addHeader('P3P', 'CP="CAO PSA OUR"');

        if ($this->config["appNamespace"] !== FALSE)
        {
            $this->httpResponse->setHeader('X-Frame-Options', NULL);
        }
    }

    /**
     * Přesměruje uživatele na Facebook.com, kde bude ověřeno jeho přihlášení
     */
    public function login()
    {
        $loginUrl = $this->getLoginUrl();
        IframeRedirect::redirectUrl($loginUrl);
    }

    /**
     * Odhlásí uživatele z naší aplikace a z Facebook.com
     */
    public function logout()
    {
        $logoutUrl = $this->getLogoutUrl();
        IframeRedirect::redirectUrl($logoutUrl);
    }

    /**
     * @return array
     */
    private function getLoginParams()
    {
        $params                 = array();
        $params["redirect_uri"] = $this->getRedirectUri();
        $params["scope"]        = $this->config["scope"];
        return $params;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getLoginUrl($params = array())
    {
        $params   = array_merge($params, $this->getLoginParams());
        $loginUrl = parent::getLoginUrl($params);
        return $loginUrl;
    }

    /**
     * Vrátí absolutní adresu, na kterou je uživatel vrácen po Facebook autorizaci
     * @return string
     */
    public function getRedirectUri()
    {
        if (isset($this->redirectUri))
        {
            return $this->redirectUri;
        }

        if ($this->existsCanvasPage())
        {
            return $this->createCanvasPageUrl();
        }

        return $this->config["canvasUrl"];
    }

    /**
     * @return boolean
     */
    private function existsCanvasPage()
    {
        return (bool) $this->config["appNamespace"];
    }

    /**
     * @return string
     */
    private function createCanvasPageUrl()
    {
        $appNamespace = $this->config["appNamespace"];
        return self::$FBAPPS_URL_PREFIX . $appNamespace . self::$FBAPPS_URL_SUFFIX;
    }

    /**
     * Nastaví URL na kterou bude uživatel z Facebook.com přesměrován
     * Přijímá nette zápis odkazů (např.: Homepage:default) nebo absolutní URL
     * @param string $redirectUri
     * @throws \Nette\InvalidArgumentException
     * @return void
     */
    public function setRedirectUri($redirectUri)
    {
        /* @var $currentPresenter \Nette\Application\UI\Presenter */
        $currentPresenter = $this->application->getPresenter();

        $netteAbsoluteLinkPrexix = "//";

        try
        {
            if (\Nette\Utils\Strings::startsWith($redirectUri, "//"))
            {
                $netteAbsoluteLinkPrexix = "";
            }

            $absoluteUri = $currentPresenter->link($netteAbsoluteLinkPrexix . $redirectUri);

            if (\Nette\Utils\Strings::startsWith($absoluteUri, "error:"))
            {
                throw new \Nette\Application\UI\InvalidLinkException;
            }
        }
        catch (\Nette\Application\UI\InvalidLinkException $exc)
        {
            if (Validators::isUrl($redirectUri) === TRUE)
            {
                $absoluteUri = $redirectUri;
            }
            else
            {
                throw new Exceptions\InvalidRedirectUriException("Given \"" . $redirectUri . "\" is not valid absolute URL or nette link.");
            }
        }

        $this->redirectUri = $absoluteUri;
    }

    /**
     * Vyvolá Facebook API požadavek a vrátí pole dat aktuálně přihlášeného uživatele
     * @return mixed
     */
    public function getMe()
    {
        return $this->api("/me/");
    }

    /**
     * Je uživatel přihlášený?
     * @return boolean
     */
    public function isLoggedIn()
    {
        try
        {
            $this->getMe();
            return TRUE;
        }
        catch (\FacebookApiException $exc)
        {
            return FALSE;
        }
    }

    /**
     * Vrátí objekt aktuálně přihlášeného uživatele
     * @return FacebookUser
     * @throws Exceptions\NotConnectedException pokud není uživatel přihlášen
     */
    public function getFacebookUser()
    {

        if (FALSE === $this->isLoggedIn())
        {
            throw new Exceptions\NotConnectedException("Could not get user data. User is not connected.");
        }

        if ($this->loggedUser === NULL)
        {
            $me               = $this->getMe();
            return $this->loggedUser = $this->createNewUser($me);
        }
        else
        {
            return $this->loggedUser;
        }
    }

    /**
     * @param array $me
     * @return \Illagrenan\Facebook\FacebookUser
     */
    private function createNewUser(array $me)
    {
        $me["email"] = isset($me["email"]) ? $me["email"] : NULL;

        return new FacebookUser(
                $me["id"], $me["first_name"], $me["last_name"], $me["link"], $me["username"], $me["gender"], $me["locale"], $me["email"]
        );
    }

}
