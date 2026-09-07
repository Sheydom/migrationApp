<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Client;
use App\Models\ChecklistItem;

class AgentController extends Controller
{
    public function chat(Request $request)
    {
        $clientData = Client::all();
       

        $message = $request->input('message');
        $response = Http::post('http://127.0.0.1:11434/api/chat', [
            'model' => 'qwen2.5:3b',
            'think'=> false,
            'stream'=>false,

            'messages' => [
                [
                    'role'=>'system',
                    'content'=>'You are a AI assistant for a Australian migration case management application'
                ],
                [
                    'role' => 'user',
                    'content' =>'Client data:\n' . $clientData->toJson() . '\n\nUser question:\n' . $message,
                ]
            ],

        ]);

        return response()->json([
            'message' => $response->json('message.content')
        ]);
    }
}