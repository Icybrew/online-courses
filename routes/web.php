<?php

use App\Core\Routing\Router;

/*
 * Web Routes
 */

/* Auth */
Router::get('/login', 'Auth\LoginController@show')->name('login.show')->middleware('guest');
Router::post('/login', 'Auth\LoginController@login')->name('login')->middleware('guest');
Router::get('/register', 'Auth\RegisterController@show')->name('register.show')->middleware('guest');
Router::post('/register', 'Auth\RegisterController@register')->name('register')->middleware('guest');
Router::post('/logout', 'Auth\LoginController@logout')->name('logout')->middleware('auth');

/* Profile */
Router::get('/profile', 'ProfileController@index')->name('profile.index')->middleware('auth');
Router::get('/profile/change-password', 'ProfileController@editPassword')->name('profile.edit.password')->middleware('auth');
Router::patch('/profile/change-password', 'ProfileController@updatePassword')->name('profile.update.password')->middleware('auth');
Router::get('/profile/purchases', 'ProfileController@purchases')->name('profile.purchases')->middleware('auth');

/* Index */
Router::get("", "IndexController@index")->name('home');


/* Courses */
Router::get('/courses', 'CoursesController@index')->name('courses.index');
Router::get('/courses/{course}', 'CoursesController@show')->name('courses.show');
Router::get('/courses/{course}/purchase', 'CoursesController@purchase')->name('courses.purchase');
Router::get('/courses/{course}/purchase/payment-method', 'CoursesController@purchasePaymentMethod')->name('courses.purchase.payment-method')->middleware('auth');
Router::post('/courses/{course}/purchase', 'CoursesController@purchaseConfirm')->name('courses.purchase.confirm')->middleware('auth');

Router::get('/courses/{course}/purchase/accept', 'CoursesController@purchaseAccept')->name('courses.purchase.accept');
Router::post('/courses/{course}/purchase/accept', 'CoursesController@purchaseAccept')->name('courses.purchase.accept');
Router::get('/courses/{course}/purchase/cancel', 'CoursesController@purchaseCancel')->name('courses.purchase.cancel');
Router::post('/courses/{course}/purchase/cancel', 'CoursesController@purchaseCancel')->name('courses.purchase.cancel');
Router::get('/courses/{course}/purchase/callback', 'CoursesController@purchaseCallback')->name('courses.purchase.callback');
Router::post('/courses/{course}/purchase/callback', 'CoursesController@purchaseCallback')->name('courses.purchase.callback');


/* Administration */
Router::get('/administration/', 'Admin\AdminController@index')->name('admin.index')->middleware('admin');

/* Administration Statistics */
Router::get('/administration/statistics', 'Admin\StatisticsController@index')->name('admin.statistics.index')->middleware('admin');
Router::get('/administration/statistics/income', 'Admin\StatisticsController@income')->name('admin.statistics.income')->middleware('admin');

/* Administration Videos */
Router::get('/administration/videos', 'Admin\VideosController@index')->name('admin.videos.index')->middleware('admin');
Router::get('/administration/videos/create', 'Admin\VideosController@create')->name('admin.videos.create')->middleware('admin');
Router::put('/administration/videos', 'Admin\VideosController@store')->name('admin.videos.store')->middleware('admin');

/* Administration Courses */
Router::get('/administration/courses', 'Admin\CoursesController@index')->name('admin.courses.index')->middleware('admin');
Router::get('/administration/courses/create', 'Admin\CoursesController@create')->name('admin.courses.create')->middleware('admin');
Router::put('/administration/courses', 'Admin\CoursesController@store')->name('admin.courses.store')->middleware('admin');
Router::get('/administration/courses/{course}', 'Admin\CoursesController@show')->name('admin.courses.show')->middleware('admin');
Router::get('/administration/courses/{course}/edit', 'Admin\CoursesController@edit')->name('admin.courses.edit')->middleware('admin');
Router::patch('/administration/courses/{course}', 'Admin\CoursesController@update')->name('admin.courses.update')->middleware('admin');
Router::delete('/administration/courses/{course}', 'Admin\CoursesController@delete')->name('admin.courses.delete')->middleware('admin');

/* Administration google */
Router::get('/administration/google/authorize', 'Admin\GoogleController@authorize')->name('admin.google.authorize')->middleware('admin');

/* Administration Users */
Router::get('/administration/users', 'Admin\UsersController@index')->name('admin.users.index')->middleware('admin');
Router::get('/administration/users/{user}', 'Admin\UsersController@show')->name('admin.users.show')->middleware('admin');
Router::post('/administration/users/{user}/send-mail', 'Admin\UsersController@sendMail')->name('admin.users.send-mail')->middleware('admin');


/*
 * Api Routes
 */

Router::get('/api/user/{user}/videos', 'Api\User\VideosController@videos')->name('api.user.video');
