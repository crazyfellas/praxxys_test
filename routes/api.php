<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
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
Route::group(['middleware' => 'prevent-back-history'],function(){
    Route::group(['middleware' => 'auth:api'], function () {
        Route::resource('products', ProductController::class);
        Route::post('updateImage',[ProductController::class,'updateImage']);
        Route::delete('product/delete/{product}',[ProductController::class,'destroy']);
        Route::delete('products/massDestroy/{products}', [ProductController::class, 'massDestroy']);


        Route::get('category',[CategoryController::class,'index']);
        Route::resource('images', ImageController::class);
    });
});







