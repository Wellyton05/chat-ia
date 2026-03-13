<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller

{
    public function index()
    {
        return view('chat');
    }

    public function message(Request $request)
    {
        $message = trim((string) $request->input('message', ''));
        $reply = $this->fakeReply($message);

        return response()->json([
            'reply' => $reply,
            'received' => $message,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
    protected function fakeReply(string $message): string
    {
        if ($message === '') {
            return 'Me conte um pouco mais para eu ajudar.';
        }

        $lower = mb_strtolower($message);

        if (str_contains($lower, 'preco') || str_contains($lower, 'valor') || str_contains($lower, 'plano')) {
            return 'Sobre valores, posso explicar os planos e te ajudar a escolher o melhor. Qual o seu objetivo?';
        }

        if (str_contains($lower, 'cancel') || str_contains($lower, 'encerrar')) {
            return 'Posso te orientar no cancelamento. Você quer pausar temporariamente ou encerrar de vez?';
        }

        if (str_contains($lower, 'suporte') || str_contains($lower, 'erro') || str_contains($lower, 'problema')) {
            return 'Sinto muito pelo transtorno. Me descreve o que aconteceu e quando começou?';
        }

        $fallbacks = [
            'Entendi! Vou analisar sua solicitação. Tem algum detalhe extra que possa me passar?',
            'Perfeito. Quer que eu abra um chamado para você com essa informação?',
            'Obrigado por explicar. Posso seguir com o atendimento por aqui mesmo.',
        ];

        return $fallbacks[array_rand($fallbacks)];
    }
}