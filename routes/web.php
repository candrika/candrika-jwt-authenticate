<?php
use Illuminate\Support\Str;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/random-key',function(){
    return Str::random(60);
});

$router->post(
    'auth/login',
    [
        'uses'=>'AuthController@authenticate'
    ]
);

$router->post('user/regis','UserController@regis');

$router->group(
    ['middleware'=>'jwt.auth'],
    function () use ($router)
    {
        # code...
        $router->get('user/detail/profile','UserController@profileDetail');
        $router->put('user/update/profile/{profile_id}','UserController@updateProfile');
    }
);
