<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/course','CourseController');



//jwt
Route::group([

    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    //register new user
    Route::post('register', 'AuthController@register');
    //update users data
    Route::post('update/{id}', 'AuthController@update');
    //show all student 
    Route::post('allStudent','AuthController@allStudent');
    //enroll course
    Route::post('courseEnroll','AuthController@courseEnroll');
    //show all enrolled courses with the students who enrolled thoes
    Route::post('takenCourses','AuthController@takenCourses');
    //show specific student couses which he enrolled 
    Route::post('specificStudentCourse','AuthController@specificStudentCourse');
    //drop enrolled course
    Route::post('dropCourse/{id}','AuthController@dropCourse');

});