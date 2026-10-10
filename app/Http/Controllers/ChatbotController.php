<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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
                    'metadata' => $data['metadata'] ?? null,
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

    /**
     * Get questions from knowledge base for suggestions
     */
    public function getQuestions(Request $request)
    {
        try {
            $limit = $request->input('limit', 20);
            $response = Http::timeout(30)->get(
                config('services.chatbot.url', 'http://localhost:5000') . '/chatbot/knowledge-base/questions',
                [
                    'limit' => $limit,
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                return response()->json($data);
            } else {
                \Log::error('Get questions error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'questions' => [],
                ], $response->status());
            }
        } catch (\Throwable $e) {
            \Log::error('Get questions connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'questions' => [],
            ], 500);
        }
    }

    /**
     * View knowledge base management page (admin only)
     */
    public function viewKnowledgeBase()
    {
        // Check if user is admin (optional - can be removed if public access is desired)
        if (Auth::guard('admin')->check()) {
            return view('admin.chatbot.knowledge-base');
        }
        
        // For non-admin users, return JSON API response
        return $this->scanKnowledgeBase(request());
    }

    /**
     * Scan knowledge base to get list of documents/articles
     */
    public function scanKnowledgeBase(Request $request)
    {
        try {
            $response = Http::timeout(30)->get(
                config('services.chatbot.url', 'http://localhost:5000') . '/chatbot/knowledge-base/scan'
            );

            if ($response->successful()) {
                $data = $response->json();
                
                \Log::info('Knowledge base scan successful', [
                    'totalDocuments' => $data['totalDocuments'] ?? 0,
                ]);

                return response()->json($data);
            } else {
                \Log::error('Knowledge base scan error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Failed to scan knowledge base',
                ], $response->status());
            }
        } catch (\Throwable $e) {
            \Log::error('Knowledge base scan connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to connect to chatbot service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detailed information about a specific document
     */
    public function getDocument(Request $request, $documentId)
    {
        try {
            $response = Http::timeout(30)->get(
                config('services.chatbot.url', 'http://localhost:5000') . '/chatbot/knowledge-base/document/' . $documentId
            );

            if ($response->successful()) {
                $data = $response->json();
                return response()->json($data);
            } else {
                \Log::error('Document fetch error', [
                    'documentId' => $documentId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Failed to fetch document',
                ], $response->status());
            }
        } catch (\Throwable $e) {
            \Log::error('Document fetch connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to connect to chatbot service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new document in the knowledge base
     */
    public function createDocument(Request $request)
    {
        $request->validate([
            'displayName' => 'required|string|max:255',
            'mimeType' => 'required|string|in:text/plain,text/html,text/csv,application/pdf',
            'knowledgeTypes' => 'required|array',
            'knowledgeTypes.*' => 'string|in:FAQ,EXTRACTIVE_QA,ARTICLE_SUGGESTION',
            'content' => 'required|string',
        ]);

        try {
            $response = Http::timeout(60)->post(
                config('services.chatbot.url', 'http://localhost:5000') . '/chatbot/knowledge-base/document',
                [
                    'displayName' => $request->input('displayName'),
                    'mimeType' => $request->input('mimeType'),
                    'knowledgeTypes' => $request->input('knowledgeTypes'),
                    'content' => $request->input('content'),
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                
                \Log::info('Knowledge base document created', [
                    'displayName' => $request->input('displayName'),
                    'documentId' => $data['document']['id'] ?? null,
                ]);

                return response()->json($data);
            } else {
                \Log::error('Document creation error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Failed to create document: ' . ($response->json()['error'] ?? 'Unknown error'),
                ], $response->status());
            }
        } catch (\Throwable $e) {
            \Log::error('Document creation connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to connect to chatbot service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import CSV file to knowledge base
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
            'displayName' => 'nullable|string|max:255',
        ]);

        try {
            $file = $request->file('csv_file');
            $csvContent = file_get_contents($file->getRealPath());
            
            // Validate CSV has content
            if (empty($csvContent)) {
                return response()->json([
                    'success' => false,
                    'error' => 'CSV file is empty',
                ], 400);
            }

            $response = Http::timeout(120)->post(
                config('services.chatbot.url', 'http://localhost:5000') . '/chatbot/knowledge-base/import-csv',
                [
                    'csvContent' => $csvContent,
                    'displayName' => $request->input('displayName', 'FAQ Import - ' . date('Y-m-d')),
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                
                \Log::info('CSV imported to knowledge base', [
                    'displayName' => $request->input('displayName'),
                    'stats' => $data['stats'] ?? null,
                ]);

                return response()->json($data);
            } else {
                \Log::error('CSV import error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Failed to import CSV: ' . ($response->json()['error'] ?? 'Unknown error'),
                ], $response->status());
            }
        } catch (\Throwable $e) {
            \Log::error('CSV import connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to connect to chatbot service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a document from the knowledge base
     */
    public function deleteDocument(Request $request, $documentId)
    {
        try {
            $response = Http::timeout(60)->delete(
                config('services.chatbot.url', 'http://localhost:5000') . '/chatbot/knowledge-base/document/' . $documentId
            );

            if ($response->successful()) {
                $data = $response->json();
                
                \Log::info('Knowledge base document deleted', [
                    'documentId' => $documentId,
                ]);

                return response()->json($data);
            } else {
                \Log::error('Document deletion error', [
                    'documentId' => $documentId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete document: ' . ($response->json()['error'] ?? 'Unknown error'),
                ], $response->status());
            }
        } catch (\Throwable $e) {
            \Log::error('Document deletion connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to connect to chatbot service: ' . $e->getMessage(),
            ], 500);
        }
    }
}
