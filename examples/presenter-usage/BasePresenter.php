<?php

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

    /**
     * @var \Illagrenan\Facebook\FacebookConnect
     */
    protected $facebookConnect;

    /**
     * @param \Illagrenan\Facebook\FacebookConnect $facebookConnect
     */
    public function injectFacebookConnectClient(\Illagrenan\Facebook\FacebookConnect $facebookConnect)
    {

        $this->facebookConnect = $facebookConnect;
    }

}
