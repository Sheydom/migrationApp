<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AgentController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');
        $response = Http::post('http://127.0.0.1:11434/api/chat', [
            'model' => 'qwen2.5:1.5b',
            'think'=> false,
            'stream'=>false,

            'messages' => [
                [
                    'role'=>'system',
                    'content'=>'You are a AI assistant for a migration case management application'
                ],
                [
                    'role' => 'user',
                    'content' => $message,
                ]
            ],

        ]);

        return response()->json([
            'message' => $response->json('message.content')
        ]);
    }
}