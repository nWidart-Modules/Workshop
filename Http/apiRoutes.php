<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' => 'workshop', 'middleware' => 'api.token.admin'], function (Router $router) {
    $router->post('modules/{module}/publish', [
        'as' => 'api.workshop.module.publish',
        'uses' => 'ModulesController@publishAssets',
    ]);
    $router->post('themes/{theme}/publish', [
        'as' => 'api.workshop.theme.publish',
        'uses' => 'ThemeController@publishAssets',
    ]);
});
