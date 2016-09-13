<?php
use Slim\App;
use Coreorm\Slim3\Theme;

$app = new App(['settings' => ['displayErrorDetails' => true]]);

$theme = Theme::instance(__DIR__ . '/themes');

// set default layout & default title & navigation
$nav = [
    [
        'icon' => 'glyphicon glyphicon-home',
        'class' => '',
        'label' => 'home',
        'url' => '/'
    ],
    [
        'icon' => 'glyphicon glyphicon-info-sign',
        'class' => '',
        'label' => 'about',
        'url' => '/about'
    ],
    [
        'icon' => 'glyphicon glyphicon-home',
        'class' => '',
        'label' => 'home (alternative)',
        'url' => '/alt'
    ],
    [
        'icon' => 'glyphicon glyphicon-info-sign',
        'class' => '',
        'label' => 'about (alternative)',
        'url' => '/alt/about'
    ]
];

$theme->setLayout('page')
    ->setData('pageTitle', 'Slim3 View Example')
    ->setData('nav', $nav);

// default pages
$pages = [];

// current path
$theme->setData('currentURL', $_SERVER['REQUEST_URI']);

$src = [];
/**
 * quick function for reading src
 * @param $file
 * @param $key
 */
$reader = function ($file, $key) use ($theme, &$src) {
    $src[$key] = '# FILE: ' . $file . PHP_EOL . PHP_EOL . htmlentities(file_get_contents(__DIR__ . $file));
    $theme->setData('src', $src);
};

$reader('/app.php', 'app.php');
$reader('/themes/default/layouts/default.phtml', 'layout/default');
$reader('/themes/default/views/pages/homepage.phtml', 'homepage');
$reader('/themes/default/views/pages/about.phtml', 'about');
$reader('/themes/alternative/views/pages/homepage.phtml', 'homepage (alternative)');
$reader('/themes/alternative/views/pages/about.phtml', 'about (alternative)');

$pages['/'] = function ($request, $response, $args) use ($theme, $nav) {
    $theme->setData('nav', $nav)
        ->setData('pageTitle', $theme->getData('pageTitle') . ' > Homepage')
        ->render($response, 'pages/homepage');
};

$pages['/about'] = function ($request, $response, $args) use ($theme, $nav) {
    $theme->setData('nav', $nav)
        ->setData('pageTitle', $theme->getData('pageTitle') . ' > About')
        ->render($response, 'pages/about');
};

$pages['/alt'] = function ($request, $response, $args) use ($theme, $nav, $pages) {
    $theme->setTheme('alternative');
    $pages['/']($request, $response, $args);
};

$pages['/alt/about'] = function ($request, $response, $args) use ($theme, $nav, $pages) {
    $theme->setTheme('alternative');
    $pages['/about']($request, $response, $args);
};

foreach ($pages as $route => $page) {
    $app->get($route, $page);
}

// run app
$app->run();
