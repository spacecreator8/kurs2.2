<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', 'App\Http\Controllers\TestController@index');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('me', 'App\Http\Controllers\AuthController@me');
});




Route::group(['namespace'=>'App\Http\Controllers\User', 'prefix'=>'user', 'middleware' => 'check.guest'], function(){
    Route::post('/store', 'StoreController');
    Route::post('/restore', 'RestoreController'); //Принимает почту, куда выслать новый пароль
});
Route::group(['namespace'=>'App\Http\Controllers\User', 'prefix'=>'user', 'middleware'=>['jwt.auth', 'banFilter']], function(){
    Route::get('/{user}/edit', 'EditController');
    Route::patch('/{user}', 'UpdateController');
    Route::post('/{user}/complaint', 'ComplaintController');
//    Route::post('/{user}/lots', 'UserLotsController'); //этот функционал выполняет роут - api/lots/my
});


Route::get('/main', 'App\Http\Controllers\MainController@index')->name('main.index');




Route::group(['namespace'=>'App\Http\Controllers', 'prefix'=>'lots', 'middleware'=>['jwt.auth', 'banFilter']], function(){
    Route::post('/all', 'Lot\AllLotController')->name('lot.all'); //можно фильтровать - ждет категорию
    Route::post('/my', 'Lot\IndexController')->name('lot.index'); //лоты пользователя - должны выдаваться все с возможностью фильтрации. Ждет 2 ключа cat_id, status_id
    Route::post('/', 'Lot\StoreController')->name('lot.store');
    Route::get('/{lot}', 'Lot\ShowController')->name('lot.show');
    Route::get('/{lot}/edit', 'Lot\EditController')->name('lot.edit');
    Route::patch('/{lot}', 'Lot\UpdateController')->name('lot.update');
    Route::delete('/{lot}', 'Lot\DeleteController')->name('lot.delete');
    Route::post('/{lot}/bet', 'MainController@betUp')->name('lot.betUp'); // ожидает либо новую назначенную цену (new_cost = *цифра*), либо сигнал о том что ставка увеличена на bet_step (bet_up = 1) - должно быть что-то одно

    Route::post('/{lot}/edit/photo/', 'Photo\StoreController');
    Route::delete('/{lot}/edit/photo/{photo}', 'Photo\DeleteController');

});

Route::group(['namespace'=>'App\Http\Controllers\Message', 'prefix'=>'message', 'middleware'=>['jwt.auth', 'banFilter']], function(){
    Route::get('/', 'IndexController')->name('message.index');
    Route::get('/{recipient}/create', 'CreateController')->name('message.asd');
    Route::post('/{recipient}', 'StoreController')->name('message.store');
    Route::delete('/{message}', 'DeleteController')->name('message.delete');
});

Route::group(['namespace'=>'App\Http\Controllers', 'prefix'=>'admin', 'middleware'=>'isAdmin'], function(){
    Route::get('/', 'AdminController@index')->name('admin.index');
    Route::post('/lots', 'AdminController@lots');//ожидает 2 ключа для фильтрации category_id, status_id
    Route::get('/complaints', 'AdminController@complaints');
    Route::get('/users', 'AdminController@users');
    Route::get('/users/{user}', 'AdminController@showUser');
    Route::get('/users/{user}/lots', 'AdminController@userLots');
    Route::get('/users/{user}/complaints', 'AdminController@userComplaints');
    Route::post('/users/{user}/{complaint}/change_rating', 'AdminController@userChangeRating');//ожидает ключи (2шт) в запросе для изменения рейтинга поведения
    Route::post('/users/{user}/ban', 'AdminController@banUser');

});
