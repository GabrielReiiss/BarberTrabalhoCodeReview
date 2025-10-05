<div class="space-y-4">
    <h2 class="text-xl font-bold">Histórico de Agendamentos</h2>

    @if($appointments->isEmpty())
        <p class="text-gray-500">Nenhum agendamento encontrado.</p>
    @else
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
            @foreach($appointments as $appointment)
                <x-card title="{{ $appointment->service->name }}" shadow separator>
                    <p class="text-sm text-gray-600">
                        <strong>Funcionario:</strong> {{ $appointment->barber->name }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Data:</strong> {{ \Carbon\Carbon::parse($appointment->start)->format('d/m/Y H:i') }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Valor:</strong> R$ {{ $appointment->service->price }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Status:</strong> {{ (!$appointment->confirmed_at) ? 'Em aberto' : ((\Carbon\Carbon::parse($appointment->end) < \Carbon\Carbon::now()) ? 'Confirmado' : 'Finalizado')}}
                    </p>
                    <x-slot:menu>
                        @if (!$appointment->confirmed_at)
                            <x-button 
                                icon="o-x-mark" 
                                class="btn-link text-error btn-sm"
                                spinner 
                                wire:click="open_cancel({{ $appointment->id }})"
                            />
                        @endif
                    </x-slot:menu>
                </x-card>
            @endforeach
        </div>

        @if($modal_cancel)
            <x-modal wire:model="modal_cancel" title="Apagar Categoria" >
                <div class="text-left">Tem certeza que deseja cancelar o agendamento de {{$appointment->service->name}}?</div>
                <x-slot:actions>
                    <x-button 
                        label="Cancelar" 
                        class=" btn-sm" 
                        @click="$wire.modal_cancel = false" 
                    />
                    <x-button 
                        label="Confirmar" 
                        class="btn-error btn-sm" 
                        wire:click="cancel()"
                        spinner 
                    />
                </x-slot:actions>
            </x-modal>
        @endif
    @endif
</div>

<!--
COMENTÁRIO 1:
🔍 Sugestão de Melhoria: A linha que calcula o status do agendamento 
contém uma lógica ternária aninhada complexa: {{ (!$appointment->confirmed_at) 
? 'Em aberto' : ((\Carbon\Carbon::parse($appointment->end) < 
\Carbon\Carbon::now()) ? 'Confirmado' : 'Finalizado')}}. Views devem ser 
simples e apenas exibir dados.

Benefícios da Mudança: Mover essa lógica para o backend (o Model Appointment) 
centraliza as regras de negócio, facilita testes e deixa a view muito mais limpa e legível.

📌 Sugestão de Implementação: Criar um Accessor no modelo Appointment.php:
// No App\Models\Appointment.php
use Carbon\Carbon;
public function getStatusAttribute(): string
{
    if (!$this->confirmed_at) {
        return 'Em aberto';
    }

    return Carbon::parse($this->end)->isPast() ? 'Finalizado' : 'Confirmado';
}

COMENTÁRIO 2:
🔍 Sugestão de Melhoria: Dentro do loop @foreach, o código acessa relacionamentos 
como $appointment->service->name e $appointment->barber->name. Se a lista de agendamentos 
for grande, isso pode gerar dezenas ou centenas de consultas ao banco de dados 
(uma para cada agendamento para buscar o serviço, e outra para buscar o barbeiro).

Benefícios da Mudança: Usar Eager Loading resolve esse problema de performance carregando 
todos os dados necessários em pouquíssimas consultas.

📌 Sugestão de Implementação: No componente Livewire (Appointments.php, que não foi 
enviado mas é o responsável por esta view), a consulta que busca os agendamentos deve 
ser modificada:
No método render() do componente Livewire
Mudar de:
$appointments = auth()->user()->appointments()->latest()->get();
Para:
$appointments = auth()->user()->appointments()->with(['service', 'barber'])->latest()->get();
-->