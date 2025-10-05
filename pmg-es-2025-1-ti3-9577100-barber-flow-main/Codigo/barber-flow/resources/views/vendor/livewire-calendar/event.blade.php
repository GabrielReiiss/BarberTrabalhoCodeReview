<div
    @if($eventClickEnabled)
        wire:click.stop="onEventClick('{{ $event['id']  }}')"
    @endif
    class="bg-white rounded-lg border py-2 px-3 shadow-md cursor-pointer">

    <p class="text-sm font-medium">
        {{ $event['title'] }}
    </p>
    <p class="mt-2 text-xs">
        {!! $event['description'] ?? 'No description' !!}
    </p>
</div>

<!-- 
COMENTÁRIO:
🔍 Sugestão de Melhoria: O código usa a sintaxe de chaves duplas 
com exclamação ({!! $event['description'] !!}). Isso diz ao Blade 
para não escapar o conteúdo, o que é uma porta aberta para ataques 
de Cross-Site Scripting (XSS) se a descrição puder ser inserida por um usuário.

Benefícios da Mudança: Usar a sintaxe padrão ({{ $event['description'] }}) 
protege sua aplicação, pois o Laravel irá converter qualquer tag HTML 
em texto simples, neutralizando scripts maliciosos.

📌 Sugestão de Implementação: A menos que você tenha certeza absoluta 
que o conteúdo da descrição é 100% seguro e precisa renderizar HTML, 
sempre use a sintaxe de escape:
Mudar de:
{!! $event['description'] ?? 'No description' !!}
Para:
{{ $event['description'] ?? 'No description' }}
-->