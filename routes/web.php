<?php

Route::group(['prefix'=>'auth'],function (){
    Route::get('login','Auth\AccountController@login');
    Route::get('checkResult','Auth\AccountController@checkResult')->name('check-result');

    Route::group(['middleware'=>'check_login'], function (){
        Route::get('check','Auth\AccountController@check');
    });
});

Route::group(['prefix' => 'api/v1'], function () {
    Route::any('/', 'Api\IndexController@getIndex');
    Route::any('/menu', 'Api\IndexController@getMenu');
    Route::any('/jump', 'Api\JumpController@getIndex');
});

Route::group(['prefix'=>'admin'], function (){ //,'middleware'=>'check_login'

   Route::any('payment','Auth\AccountController@payment');

   Route::group(['prefix'=>'settlement'], function (){
       Route::any('customer','Auth\AccountController@customer');
       Route::any('landlord','Auth\AccountController@landlord');
   });

});
