<?php

/**
 * Tento soubor je součástí rozšíření "Nette Facebook Connect"
 * @link https://github.com/illagrenan/nette-facebook-connect
 * 
 * Copyright (c) 2013 Václav Dohnal, http://www.vaclavdohnal.cz
 */

namespace Illagrenan\Facebook\DI;

use Nette;
use Nette\Config\Configurator;

class FacebookConnectExtension extends Nette\Config\CompilerExtension
{

    /**
     * @var array 
     */
    public $defaults = array(
        'appName'      => FALSE,
        'description'  => FALSE,
        'scope'        => FALSE,
        'appId'        => FALSE,
        'secret'       => FALSE,
        'appNamespace' => FALSE,
        'canvasUrl'    => FALSE,
        'tabUrl'       => FALSE
    );

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config  = $this->getConfig($this->defaults);

        $fbConnectParams = array(
            $config,
            '@application',
            '@httpResponse'
        );

        $builder->addDefinition($this->prefix('client'))
                ->setClass('\Illagrenan\Facebook\FacebookConnect', $fbConnectParams);
    }

    /**
     * @param \Nette\Config\Configurator $configurator
     */
    public static function register(Nette\Config\Configurator $configurator)
    {
        $configurator->onCompile[] = function (Configurator $config, Nette\Config\Compiler $compiler)
                {
                    $compiler->addExtension('facebookConnect', new FacebookConnectExtension());
                };
    }

}