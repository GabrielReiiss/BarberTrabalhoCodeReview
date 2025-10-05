<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Middleware\UserMiddleware;
use App\Livewire\User\UserList;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;
use App\Livewire\Login\Form;
use App\Livewire\Login\ForgotPassword;
use App\Livewire\Login\Logout;
use App\Livewire\Login\ResetPassword;
use App\Livewire\Profile\UserProfile;
use App\Livewire\SignUp\Cadastro;
use App\Livewire\LandingPage\LandingPage;
use App\Livewire\Appointment\AppointmentList;
use App\Livewire\Appointment\View;
use App\Livewire\Services\ServicesList;
use App\Livewire\Finance\FinanceList;
use App\Livewire\Profile\Index;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', LandingPage::class)->name('index');

// Rotas para usuários não logados
Route::middleware('guest')->group(function(){
    Route::get('/login', Form::class)->name('login');
    Route::get('/sign-up', Cadastro::class)->name('sign-up');

    // forgot password
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::post('/forgot-password', [ForgotPassword::class, 'sendResetLink'])->name('password.email');

    // reset password
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
    Route::post('/reset-password', [ResetPassword::class, 'resetPassword'])->name('password.update');

    Route::get('/auth/google', function () {
        return Socialite::driver('google')->redirect();
    })->name('auth.google');

    Route::get('/auth/google/callback', function () {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::firstOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'email' => $googleUser->getEmail(),
        ])->update([
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'email' => $googleUser->getEmail(),
        ]);

        Auth::login($user);

        return redirect()->route('profile'); // ajuste conforme sua rota
    });
});

// Rotas para logados
Route::middleware('auth')->group(function(){
    
    Route::get('/profile', Index::class)->name('profile');
    Route::get('/logout', Logout::class); 
    Route::get('/appointments', AppointmentList::class)->name('appointments');
    
    Route::middleware([UserMiddleware::class])->group(function(){
        Route::get('/users', UserList::class);
        Route::get('/finance', FinanceList::class);
        Route::get('/services', ServicesList::class);
        Route::get('/relatory', [FinanceList::class, 'export_relatory'])->name('relatory');
        Route::get('/calendar', View::class); 
    });

});

/*
COMENTÁRIO 1:
🔍 Sugestão de Melhoria: A rota /auth/google/callback contém 
toda a lógica de autenticação com o Google. Isso repete o 
problema já visto no arquivo api.php e também ignora a existência 
do GoogleController criado anteriormente.

Benefícios da Mudança: Centralizar a lógica no GoogleController 
(e no GoogleAuthService sugerido na Parte 1) evita duplicação 
de código e mantém o arquivo de rotas limpo.

📌 Sugestão de Implementação: Remover a closure e apontar a rota para o controller:
Remover a closure gigante
Route::get('/auth/google/callback', function () { ... });
Apontar para o controller já existente
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);




COMENTÁRIO 2:
🔍 Sugestão de Melhoria: A rota de logout (/logout) está definida com 
Route::get. Ações que modificam o estado do servidor (como login, logout, 
exclusão) devem sempre usar verbos HTTP como POST, PUT ou DELETE.

Benefícios da Mudança: Usar GET para logout cria uma vulnerabilidade de 
Cross-Site Request Forgery (CSRF). Navegadores podem pré-carregar links, 
e um simples link malicioso em outro site poderia deslogar o seu usuário. 
Usar POST com a proteção CSRF do Laravel previne isso.

📌 Sugestão de Implementação:
Mudar de:
Route::get('/logout', Logout::class);
Para:
Route::post('/logout', Logout::class)->name('logout');
*/