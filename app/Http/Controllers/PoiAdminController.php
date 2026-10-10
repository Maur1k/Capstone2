<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Poi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PoiAdminController extends Controller
{
    public function index(Request $request)
    {
        $floors = Floor::orderBy('name')->get();

        $selectedFloorId = $request->query('floor_id');

        $poisQuery = Poi::with('floor')->orderBy('name');
        if ($selectedFloorId) {
            $poisQuery->where('floor_id', $selectedFloorId);
        }
        $pois = $poisQuery->get();

        $editPoi = null;

        if ($request->filled('edit')) {
            $editPoi = Poi::with('floor')->find($request->input('edit'));
        }

        return view('pois.manage', [
            'floors' => $floors,
            'pois' => $pois,
            'selectedFloorId' => $selectedFloorId,
            'editPoi' => $editPoi,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $poi = Poi::create($data);
        $this->clearWayfindingCaches($poi->floor_id);

        return redirect()
            ->route('admin.pois.index')
            ->with('status', 'POI created successfully.');
    }

    public function update(Request $request, Poi $poi)
    {
        $originalFloorId = $poi->floor_id;
        $data = $this->validatedData($request);
        $poi->update($data);
        $this->clearWayfindingCaches($poi->floor_id);
        if ($originalFloorId !== $poi->floor_id) {
            $this->clearWayfindingCaches($originalFloorId);
        }

        return redirect()
            ->route('admin.pois.index')
            ->with('status', 'POI updated successfully.');
    }

    public function destroy(Poi $poi)
    {
        $floorId = $poi->floor_id;
        $poi->delete();
        $this->clearWayfindingCaches($floorId);

        return redirect()
            ->route('admin.pois.index')
            ->with('status', 'POI deleted.');
    }

    /**
     * Manually refresh kiosk wayfinding caches from the admin UI.
     * This clears floor/POI caches so the kiosk sees latest data
     * without needing any artisan commands.
     */
    public function refreshKiosk()
    {
        // Clear global floors cache
        Cache::forget('floors_with_pois');

        // Clear per-floor and per-POI caches used by WayfindingController
        $floorIds = Floor::pluck('id');
        foreach ($floorIds as $id) {
            Cache::forget("floor_{$id}_with_pois");
            Cache::forget("pois_floor_{$id}");
        }

        $poiIds = Poi::pluck('id');
        foreach ($poiIds as $id) {
            Cache::forget("poi_{$id}");
        }

        return redirect()
            ->route('admin.pois.index')
            ->with('status', 'Kiosk data cache refreshed. You can reload the kiosk screen to see latest POIs.');
    }

    protected function validatedData(Request $request): array
    {
        $rules = [
            'floor_id' => ['required', 'exists:floors,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'x' => ['required', 'numeric'],
            'y' => ['required', 'numeric'],
            'description' => ['nullable', 'string', 'max:500'],
            'model_position_x' => ['nullable', 'numeric'],
            'model_position_y' => ['nullable', 'numeric'],
            'model_position_z' => ['nullable', 'numeric'],
            'additional_meta' => ['nullable', 'json'],
        ];

        // Add validation rules for dynamic waypoints
        $waypoints = $request->input('waypoints', []);
        foreach ($waypoints as $index => $waypoint) {
            $rules["waypoints.{$index}.x"] = ['nullable', 'numeric'];
            $rules["waypoints.{$index}.y"] = ['nullable', 'numeric'];
            $rules["waypoints.{$index}.z"] = ['nullable', 'numeric'];
            $rules["waypoints.{$index}.note"] = ['nullable', 'string', 'max:255'];
        }

        // Also support legacy waypoint_1_x, waypoint_2_x format for backward compatibility
        $rules['waypoint_1_x'] = ['nullable', 'numeric'];
        $rules['waypoint_1_y'] = ['nullable', 'numeric'];
        $rules['waypoint_1_z'] = ['nullable', 'numeric'];
        $rules['waypoint_1_note'] = ['nullable', 'string', 'max:255'];
        $rules['waypoint_2_x'] = ['nullable', 'numeric'];
        $rules['waypoint_2_y'] = ['nullable', 'numeric'];
        $rules['waypoint_2_z'] = ['nullable', 'numeric'];
        $rules['waypoint_2_note'] = ['nullable', 'string', 'max:255'];

        $validated = $request->validate($rules);

        return [
            'floor_id' => $validated['floor_id'],
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'x' => $validated['x'],
            'y' => $validated['y'],
            'meta' => $this->buildMeta($validated),
        ];
    }

    protected function buildMeta(array $data): array
    {
        $meta = [];

        if (!empty($data['description'])) {
            $meta['description'] = $data['description'];
        }

        $modelPosition = array_filter([
            'x' => $data['model_position_x'] ?? null,
            'y' => $data['model_position_y'] ?? null,
            'z' => $data['model_position_z'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        if (!empty($modelPosition)) {
            $meta['modelPosition'] = array_map('floatval', $modelPosition);
        }

        $waypoints = [];
        
        // Process new dynamic waypoints array format
        if (isset($data['waypoints']) && is_array($data['waypoints'])) {
            foreach ($data['waypoints'] as $waypointData) {
                if (!is_array($waypointData)) {
                    continue;
                }
                
                $wx = $waypointData['x'] ?? null;
                $wy = $waypointData['y'] ?? null;
                $wz = $waypointData['z'] ?? null;
                $note = $waypointData['note'] ?? null;

                if ($wx === null && $wy === null && $wz === null && empty($note)) {
                    continue;
                }

                $waypoint = array_filter([
                    'x' => $wx,
                    'y' => $wy,
                    'z' => $wz,
                ], fn ($value) => $value !== null && $value !== '');

                $waypoint = array_map('floatval', $waypoint);

                if (!empty($note)) {
                    $waypoint['note'] = $note;
                }

                if (!empty($waypoint)) {
                    $waypoints[] = $waypoint;
                }
            }
        }
        
        // Also support legacy waypoint_1_x, waypoint_2_x format for backward compatibility
        if (empty($waypoints)) {
        for ($index = 1; $index <= 2; $index++) {
            $wx = $data["waypoint_{$index}_x"] ?? null;
            $wy = $data["waypoint_{$index}_y"] ?? null;
            $wz = $data["waypoint_{$index}_z"] ?? null;
            $note = $data["waypoint_{$index}_note"] ?? null;

            if ($wx === null && $wy === null && $wz === null && empty($note)) {
                continue;
            }

            $waypoint = array_filter([
                'x' => $wx,
                'y' => $wy,
                'z' => $wz,
            ], fn ($value) => $value !== null && $value !== '');

            $waypoint = array_map('floatval', $waypoint);

            if (!empty($note)) {
                $waypoint['note'] = $note;
            }

            if (!empty($waypoint)) {
                $waypoints[] = $waypoint;
                }
            }
        }

        if (!empty($waypoints)) {
            $meta['waypoints'] = $waypoints;
        }

        if (!empty($data['additional_meta'])) {
            $additional = json_decode($data['additional_meta'], true);
            if (is_array($additional)) {
                $meta = array_replace_recursive($meta, $additional);
            }
        }

        return $meta;
    }

    protected function clearWayfindingCaches(?int $floorId = null): void
    {
        Cache::forget('floors_with_pois');
        if ($floorId) {
            Cache::forget("floor_{$floorId}_with_pois");
        }
    }
}











