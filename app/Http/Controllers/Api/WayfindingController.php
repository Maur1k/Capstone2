<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FloorResource;
use App\Http\Resources\PoiResource;
use App\Models\Floor;
use App\Models\Poi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WayfindingController extends Controller
{
    /**
     * Get all floors with POIs (always fresh data)
     */
    public function floors(): JsonResponse
    {
        try {
            // Always fetch fresh floors + POIs so kiosk reflects latest changes immediately
            $floors = Floor::with('pois')->get();

            return response()->json([
                'success' => true,
                'data' => FloorResource::collection($floors),
                'meta' => [
                    'total' => $floors->count(),
                    'generated_at' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching floors', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch floors'
            ], 500);
        }
    }

    /**
     * Get specific floor with POIs (always fresh data)
     */
    public function floor($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:floors,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid floor ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Always fetch fresh floor + POIs so kiosk reflects latest changes
            $floor = Floor::with('pois')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new FloorResource($floor),
                'meta' => [
                    'generated_at' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching floor', ['floor_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Floor not found'
            ], 404);
        }
    }

    /**
     * Get POIs by floor ID (always fresh data)
     */
    public function poisByFloor($floorId): JsonResponse
    {
        $validator = Validator::make(['floor_id' => $floorId], [
            'floor_id' => 'required|integer|exists:floors,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid floor ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Always fetch fresh POIs so kiosk reflects latest changes immediately
            $pois = Poi::where('floor_id', $floorId)->get();

            return response()->json([
                'success' => true,
                'data' => PoiResource::collection($pois),
                'meta' => [
                    'floor_id' => $floorId,
                    'total' => $pois->count(),
                    'generated_at' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching POIs by floor', ['floor_id' => $floorId, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch POIs'
            ], 500);
        }
    }

    /**
     * Get specific POI (always fresh data)
     */
    public function poi($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:pois,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid POI ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Always fetch fresh POI so kiosk reflects latest changes immediately
            $poi = Poi::with('floor')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new PoiResource($poi),
                'meta' => [
                    'generated_at' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching POI', ['poi_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'POI not found'
            ], 404);
        }
    }

    /**
     * Calculate route between two POIs (always fresh data)
     */
    public function route(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|integer|exists:pois,id',
            'to' => 'required|integer|exists:pois,id|different:from'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid route parameters',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fromId = $request->query('from');
            $toId = $request->query('to');

            // Always fetch fresh POI data so route reflects latest changes
            $from = Poi::findOrFail($fromId);
            $to = Poi::findOrFail($toId);

            // Enhanced route calculation with distance and duration estimation
            $routeData = [
                'points' => [
                    ['x' => $from->x, 'y' => $from->y, 'poi_id' => $from->id, 'name' => $from->name],
                    ['x' => $to->x, 'y' => $to->y, 'poi_id' => $to->id, 'name' => $to->name]
                ],
                'distance' => $this->calculateDistance($from->x, $from->y, $to->x, $to->y),
                'estimated_duration' => $this->estimateWalkingTime($from->x, $from->y, $to->x, $to->y),
                'from_poi' => new PoiResource($from),
                'to_poi' => new PoiResource($to)
            ];

            return response()->json([
                'success' => true,
                'data' => $routeData,
                'meta' => [
                    'calculated_at' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating route', [
                'from' => $request->query('from'),
                'to' => $request->query('to'),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate route'
            ], 500);
        }
    }

    /**
     * Calculate distance between two points
     */
    private function calculateDistance($x1, $y1, $x2, $y2): float
    {
        return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
    }

    /**
     * Estimate walking time based on distance (assuming 1.4 m/s walking speed)
     */
    private function estimateWalkingTime($x1, $y1, $x2, $y2): int
    {
        $distance = $this->calculateDistance($x1, $y1, $x2, $y2);
        $walkingSpeed = 1.4; // meters per second
        return (int) round($distance / $walkingSpeed);
    }

    /**
     * Clear cache for floors and POIs
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::forget('floors_with_pois');
            
            // Clear floor-specific caches
            $floors = Floor::pluck('id');
            foreach ($floors as $floorId) {
                Cache::forget("floor_{$floorId}_with_pois");
                Cache::forget("pois_floor_{$floorId}");
            }

            // Clear POI caches
            $pois = Poi::pluck('id');
            foreach ($pois as $poiId) {
                Cache::forget("poi_{$poiId}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing cache', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }
}
