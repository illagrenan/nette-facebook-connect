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
        'description'   => FALSE,
        'scope'         => FALSE,
        'appId'        => FALSE,
        'secret'    => FALSE,
        'appNamespace' => FALSE,
        'canvasUrl'    => FALSE,
        'tabUrl'       => FALSE
    );

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config  = $this->getConfig($this->defaults);
       
        $client = $builder->addDefinition($this->prefix('client'))
                ->setClass('\Illagrenan\Facebook\FacebookConnect', array(
                    $config,
                    '@container'
                ))
                ->addSetup('setHeaders');



        return;

        Nette\Diagnostics\Debugger::dump($config);
        die();


        $config = $this->getConfig(array(
            'common.facebookConnect' => 'facebookConnect'
        ));

        $builder = $this->getContainerBuilder();

        $api = $builder->addDefinition("fb")
                ->setClass('\Illagrenan\Facebook\FacebookConnect', array(
            array("appId"  => 123, "secret" => 123),
            '@container'
        ));

        /*
          if (isset($config['accessKey']) && isset($config['accessSecret']))
          {
          $api->addSetup('setOAuthToken', array(
          $config['accessKey'],
          $config['accessSecret']
          ));
          }

         */
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