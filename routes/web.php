<?php

use App\Http\Controllers\AuthController as UserAuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\PemesananTiketController as UserPemesananTiketController;
use App\Http\Controllers\User\ProfilController as UserProfilController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('user.home.index'))->name('home');
Route::get('/home', fn () => view('user.home.index'))->name('user.home.index');

Route::get('/login', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->role === User::ROLE_USER) {
            return redirect()->route('user.dashboard');
        }

        if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PETUGAS], true)) {
            return redirect('/admin');
        }
    }

    return redirect()->route('user.login');
})->name('login');


/*
|--------------------------------------------------------------------------
| Logout Admin tanpa menghapus session User
|--------------------------------------------------------------------------
*/
Route::post('/admin/logout-safe', function (Request $request) {
    Auth::guard('admin')->logout();

    // Hanya ganti CSRF token, jangan invalidate seluruh session.
    $request->session()->regenerateToken();

    return redirect('/admin/login');
})
    ->middleware('auth:admin')
    ->name('admin.logout.safe');

Route::prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/login', [UserAuthController::class, 'showLogin'])
            ->name('login');
        Route::post('/login', [UserAuthController::class, 'login'])
            ->name('login.process');
        Route::get('/register', [UserAuthController::class, 'showRegister'])
            ->name('register');
        Route::post('/register', [UserAuthController::class, 'register'])
            ->name('register.process');
    });

Route::middleware(['auth:web', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout'])
            ->name('logout');
        Route::get('/dashboard', [UserDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/profil', [UserProfilController::class, 'edit'])
            ->name('profil.edit');
        Route::put('/profil', [UserProfilController::class, 'update'])
            ->name('profil.update');
        Route::get('/pemesanan', [UserPemesananTiketController::class, 'index'])
            ->name('pemesanan.index');
        Route::get('/pemesanan/create', [UserPemesananTiketController::class, 'create'])
            ->name('pemesanan.create');
        Route::post('/pemesanan', [UserPemesananTiketController::class, 'store'])
            ->name('pemesanan.store');
        Route::get('/pemesanan/{pemesanan}', [UserPemesananTiketController::class, 'show'])
            ->name('pemesanan.show');
        Route::get('/pemesanan/{pemesanan}/edit', [UserPemesananTiketController::class, 'edit'])
            ->name('pemesanan.edit');
        Route::put('/pemesanan/{pemesanan}', [UserPemesananTiketController::class, 'update'])
            ->name('pemesanan.update');
    });
