<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Cashflows\CashflowsManager;
use App\Livewire\Dashboard\DashboardView;
use App\Livewire\Debts\DebtsManager;
use App\Livewire\Payments\PaymentsTable;
use App\Livewire\Settings\SettingsForm;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/locale/{locale}', function (string $locale, Request $request) {
    if (in_array($locale, SetLocale::SUPPORTED, true)) {
        $request->session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('locale.switch');

Route::middleware(['auth', 'verified', 'company'])->group(function () {
    Route::get('/dashboard', DashboardView::class)->name('dashboard');
    Route::get('/debts', DebtsManager::class)->name('debts.index');
    Route::get('/cashflow', CashflowsManager::class)->name('cashflow.index');
    Route::get('/payments', PaymentsTable::class)->name('payments.index');
    Route::get('/settings', SettingsForm::class)->name('settings.index');
    Route::get('/companies', \App\Livewire\Companies\CompaniesManager::class)->name('companies.index');
    Route::post('/companies/{company}/switch', function (\App\Models\Company $company, Request $request) {
        abort_unless($company->user_id === $request->user()->id, 404);
        \App\Support\CurrentCompany::set($company);
        return back();
    })->name('companies.switch');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
