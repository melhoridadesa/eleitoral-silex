<?php

#Chama as classes necessarias para utilização

use Silex\Provider\MonologServiceProvider,
    Silex\Provider\TwigServiceProvider,
    Silex\Provider\SecurityServiceProvider,
    Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder,
    Silex\Provider\UrlGeneratorServiceProvider;

#config do sistema
define('ROOT', dirname(__DIR__));

#chamada do autoloader
$loader = require ROOT . "/vendor/autoload.php";

#Cria instancia objeto app Silex
$app = new Silex\Application();

#Registra o objeto para criação de URL
$app->register(new UrlGeneratorServiceProvider());

#ativa o debug
$app['debug'] = true;

#cache
$app->register(new Silex\Provider\HttpCacheServiceProvider(), ['http_cache.cache_dir' => ROOT . '/storage/temp/http']);

# autoloader
$app['autoloader'] = $app->share(function()use($loader) {
    return $loader;
});

# Adiciona ao autoloader a chamada
$app['autoloader']->add("app", ROOT);

#Registra a pasta de layout do sistema
$app->register(new TwigServiceProvider(), array(
    'twig.options' => [
        'cache' => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true
    ],
    'twig.form.templates' => array('form_div_layout.html.twig', 'layouts/layout.tpl'),
    'twig.path' => array(ROOT . '/app/views/')
));

#Armazena os logs de erro
$app->register(new MonologServiceProvider(), [
    'monolog.logfile' => ROOT . '/storage/log/app.log',
    'monolog.name' => 'app',
    'monolog.level' => 300
]);

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

#Chamada de arquivos necessarios
require_once  __DIR__.'/services/services.php';
require_once 'routes.php';

#Retorna app para utilização
return $app;
