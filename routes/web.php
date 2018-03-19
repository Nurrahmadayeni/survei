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

Route::get('/testGraph', 'TestingController@index');

Route::get('/', 'SurveyController@index');
Route::get('survey', 'SurveyController@index');
Route::get('survey/admin', 'SurveyController@listAdmin');
Route::get('survey/ajax', 'SurveyController@getAjax');
Route::get('survey/ajaxSurvey', 'SurveyController@ajaxSurvey');
Route::get('survey/ajaxSurveyActive', 'SurveyController@ajaxSurveyActive');
Route::get('survey/create', 'SurveyController@create');
Route::post('survey/create', 'SurveyController@store');
Route::delete('survey/delete', 'SurveyController@destroy');
Route::get('survey/show/{id}', 'SurveyController@show');
Route::get('survey/edit/{id}', 'SurveyController@edit');
Route::put('survey/edit', 'SurveyController@update');
Route::get('survey/copy/{id}', 'SurveyController@copy');
Route::get('survey/answer/{id}', 'SurveyController@answer');
Route::post('survey/answer', 'SurveyController@answerStore');
Route::get('survey/showAnswer', 'SurveyController@showAnswer');
Route::get('survey/report', 'SurveyController@report');
Route::post('survey/report', 'SurveyController@showreport');
Route::get('survey/reports', 'SurveyController@reportExcel');
Route::get('survey/getObjective', 'SurveyController@getObjective');
Route::get('survey/reportDownload', 'SurveyController@downloadReport');
Route::get('survey/mhs', 'SurveyController@getSubject');
Route::get('survey/insert', 'SurveyController@insert');

Route::get('question/create/{id}', 'QuestionController@create');
Route::post('question/create', 'QuestionController@store');
Route::post('question/getQstTotal', 'QuestionController@getQstTotal');
Route::post('question/getSampleTotal', 'QuestionController@getSampleTotal');
Route::get('question/show/{id}', 'QuestionController@show');
Route::get('question/ajax', 'QuestionController@getAjax');
Route::post('question/edit', 'QuestionController@edit');
Route::delete('question/delete', 'QuestionController@destroy');

Route::get('users/', 'UserController@index');
Route::get('users/create', 'UserController@create');
Route::post('users/create', 'UserController@store');
Route::post('users/delete', 'UserController@destroy');
Route::get('users/ajax', 'UserController@getAjax');
Route::delete('users/delete', 'UserController@destroy');
Route::get('users/edit', 'UserController@edit');
Route::put('users/edit', 'UserController@update');
Route::get('users/ajax/search', 'UserController@searchUser');

Route::get('callback.php', 'CallbackController@callback');