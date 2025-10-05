<div>    
    <div class="w-full max-w-xl mx-auto p-2">
        @php
            $crop = include app_path('View/Components/AvatarCropper.php');
        @endphp

        <div class="flex justify-center mb-2">  
            <x-file wire:model="avatar" accept="image/png, image/jpeg" change-text="{{ ($user->google_id) ? 'Avatar' : 'Alterer' }}" crop-after-change :crop-config="$crop" :disabled="$user->google_id">    
                <img src="{{ ($user->google_id) ? $user->avatar : asset($user->avatar) }}" class="h-36 rounded-full" />
            </x-file>
        </div>

        <div class="p-4">
            <x-form wire:submit="save">
                <x-input
                    label="Nome de usuário"
                    type="name"
                    wire:model="username"
                    :readonly="$user->google_id"
                />
                    
                <x-input
                    label="E-mail de usuário"
                    type="email"
                    wire:model="mail"
                    :readonly="$user->google_id"
                />

                <x-input
                    label="Telefone"
                    wire:model="phone"
                    x-data
                    x-mask="(99) 999999999"
                />
                
                @if(!$user->google_id)
                    <p class="text-gray-400 text-center text-sm pt-9">──────────────   Alterar senha   ──────────────</p>

                    <x-password 
                        label="Senha Atual" 
                        wire:model="actual_password" 
                        clearable 
                    />
                    <x-password 
                        label="Nova Senha" 
                        wire:model="password" 
                        clearable 
                    />
                    <x-password 
                        label="Repita a senha" 
                        wire:model="confirm_password" 
                        clearable 
                    />
                @endif

                <x-slot:actions>
                    <x-button 
                        label="Salvar" 
                        type="submit" 
                        spinner="save" 
                        class="btn-sm btn-success"
                        spinner="save" 
                    />
                </x-slot:actions>
            </x-form>
        </div>
    </div>
</div>

<!--
COMENTÁRIO 1:
🔍 Sugestão de Melhoria: A linha @php $crop = include 
app_path('View/Components/AvatarCropper.php'); @endphp 
é um forte code smell e uma violação do padrão MVC. Ela 
está incluindo e executando um arquivo PHP diretamente da 
view, misturando responsabilidades de configuração, lógica 
e apresentação.

Benefícios da Mudança: A configuração do componente de crop 
deve ser gerenciada pelo backend. Isso mantém a view limpa, 
melhora a segurança e segue as convenções do framework.

📌 Sugestão de Implementação: A configuração $crop deve ser 
preparada como uma propriedade pública no componente Livewire 
UserProfile e passada para a view a partir dele, sem a 
necessidade de include ou diretivas @php.




COMENTÁRIO 2:
🔍 Sugestão de Melhoria: A lógica para decidir qual URL de 
avatar usar está na view: <img src="{{ ($user->google_id) ? 
$user->avatar : asset($user->avatar) }}" ... />.

Benefícios da Mudança: Centralizar essa lógica em um accessor 
no modelo User simplifica a view e garante que a URL do avatar 
seja sempre gerada da mesma forma em qualquer parte do sistema.

📌 Sugestão de Implementação: Adicionar um accessor no modelo User.php:
// No App\Models\User.php
public function getAvatarUrlAttribute(): string
{
    if ($this->google_id && filter_var($this->avatar, FILTER_VALIDATE_URL)) {
        return $this->avatar; // Se for conta Google, o avatar já é uma URL completa
    }
    return asset($this->avatar ?? 'images/default-avatar.png'); // Retorna o asset ou um avatar padrão
}
-->