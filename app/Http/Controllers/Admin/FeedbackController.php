<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedbacks
     */
    public function index(Request $request)
    {
        $query = Feedback::query()->orderBy('created_at', 'desc');

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Search by tracking number
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('tracking_number', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply pagination with query string preservation
        $feedbacks = $query->paginate(20)->withQueryString();

        // Calculate statistics (use filtered query for accurate stats)
        $statsQuery = Feedback::query();
        
        // Apply same filters to stats
        if ($request->filled('rating')) {
            $statsQuery->where('rating', $request->rating);
        }
        if ($request->filled('source')) {
            $statsQuery->where('source', $request->source);
        }
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $statsQuery->where(function($q) use ($searchTerm) {
                $q->where('tracking_number', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $stats = [
            'total' => $statsQuery->count(),
            'average_rating' => $statsQuery->avg('rating') ? round($statsQuery->avg('rating'), 2) : 0,
            'by_rating' => $statsQuery->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->orderBy('rating', 'desc')
                ->pluck('count', 'rating')
                ->toArray(),
            'by_source' => $statsQuery->selectRaw('source, COUNT(*) as count')
                ->groupBy('source')
                ->pluck('count', 'source')
                ->toArray(),
        ];

        return view('admin.feedbacks.index', compact('feedbacks', 'stats'));
    }

    /**
     * Display the specified feedback
     */
    public function show(Feedback $feedback)
    {
        return view('admin.feedbacks.show', compact('feedback'));
    }

    /**
     * Delete feedback
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return redirect()->route('admin.feedbacks.index')
            ->with('success', 'Feedback deleted successfully.');
    }
}

