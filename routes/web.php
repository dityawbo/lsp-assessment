<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('skema', function () {
    return Inertia::render('Scheme');
})->name('skema');

Route::get('profil', function () {
    return Inertia::render('Profile');
})->name('profil');

Route::get('pendaftaran', function () {
    return Inertia::render('Pendaftaran');
})->name('pendaftaran');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
