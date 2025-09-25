<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function view()
    {
        return view('chatbot.index');
    }

    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'sessionId' => 'nullable|string',
            'language' => 'nullable|string|in:en,fil'
        ]);

        $sessionId = $request->input('sessionId') ?: Str::uuid()->toString();
        $message = $request->input('message');
        $language = $request->input('language', 'en');

        try {
            // Call Node.js chatbot service
            $response = Http::timeout(30)->post(config('services.chatbot.url', 'http://localhost:5000') . '/chatbot/message', [
                'message' => $message,
                'sessionId' => $sessionId,
                'language' => $language,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Log the response for debugging
                \Log::info('Chatbot service response', [
                    'message' => $message,
                    'language' => $language,
                    'reply' => $data['reply'] ?? 'No reply',
                    'sessionId' => $data['sessionId'] ?? $sessionId,
                ]);

                return response()->json([
                    'reply' => $data['reply'] ?? 'I did not understand that. Could you rephrase?',
                    'sessionId' => $data['sessionId'] ?? $sessionId,
                ]);
            } else {
                \Log::error('Chatbot service error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'reply' => 'Sorry, the chatbot service is temporarily unavailable. Please try again later.',
                    'sessionId' => $sessionId,
                ], 200);
            }
        } catch (\Throwable $e) {
            \Log::error('Chatbot service connection error: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Sorry, something went wrong connecting to the assistant. Please try again later.',
                'sessionId' => $sessionId,
            ], 200);
        }
    }
}
