<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Página de prueba de la API
Route::get('/api-test', function () {
    return view('api-test');
});
