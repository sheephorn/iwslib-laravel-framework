<?php

use IwslibLaravel\Util\RouteHelper;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// ルーティングで適合しない場合はフロント側のRoutingにゆだねる
RouteHelper::get('/{any?}', IwslibLaravel\Http\IndexController::class)->where('any', '.*');
