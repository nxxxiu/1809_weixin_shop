<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->resource('goods', GoodsController::class);
    $router->resource('WxUser', UserController::class);

    //素材
    Route::post('/material/material','MaterialController@material');
    Route::get('/material/index','MaterialController@index');

    //消息群发
    Route::get('/groups/index','GroupsController@index');
    Route::get('/groups/groups','GroupsController@groups');
});
