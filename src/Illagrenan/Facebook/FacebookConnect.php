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

final class FacebookConnect extends \Facebook
{

    /**
     * @var \Nette\DI\Container
     */
    private $container;

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
     * @param \Nette\DI\Container $container
     */
    public function __construct(array $config, \Nette\DI\Container $container)
    {
        parent::__construct($config);

        $this->config    = $config;
        $this->container = $container;
    }

    /**
     * Povolí zápis cookies do IFRAMe a pokud má aplikace appNamespace, povolí vložení aplikace do IFRAMe 
     * @return void
     */
    public function setHeaders()
    {

        $this->container->httpResponse->addHeader('P3P', 'CP="CAO PSA OUR"');

        if ($this->config["appNamespace"] !== FALSE)
        {
            $this->container->httpResponse->setHeader('X-Frame-Options', NULL);
        }
    }

    /**
     * @return void
     */
    public function login()
    {
        $loginUrl = $this->getLoginUrl();
        IframeRedirect::redirectUrl($loginUrl);
    }

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
     * @param string $redirectUri
     * @throws \Nette\InvalidArgumentException
     * @return void
     */
    public function setRedirectUri($redirectUri)
    {
        if (FALSE === Validators::isUrl($redirectUri))
        {
            throw new \Nette\InvalidArgumentException($redirectUri . " is not valid URL.");
        }

        $this->redirectUri = $redirectUri;
    }

    /**
     * @return mixed
     */
    public function getMe()
    {
        return $this->api("/me/");
    }

    /**
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
     * @return \Illagrenan\Facebook\FacebookUser
     * @throws NotConnectedException
     */
    public function getFacebookUser()
    {

        if (FALSE === $this->isLoggedIn())
        {
            throw new NotConnectedException("Could not get user data. User is not connected.");
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
