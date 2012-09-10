<?php

namespace Illagrenan\Facebook;

use Nette\Utils\Validators;
use Nette\Templating\FileTemplate;

final class IframeRedirect extends \Nette\Object
{

    /**
     * @var string
     */
    private static $TEMPLATE_DIR = "../templates";

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