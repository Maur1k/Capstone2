<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Submit feedback
     */
    public function submit(Request $request)
    {
        try {
            // Log incoming request for debugging
            Log::info('Feedback submission request received', [
                'headers' => $request->headers->all(),
                'body' => $request->all(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
            ]);

            // Determine source (kiosk or website)
            $source = $request->header('X-Request-Source', 'kiosk');
            if (!in_array($source, ['kiosk', 'website'])) {
                $source = 'kiosk';
            }

            // For website feedback, tracking_number is optional
            // For kiosk feedback, tracking_number is optional (kiosk homepage doesn't have tracking number)
            $rules = [
                'rating' => 'required|integer|min:1|max:5',
                'message' => 'nullable|string|max:1000',
                'tracking_number' => 'nullable|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                Log::warning('Feedback validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create feedback
            $feedback = Feedback::create([
                'tracking_number' => $request->tracking_number ? trim($request->tracking_number) : null,
                'rating' => (int) $request->rating,
                'message' => $request->message ? trim($request->message) : null,
                'source' => $source,
            ]);

            Log::info('Feedback submitted successfully', [
                'feedback_id' => $feedback->id,
                'tracking_number' => $request->tracking_number,
                'rating' => $feedback->rating,
                'source' => $source,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully',
                'data' => [
                    'id' => $feedback->id,
                    'tracking_number' => $feedback->tracking_number,
                    'rating' => $feedback->rating,
                    'source' => $feedback->source,
                    'created_at' => $feedback->created_at->toISOString(),
                ]
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Feedback database error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Database error. Please check if the feedbacks table exists.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Feedback submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}

