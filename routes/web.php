<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

//Login
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'LoginVerification'])->name('LoginVerification');

//Registration
Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/register', [RegisterController::class, 'registration'])->name('registration');

//Registration Verification
Route::get('/verify', [RegisterController::class, 'ShowRegistrationVerification'])->name('ShowRegistrationVerification');
Route::post('/verify-otp', [RegisterController::class, 'RegistrationVerification'])->name('RegistrationVerification');
Route::get('/resend-otp', [RegisterController::class, 'ResendVerification'])->name('ResendVerification');

//Forgot Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'ForgotPassword'])->name('ForgotPassword');
Route::post('/forgot-password', [ForgotPasswordController::class, 'ForgotPasswordEmail'])->name('ForgotPasswordEmail');
Route::get('/forgot-password-otp-show', [ForgotPasswordController::class, 'ShowForgotPasswordOTP'])->name('ShowForgotPasswordOTP');
Route::post('/forgot-password-otp', [ForgotPasswordController::class, 'ForgotPasswordOTP'])->name('ForgotPasswordOTP');
Route::get('/forgot-password-otp-resend', [ForgotPasswordController::class, 'ForgotPasswordOTPresend'])->name('ForgotPasswordOTPresend');
Route::get('/forgot-password-reset-show', [ForgotPasswordController::class, 'ShowForgotPasswordReset'])->name('ShowForgotPasswordReset');
Route::post('/forgot-password-reset', [ForgotPasswordController::class, 'ForgotPasswordReset'])->name('ForgotPasswordReset');

//logout
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('ActiveStatus')->group(function () {

    //user dashboard
    Route::get('/Dashboard', [DashboardController::class, 'Dashboard'])->name('Dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('/Transactions', [DashboardController::class, 'ViewTransactions'])->name('ViewTransactions');

    //user profile
    Route::get('/Profile', [ProfileController::class, 'Profile'])->name('Profile');
    //profile info update
    Route::get('/Profile-update', [ProfileController::class, 'ProfileUpdate'])->name('ProfileUpdate');
    Route::post('/Profile-update', [ProfileController::class, 'saveProfileUpdate'])->name('saveProfileUpdate');
    //profile email update
    Route::get('/Email-update', [ProfileController::class, 'showEmailUpdate'])->name('showEmailUpdate');
    Route::post('/Email-update', [ProfileController::class, 'EmailUpdate'])->name('EmailUpdate');
    Route::get('/New-Email-update', [ProfileController::class, 'showNewEmailUpdate'])->name('showNewEmailUpdate');
    Route::post('/New-Email-update', [ProfileController::class, 'NewEmailUpdate'])->name('NewEmailUpdate');
    Route::get('/New-Email-update-otp', [ProfileController::class, 'showNewEmailUpdateOTP'])->name('showNewEmailUpdateOTP');
    Route::post('/New-Email-update-otp', [ProfileController::class, 'NewEmailUpdateOTP'])->name('NewEmailUpdateOTP');
    Route::get('/New-Email-update-otp-resend', [ProfileController::class, 'NewEmailUpdateOTPresend'])->name('NewEmailUpdateOTPresend');
    //profile password update
    Route::get('/Password-update-otp', [ProfileController::class, 'PasswordUpdateOTP'])->name('PasswordUpdateOTP');
    Route::get('/Password-update', [ProfileController::class, 'showPasswordUpdateOTP'])->name('showPasswordUpdateOTP');
    Route::get('/Password-update-otp-resend', [ProfileController::class, 'PasswordUpdateOTPresend'])->name('PasswordUpdateOTPresend');
    Route::post('/Password-update-otp-verify', [ProfileController::class, 'PasswordUpdateOTPverify'])->name('PasswordUpdateOTPverify');
    Route::get('/Password-update-show', [ProfileController::class, 'showPasswordUpdate'])->name('showPasswordUpdate');
    Route::post('/Password-update-new', [ProfileController::class, 'PasswordUpdate'])->name('PasswordUpdate');

    //income
    Route::get('/income', [IncomeController::class, 'ViewIncomePage'])->name('ViewIncomePage');
    Route::get('/income-add', [IncomeController::class, 'ViewAddIncomePage'])->name('ViewAddIncomePage');
    Route::post('/income-store', [IncomeController::class, 'storeIncome'])->name('storeIncome');
    Route::get('/income-edit', [IncomeController::class, 'ViewEditIncomePage'])->name('ViewEditIncomePage');
    Route::post('/income-edit-save', [IncomeController::class, 'storeEditIncome'])->name('storeEditIncome');
    Route::post('/income/update-status-removed', [IncomeController::class, 'updateIncomeStatusRemoved'])->name('updateIncomeStatusRemoved');

    //Expenses
    Route::get('/expenses', [ExpensesController::class, 'ViewExpensesPage'])->name('ViewExpensesPage');
    Route::get('/expense-add', [ExpensesController::class, 'ViewAddExpensePage'])->name('ViewAddExpensePage');
    Route::post('/expense-store', [ExpensesController::class, 'storeExpense'])->name('storeExpense');
    Route::get('/expense-edit', [ExpensesController::class, 'ViewEditExpensePage'])->name('ViewEditExpensePage');
    Route::post('/expense-edit-save', [ExpensesController::class, 'storeEditExpense'])->name('storeEditExpense');
    Route::post('/expense/update-status-removed', [ExpensesController::class, 'updateExpenseStatusRemoved'])->name('updateExpenseStatusRemoved');

    //Projects
    Route::get('/projects', [ProjectController::class, 'ViewProjectPage'])->name('ViewProjectPage');
    Route::get('/projects-add', [ProjectController::class, 'ViewAddProjectPage'])->name('ViewAddProjectPage');
    Route::post('/projects-store', [ProjectController::class, 'storeProject'])->name('storeProject');
    Route::get('/projects-edit', [ProjectController::class, 'ViewEditProjectPage'])->name('ViewEditProjectPage');
    Route::post('/projects-edit-save', [ProjectController::class, 'storeEditProject'])->name('storeEditProject');
    Route::post('/projects/update-status-start', [ProjectController::class, 'updateStatusStart'])->name('projects.updateStatusStart');
    Route::post('/projects/update-status-cancel', [ProjectController::class, 'updateStatusCancel'])->name('projects.updateStatusCancel');
    Route::post('/projects/update-status-restore', [ProjectController::class, 'updateStatusRestore'])->name('projects.updateStatusRestore');
    Route::post('/projects/update-status-done', [ProjectController::class, 'updateStatusDone'])->name('projects.updateStatusDone');
    Route::post('/projects/update-status-removed', [ProjectController::class, 'updateStatusRemoved'])->name('projects.updateStatusRemoved');

});

