<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AgentController extends Controller
{
    public function chat(Request $request)
    {
        set_time_limit(120);
        $clientData = Client::all();

        try {
            $message = $request->input('message');
            $response = Http::timeout(120)->post('http://127.0.0.1:11434/api/chat', [
                'model' => 'qwen2.5:7b',
                'think' => false,
                'stream' => false,

                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a AI assistant for a Australian migration case management application',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Client data:\n".$clientData->toJson()."\n\nUser question:\n".$message,
                    ],
                ],

            ]);

        } catch (ConnectionException $e) {
            \Log::error('Ollama connection failed', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'The AI service timed out. Try a simpler question.'], 504);
        } catch (\Throwable $e) {
            \Log::error('Agent chat error', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Something went wrong processing your request.'], 500);
        }

        return response()->json([
            'message' => $response->json('message.content'),
        ]);
    }
}
