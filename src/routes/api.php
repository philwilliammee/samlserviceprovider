<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PhilWilliammee\SamlServiceProvider\Http\Controllers\SamlLoginController;

Route::post('/saml-acs', [SamlLoginController::class, 'samlAcs'])->name('saml-acs');
