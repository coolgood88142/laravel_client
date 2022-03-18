<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Route::get('/authorize', function () {
//     $query = http_build_query([
//         'client_id'     => '17',
//         'redirect_uri'  => 'http://127.0.0.1:8080/token',
//         'response_type' => 'code',
//         'scope'         => 'Email',
//         'state'         => '1234',
//     ]);

//     return redirect('http://127.0.0.1:8000/oauth/authorize?' . $query);
// });

// Route::get('/token', function (Request $request) {
//     $http     = new GuzzleHttp\Client;
//     $response = $http->post('http://127.0.0.1:8000/oauth/token', [
//         'form_params' => [
//             'grant_type'    => 'authorization_code',
//             'redirect_uri'  => 'http://127.0.0.1:8080/token',
//             'code'          => $request->code,
//         ],
//         'headers' => [
//             'Accept'        => 'application/json',
//             'Authorization' => 'Basic ' . base64_encode('17:YwvWN4dVe0blF7zpwAl2ge61ksTYBrvY8J8NfMT6'),
//         ],
//     ]);

//     return json_decode((string)$response->getBody(), true);
// });

Route::group(['prefix' => '/user', 'middleware' => 'auth:api'], function (){
    Route::get('/profile', function (Request $request) {
        return $request->user()->toArray();
    })->middleware('scope:Profile');
    Route::get('/email', function (Request $request) {
        return $request->user()->email;
    })->middleware('scope:Email');
});

Route::get('/authorize', function (Request $request) {
    $request->session()->put('state', $state = Str::random(40));

    $query = http_build_query([
        'client_id' => '19',
        'redirect_uri' => 'http://127.0.0.1:8080/callback',
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
    ]);

    return redirect('http://127.0.0.1:8000/authorizationCode?'.$query);
});

Route::get('/callback', function (Request $request) {
    $http     = new GuzzleHttp\Client;
    $response = $http->post('http://127.0.0.1:8000/oauth/token', [
        'form_params' => [
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => 'http://127.0.0.1:8080/callback',
            'code'          => $request->code,
            'client_id'     => '19',
            'client_secret' => 'YwvWN4dVe0blF7zpwAl2ge61ksTYBrvY8J8NfMT6'
        ]
    ]);

    $queryString = json_decode((string)$response->getBody(), true);

    $userInfo = $http->get('http://127.0.0.1:8000/api/user', [
        'headers' => [
            'Authorization' => 'Bearer '. $queryString['access_token'],
        ],
    ]);

    $user = json_decode((string)$userInfo->getBody(), true);

    return redirect('http://127.0.0.1:8080/verifyUserInfo?name=' . $user['name'] . '&email=' . $user['email'] . '&password=' . $user['password']);



});

Route::get('verifyUserInfo', 'UserController@verifyUserInfo')->name('verifyUserInfo');

// redirect('http://127.0.0.1:8080/login?email='. $user['email'] . '&');


