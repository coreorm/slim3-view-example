<?php
use Slim\App;
use Coreorm\Slim3\Theme;

$app = new App(['settings' => ['displayErrorDetails' => true]]);

$conf = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

$theme = Theme::instance(__DIR__ . '/themes');
$theme->setLayout('page');
foreach ($conf as $k => $v) {
    $theme->setData($k, $v);
}

// now set shared resources (notice the theme is omitted as it defaults to 'default')
$theme->share('layouts/default')
    ->share('views/bits/header')
    ->share('views/bits/nav')
    ->share('views/pages/about')
    ->share('views/pages/homepage');

// default pages
$pages = [];

// current path
$theme->setData('currentURL', $_SERVER['REQUEST_URI']);

// read src
$src = [];
foreach ($conf['reader'] as $pc) {
    $key = $pc[1];
    $file = $pc[0];
    $src[$key] = '# FILE: ' . $file . PHP_EOL . PHP_EOL . htmlentities(file_get_contents(__DIR__ . $file));
    $theme->setData('src', $src);
}

$pages['/'] = function ($request, $response, $args) use ($theme) {
    $theme->setData('pageTitle', $theme->getData('pageTitle') . ' > Homepage')
        ->render($response, 'pages/homepage', [], true);
};

$pages['/about'] = function ($request, $response, $args) use ($theme) {
    $theme->setData('pageTitle', $theme->getData('pageTitle') . ' > About')
        ->render($response, 'pages/about', [], true);
};

$pages['/alt'] = function ($request, $response, $args) use ($theme, $pages) {
    $theme->setTheme('alternative');
    $pages['/']($request, $response, $args);
};

$pages['/alt/about'] = function ($request, $response, $args) use ($theme, $pages) {
    $theme->setTheme('alternative');
    $pages['/about']($request, $response, $args);
};

foreach ($pages as $route => $page) {
    $app->get($route, $page);
}

// run app
$app->run();
