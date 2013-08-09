<?php

// Load Nette Framework
require LIBS_DIR . '/autoload.php';

// ...
// Configure application
$configurator = new Nette\Config\Configurator;

\Illagrenan\Facebook\DI\FacebookConnectExtension::register($configurator);

// ...
// Configure and run the application!
$container->application->run();

