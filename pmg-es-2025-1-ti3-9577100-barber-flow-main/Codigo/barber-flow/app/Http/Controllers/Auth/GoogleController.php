<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Registered;

class GoogleController extends Controller
{
    public function redirect(Request $request, string $provider)
    {

        $this->validateProvider($request);

        return Socialite::driver($provider)->redirect();
        
    }

    public function callback(Request $request, String $provider)
    {
        $this->validateProvider($request);
        
        $response = Socialite::driver('google')->user();

        //verifica se ja existe
        $user = User::where('email', $response->getEmail())->first();

        //se ja existe
        if($user && !$user->google_id){
            return redirect()->route('login')->withErrors([
                'email' => 'Já existe uma conta com esse e-mail.'
            ]);
        }

        //criando user
        $user = User::firstOrCreate(
            ['email' => $response->getEmail()],
            ['password' => bcrypt(Str::random(16))],
        );

        $user->update([
            'google_id' => $response->getId()
        ]);

        if($user->wasRecentlyCreated){
            event(new Registered(($user)));
        }

        Auth::login($user, remember: true);
        return redirect()->intended('/users');

    }

    protected function validateProvider(Request $request): array
    {
        return Validator::make(
            $request->route()->parameters(),
            ['provider' => 'in:google']
        )->validate();
    }
    
}

/*

COMENTÁRIO 1:
🔍 Sugestão de Melhoria: A classe GoogleController está com a 
responsabilidade de lidar com a lógica de negócio de autenticação, 
validação, busca e criação de usuários. Isso é um code smell conhecido 
como "Fat Controller".

Benefícios da Mudança: Para seguir o Princípio da Responsabilidade 
Única (SOLID), sugiro mover toda a lógica do método callback para 
uma nova classe de serviço, como App\Services\Auth\GoogleAuthService. 
O controller se tornaria muito mais limpo, apenas orquestrando a 
chamada para este serviço.

📌 Sugestão de Implementação:
Criar a classe GoogleAuthService com um método handleCallback(SocialiteUser $socialiteUser).
Injetar este serviço no construtor do GoogleController.
O método callback do controller se resumiria a chamar o serviço 
e fazer o login do usuário.




COMENTÁRIO 2:
🔍 Sugestão de Melhoria: A linha ['password' => bcrypt(Str::random(16))] 
cria uma senha aleatória para o usuário social. Como o campo password 
na migration create_users_table já é nullable, essa senha não tem utilidade prática.

Benefícios da Mudança: Simplifica o código e evita armazenar uma 
senha que nunca será usada. O modelo User já tem o cast para 
hashed, então o Laravel cuidaria disso automaticamente se uma 
senha fosse passada, mas o ideal aqui é simplesmente não definir uma.

📌 Sugestão de Implementação: Ao usar updateOrCreate, simplesmente 
omita o campo password ou defina-o como null se o usuário for social.
*/
