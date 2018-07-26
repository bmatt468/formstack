<?php

use Illuminate\Http\Request;

Route::prefix('documents')->middleware('auth.basic.once')->group(function () {
    Route::get('', 'DocumentsController@all');
    Route::post('', 'DocumentsController@post');

    Route::get('{id}', 'DocumentsController@get');
    Route::put('{id}', 'DocumentsController@put');
    Route::delete('{id}', 'DocumentsController@delete');

    Route::get('{id}/data/{key}', 'DataController@get');
    Route::patch('{id}/data/{key}', 'DataController@patch');
    Route::delete('{id}/data/{key}', 'DataController@delete');

    Route::get('{id}/export', 'ExportController@get');
    Route::post('{id}/export', 'ExportController@post');
});
