<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/documents', static function() {
    return \Illuminate\Support\Facades\File::get(resource_path() . '/views/docs/documents.html');
});

Route::get('/fcm', static function (): \Illuminate\View\View {
    return view('fcm-template');
});

Route::get('/{any}', function (\Illuminate\Http\Request $request) {
    return Storage::disk('s3')->response($request->getPathInfo());
})->where('any', '.*');
