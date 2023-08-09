<?php
use Monster\App\Route;

/*
|--------------------------------------------------------------------------
| API-Monster Route
|--------------------------------------------------------------------------
|
| The Route class is responsible for defining routes and their associated
| handlers in the application. It provides methods for specifying various
| types of HTTP routes such as GET, POST, etc. and mapping them to
| corresponding controller actions or closures.
|
*/

Route::group('/api', function () {

    Route::get('', 'Welcome@index');
    Route::post('', 'Welcome@index');

    // GET
    Route::get('/download', 'SoundController@download');
    Route::get('/get', 'SoundController@get');

    // POST
    Route::post('/download', 'SoundController@downloadPOST');
    Route::post('/get', 'SoundController@getPOST');

});