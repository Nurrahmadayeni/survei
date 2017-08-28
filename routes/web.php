<?php

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

Route::get('/', 'SurveyController@index');
Route::get('survey', 'SurveyController@index');
Route::get('survey/ajax', 'SurveyController@getAjax');
Route::get('survey/ajaxSurvey', 'SurveyController@ajaxSurvey');
Route::get('survey/ajaxSurveyUser', 'SurveyController@getAjaxSurveyUser');
Route::get('survey/create', 'SurveyController@create');
Route::post('survey/create', 'SurveyController@store');
Route::delete('survey/delete', 'SurveyController@destroy');
Route::get('survey/show', 'SurveyController@show');
Route::get('survey/edit', 'SurveyController@edit');
Route::put('survey/edit', 'SurveyController@update');
Route::get('survey/report', 'SurveyController@report');
Route::post('survey/report', 'SurveyController@showreport');

Route::get('users/', 'UserController@index');
Route::get('users/create', 'UserController@create');
Route::post('users/create', 'UserController@store');
Route::post('users/delete', 'UserController@destroy');
Route::get('users/ajax', 'UserController@getAjax');
Route::delete('users/delete', 'UserController@destroy');
Route::get('users/edit', 'UserController@edit');
Route::put('users/edit', 'UserController@update');
Route::get('users/ajax/search', 'UserController@searchUser');