
<div
    ondragenter="onLivewireCalendarEventDragEnter(event, '{{ $componentId }}', '{{ $day }}', '{{ $dragAndDropClasses }}');"
    ondragleave="onLivewireCalendarEventDragLeave(event, '{{ $componentId }}', '{{ $day }}', '{{ $dragAndDropClasses }}');"
    ondragover="onLivewireCalendarEventDragOver(event);"
    ondrop="onLivewireCalendarEventDrop(event, '{{ $componentId }}', '{{ $day }}', {{ $day->year }}, {{ $day->month }}, {{ $day->day }}, '{{ $dragAndDropClasses }}');"
    class="flex-1 h-30 lg:h-40 border border-gray-200 -mt-px -ml-px"
    style="min-width: 10rem;">

    {{-- Wrapper for Drag and Drop --}}
    <div
        class="w-full h-full"
        id="{{ $componentId }}-{{ $day }}">

        <div
            @if($dayClickEnabled)
                wire:click="onDayClick({{ $day->year }}, {{ $day->month }}, {{ $day->day }})"
            @endif
            class="w-full h-full p-2 {{ $dayInMonth ? $isToday ? 'bg-green-50/50' : ' bg-white ' : 'bg-gray-100' }} flex flex-col">

            {{-- Number of Day --}}
            <div class="flex items-center">
                <p class="text-sm {{ $dayInMonth ? $isToday ? 'text-success font-bold' : ' font-medium ' : '' }}">
                    {{ $day->format('j') }}
                </p>
                <p class="text-xs text-gray-600 ml-4">
                    @if($events->isNotEmpty())
                        {{ $events->count() }} {{ Str::plural('event', $events->count()) }}
                    @endif
                </p>
            </div>

            {{-- Events --}}
            <div class="p-2 my-2 flex-1 overflow-y-auto">
                <div class="grid grid-cols-1 grid-flow-row gap-2">
                    @foreach($events as $event)
                        <div
                            @if($dragAndDropEnabled)
                                draggable="true"
                            @endif
                            ondragstart="onLivewireCalendarEventDragStart(event, '{{ $event['id'] }}')">
                            @include($eventView, [
                                'event' => $event,
                            ])
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

<!--
COMENTÁRIO:
🔍 Sugestão de Melhoria: O componente tem múltiplos 
atributos de eventos JavaScript inline (ondragenter, 
ondragleave, ondragover, ondrop). Isso mistura a 
estrutura HTML com o comportamento de forma excessiva.

Benefícios da Mudança: Abstrair essa lógica para uma 
diretiva AlpineJS (que já parece estar no projeto) ou 
para um arquivo JS separado deixaria o HTML mais limpo 
e declarativo, além de facilitar a manutenção da lógica 
de drag-and-drop.

📌 Sugestão de Implementação (com AlpineJS):
Criar um componente AlpineJS que encapsule toda a lógica de drag-and-drop.
A view seria simplificada para algo como:
<div x-data="calendarDayDropTarget" 
     @dragenter.prevent="onEnter" 
     @dragleave.prevent="onLeave" 
     @dragover.prevent 
     @drop.prevent="onDrop">
     ...
</div>
-->