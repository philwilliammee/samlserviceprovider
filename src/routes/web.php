<?php

use Illuminate\Support\Facades\Route;
use PhilWilliammee\SamlServiceProvider\Http\Controllers\SamlLoginController;

Route::get('/saml-logout', [SamlLoginController::class, 'logout'])->name('saml-logout');
Route::get('/saml-login', [SamlLoginController::class, 'login'])->name('saml-login');
Route::get('/saml-metadata', [SamlLoginController::class, 'metadata'])->name('saml-metadata');
