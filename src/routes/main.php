<?php

use App\Http\Route;

Route::get('/', 'HomeController@index');
Route::get('/projects', 'ProjectController@index');
Route::post('/projects/create', 'ProjectController@store');
Route::post('/user/login', 'UserController@login');