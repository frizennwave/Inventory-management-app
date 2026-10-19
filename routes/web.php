<?php

use Illuminate\Support\Facades\Route;

Route::get('/version', function () {
    return view('welcome');
});
