<?php

Route::group(['namespace' => 'CoruscateSolutions\SerialNumberGeneratorLaravel\Http\Controllers' , 'prefix'=>'serial-no'], function () {

   Route::post('/', 'SerialController@store');
   Route::post('list', 'SerialController@listDetail');
   Route::get('/{id}', 'SerialController@show');
   Route::delete('delete/{id}', 'SerialController@destroy');

});
