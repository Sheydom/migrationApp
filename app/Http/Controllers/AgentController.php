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
             $response = Http::timeout(120)->post('http://ollama:11434/api/chat', [
                 'model' => 'qwen2.5:3b',
                 'think' => false,
                 'stream' => false,
                 'messages' => [
                     [
                         'role' => 'system',
                         'content' => 'You are a AI assistant for a Australian migration case management application. Answer based only on supplied database data.
                         Do not invent client information',
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
//  namespace App\Http\Controllers;
//  use App\Models\Client;
//  use Illuminate\Http\Request;
//  use Illuminate\Support\Facades\Http;
//  class AgentController extends Controller
//  {
//      public function chat(Request $request)
//      {
//          set_time_limit(120);
//          $clientData = Client::all();
//          $message = $request->input('message');
//          return response()->stream(function () use ($message, $clientData) {
//             try {
//                 $response = Http::timeout(120)
//                     ->withOptions([
//                         'stream' => true,
//                     ])
//                     ->post('http://127.0.0.1:11434/api/chat', [
//                         'model' => 'qwen2.5:3b',
//                         'think' => false,
//                         'stream' => true,

//                         'messages' => [
//                             [
//                                 'role' => 'system',
//                                 'content' => 'You are an AI assistant for an Australian migration case management application.
//                                 Answer based only on supplied database data.
//                                 Do not invent client information.',
//                             ],
//                             [
//                                 'role' => 'user',
//                                 'content' => "Client data:\n"
//                                     . $clientData->toJson()
//                                     . "\n\nUser question:\n"
//                                     . $message,
//                             ],
//                         ],
//                     ]);

//                 $body = $response->toPsrResponse()->getBody();

//                 while (! $body->eof()) {
//                     $chunk = $body->read(1024);

//                     if ($chunk !== '') {
//                         echo $chunk;

//                         if (ob_get_level() > 0) {
//                             ob_flush();
//                         }

//                         flush();
//                     }
//                 }

//             } catch (\Throwable $e) {
//                 \Log::error('Agent chat error', [
//                     'error' => $e->getMessage(),
//                 ]);

//                 echo json_encode([
//                     'error' => 'Something went wrong processing your request.',
//                     'done' => true,
//                 ]) . "\n";

//                 flush();
//             }

//         }, 200, [
//             'Content-Type' => 'application/x-ndjson',
//             'Cache-Control' => 'no-cache',
//             'X-Accel-Buffering' => 'no',
//         ]);
//     }
// }
