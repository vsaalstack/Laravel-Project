<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AgentController,
    OfficeController,
    UserController,
};

 // Agents Route
 Route::get('agent/password/{id}', [AgentController::class, 'password'])->name('agent.password');
 Route::post('agent/setting/{id}', [AgentController::class, 'setting'])->name('agent.setting');
 Route::get('agent/office/{id}/{ids}', [AgentController::class, 'office'])->name('agent.office');
 Route::resource('agent', AgentController::class);

 //Office Routes
 Route::post('office/setting/{id}', [OfficeController::class, 'setting'])->name('office.setting');
 Route::get('office/apifyTask/{id}', [OfficeController::class, 'apifyTask']);
 Route::resource('office', OfficeController::class)


 // User Profile update
 Route::get('user/password/{id}', [UserController::class, 'password'])->name('user.password');
 Route::resource('user', UserController::class);
 Route::get('subscription-details', [UserController::class, 'subscriptionDetails'])->name('user.subscriptionDetails');
