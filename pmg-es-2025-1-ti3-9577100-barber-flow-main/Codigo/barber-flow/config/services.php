<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' =>  [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    ],
];

/*
COMENTÁRIO:
🔍 Sugestão de Melhoria: O arquivo services.php (e outros arquivos 
de configuração) dependem diretamente de variáveis de ambiente (ex: 
env('GOOGLE_CLIENT_ID')). Se uma dessas variáveis essenciais não for 
definida no arquivo .env, a aplicação pode falhar de maneiras inesperadas em produção.

Benefícios da Mudança: Adicionar uma camada de validação que garanta 
que todas as variáveis de ambiente necessárias estão presentes durante 
a inicialização da aplicação torna o sistema mais robusto e à prova 
de erros de configuração.

📌 Sugestão de Implementação: Uma abordagem simples é verificar as 
variáveis no AppServiceProvider ou em um provedor de serviço dedicado:
if (config('services.google.client_id') === null) {
    throw new \Exception('A variável de ambiente GOOGLE_CLIENT_ID é obrigatória.');
}
*/