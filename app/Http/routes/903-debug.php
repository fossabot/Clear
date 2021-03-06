<?php

\Route::group(['namespace' => 'Manage\Debug', 'prefix' => 'debug', 'before' => 's5_admin', 'middleware' => ['web']], function() {
    \Route::get('', function(){ return \Redirect::to('/debug/decrypt'); });
    \Route::get('/error', function(){ throw new Exception('This is an intentional error.'); });
    \Route::controller('/log', 'LogController');
    \Route::controller('/queue', 'QueueController');
    \Route::controller('/decrypt', 'DecryptController');
});
