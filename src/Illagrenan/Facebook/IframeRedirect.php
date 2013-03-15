<?php

/**
 * Tento soubor je součástí rozšíření "Nette Facebook Connect"
 * @link https://github.com/illagrenan/nette-facebook-connect
 * 
 * Copyright (c) 2013 Václav Dohnal, http://www.vaclavdohnal.cz
 */

namespace Illagrenan\Facebook;

use Nette\Utils\Validators;
use Nette\Templating\FileTemplate;

/**
 * Redirect JavaScriptem (naše aplikace běží na apps.facebook.com).
 * 
 * @author Vašek Dohnal http://www.vaclavdohnal.cz
 */
final class IframeRedirect extends \Nette\Object
{

    /**
     * @var string
     */
    private static $TEMPLATE_DIR = "templates";

    private function __construct()
    {
        
    }

    /**
     * Redirects using JavaScript.
     * @param string $url 
     * @return void
     */
    public static function redirectUrl($url)
    {
        if (FALSE === Validators::isUrl($url))
        {
            throw new \Nette\InvalidArgumentException($url . " is not valid URL.");
        }

        $template = new FileTemplate(dirname(__FILE__) . '/' . self::$TEMPLATE_DIR . '/iframeRedirect.latte');

        $template->registerHelperLoader('Nette\Templating\Helpers::loader');
        $template->registerFilter(new \Nette\Latte\Engine);
        $template->url = $url;
        $template->render();
        exit;
    }

}