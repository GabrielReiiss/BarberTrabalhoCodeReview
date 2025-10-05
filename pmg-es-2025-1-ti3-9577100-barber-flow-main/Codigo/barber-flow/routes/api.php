<?php

use App\Mail\Cancel;
use App\Mail\Confirm;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\TwiML\MessagingResponse;

Route::post('/webhook/whatsapp', function (Request $request) {
    ds(1);
    $from = $request->input('From');               
    $body = trim($request->input('Body'));         

    Log::info("Recebido de $from: $body");

    $phone = str_replace('whatsapp:+55', '', $from);
    
    $ddd     = substr($phone, 0, 2);
    $resto   = substr($phone, 2);

    $phone = "($ddd) 9$resto";

    $appointment = Appointment::whereHas('user', function ($query) use ($phone) {
        $query->where('phone', $phone);
    })
    ->whereNull('confirmed_at')
    ->latest()
    ->first();

    $msg = new MessagingResponse();

    if ($appointment) {
        if ($body === '1') 
        {
            $appointment->update(['confirmed_at' => now()]);
            
            Mail::to($appointment->user->email)->send(new Confirm($appointment));

            $msg->message(
                "*✅ Agendamento Confirmado!*\n\n" .
                "Seu horário foi confirmado com sucesso! ✨\n\n" .
                "💇‍♂️ *Serviço:* {$appointment->service->name}\n" .
                "🧑‍🔧 *Profissional:* {$appointment->barber->name}\n\n" .
                "Estamos te esperando! 😄\nSe tiver qualquer dúvida, é só nos chamar.\n\n" .
                "*Obrigado por escolher nossos serviços!* 💈"
            );
        } 
        elseif ($body === '2') 
        {
            $appointment->delete(); 
            Mail::to($appointment->user->email)->send(new Cancel($appointment));
            $msg->message(
                "*❌ Agendamento Cancelado!*\n\n" .
                "Recebemos sua solicitação de cancelamento para o serviço *{$appointment->service->name}* e tudo foi atualizado com sucesso. 🗓️\n\n" .
                "Esperamos poder te atender em uma próxima oportunidade! 😊\n\n" .
                "Se quiser reagendar ou tirar alguma dúvida, é só nos chamar. 📲\n\n" .
                "*Agradecemos por considerar nossos serviços!* 💈"
            );
        } 
        else 
        {
            $msg->message("🤖: Responda com *1* para confirmar ou *2* para cancelar.");
        }
    } else {
        $msg->message("⚠️ Nenhum agendamento pendente encontrado.");
    }

    return response($msg, 200)->header('Content-Type', 'text/xml');
});

/*
COMENTÁRIO 1:
🔍 Sugestão de Melhoria: Toda a lógica para o webhook do WhatsApp 
está dentro de uma função de closure no arquivo de rotas. Arquivos 
de rota devem apenas definir endpoints e delegar a ação para 
controllers, nunca conter a lógica de negócio em si.

Benefícios da Mudança: Melhora drasticamente a organização e o reuso. 
Permite o cache de rotas do Laravel (php artisan route:cache), que 
não funciona com closures. Facilita os testes, pois a lógica pode 
ser testada em uma classe de controller isoladamente.

📌 Sugestão de Implementação:
Criar um novo controller: php artisan make:controller Api\\WhatsAppWebhookController.
Mover toda a lógica da closure para um método handle neste controller.
Atualizar a rota em api.php:
use App\Http\Controllers\Api\WhatsAppWebhookController;
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle']);




COMENTÁRIO 2:
🔍 Sugestão de Melhoria: Seguindo a sugestão anterior, mesmo dentro de 
um controller, a lógica do webhook viola o Princípio da Responsabilidade 
Única (SRP). Ela formata dados, consulta o banco, atualiza registros, envia 
e-mails e monta uma resposta XML.

Benefícios da Mudança: Sugiro criar uma classe WhatsAppWebhookService 
que receba os dados do webhook e orquestre as ações. Isso desacopla a 
lógica de negócio do framework, tornando-a mais testável, manutenível e clara.

📌 Sugestão de Implementação: O controller ficaria muito simples:
public function handle(Request $request) {
    $responseMessage = $this->webhookService->process($request->all());
    return response($responseMessage, 200)->header('Content-Type', 'text/xml');
}




COMENTÁRIO 3:
🔍 Sugestão de Melhoria: O bloco de código que manipula a string do número 
de telefone é complexo e pode ser frágil.

Benefícios da Mudança: Extrair essa lógica para uma classe dedicada ou um 
helper (ex: PhoneNumberFormatter) centraliza a responsabilidade, permite 
reuso e facilita a criação de testes unitários específicos para essa formatação.

📌 Sugestão de Implementação: Criar uma classe App\Utils\PhoneNumberFormatter 
com um método estático formatForDb($whatsappPhoneString).
*/