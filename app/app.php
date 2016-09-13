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
        'icon' => 'glyphicon glyphicon-star',
        'class' => '',
        'label' => 'github',
        'url' => 'https://github.com/coreorm/slim3-view-example'
    ],
    [
        'icon' => 'glyphicon glyphicon-info-sign',
        'class' => '',
        'label' => 'about',
        'url' => '/about'
    ]
];

$theme->setLayout('page')
    ->setData('pageTitle', 'Slim3 View Example')
    ->setData('nav', $nav);

// homepage
$app->get('/', function ($request, $response, $args) use ($theme, $nav) {
    $nav[0]['class'] = 'active';
    $theme->setData('nav', $nav)
        ->setData('pageTitle', $theme->getData('pageTitle') . ' > Homepage')
        ->render($response, 'pages/homepage');
});

// about us page
$app->get('/about', function ($request, $response, $args) use ($theme, $nav) {
    $nav[2]['class'] = 'active';
    $theme->setData('nav', $nav)
        ->setData('pageTitle', $theme->getData('pageTitle') . ' > About')
        ->render($response, 'pages/about');
});

// run app
$app->run();
