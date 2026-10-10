<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceStep;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    // Services
    public function index()
    {
        $services = Service::orderBy('name')->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Handle file upload - file takes precedence over icon_path
        if ($request->hasFile('icon')) {
            try {
                $file = $request->file('icon');

                // Ensure the directory exists
                $iconsDir = storage_path('app/public/icons');
                if (!file_exists($iconsDir)) {
                    mkdir($iconsDir, 0755, true);
                }

                // Sanitize filename - preserve extension
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nameWithoutExt);
                $filename = time() . '_' . $sanitizedName . '.' . $extension;

                // Use Storage facade for more reliable file handling
                $stored = Storage::disk('public')->putFileAs('icons', $file, $filename);

                if ($stored) {
                    // Verify file was actually stored
                    $fullPath = Storage::disk('public')->path('icons/' . $filename);

                    if (!file_exists($fullPath)) {
                        Log::error('File stored but not found (new service)', [
                            'stored_path' => $stored,
                            'full_path' => $fullPath,
                            'filename' => $filename
                        ]);
                        return redirect()->route('admin.services.create')->with('error', 'File was saved but could not be verified.');
                    }

                    // Generate the correct storage URL
                    $data['icon_path'] = '/storage/icons/' . $filename;

                    // Log successful upload
                    Log::info('Icon uploaded successfully (new service)', [
                        'filename' => $filename,
                        'stored_path' => $stored,
                        'full_path' => $fullPath,
                        'file_exists' => file_exists($fullPath),
                        'file_size' => filesize($fullPath)
                    ]);
                } else {
                    return redirect()->route('admin.services.create')->with('error', 'Failed to upload icon file.');
                }
            } catch (\Exception $e) {
                Log::error('Icon upload error (new service): ' . $e->getMessage());
                return redirect()->route('admin.services.create')->with('error', 'Error uploading icon: ' . $e->getMessage());
            }
        } elseif (empty($request->icon_path)) {
            // If no file and no icon_path, set default
            $data['icon_path'] = '/assets/icons/civil-services.png';
        }

        Service::create($data);
        return redirect()->route('admin.services.index')->with('status', 'Service created.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Handle file upload - file takes precedence over icon_path
        if ($request->hasFile('icon')) {
            try {
                $file = $request->file('icon');

                // Ensure the directory exists
                $iconsDir = storage_path('app/public/icons');
                if (!file_exists($iconsDir)) {
                    mkdir($iconsDir, 0755, true);
                }

                // Sanitize filename - preserve extension
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nameWithoutExt);
                $filename = time() . '_' . $sanitizedName . '.' . $extension;

                // Use Storage facade for more reliable file handling
                $stored = Storage::disk('public')->putFileAs('icons', $file, $filename);

                if ($stored) {
                    // Verify the file was actually stored
                    $fullPath = Storage::disk('public')->path('icons/' . $filename);

                    if (!file_exists($fullPath)) {
                        Log::error('File stored but not found (update service)', [
                            'stored_path' => $stored,
                            'full_path' => $fullPath,
                            'filename' => $filename,
                            'disk_path' => Storage::disk('public')->path('icons')
                        ]);
                        return redirect()->route('admin.services.edit', $service)->with('error', 'File was saved but could not be verified.');
                    }

                    // Generate the correct storage URL
                    $data['icon_path'] = '/storage/icons/' . $filename;

                    // Log successful upload
                    Log::info('Icon uploaded successfully (update service)', [
                        'filename' => $filename,
                        'stored_path' => $stored,
                        'full_path' => $fullPath,
                        'file_exists' => file_exists($fullPath),
                        'file_size' => filesize($fullPath)
                    ]);

                    // Delete old icon file if it exists and is in storage
                    if ($service->icon_path && strpos($service->icon_path, '/storage/icons/') === 0) {
                        $oldFilename = basename($service->icon_path);
                        $oldPath = 'public/icons/' . $oldFilename;
                        if (Storage::exists($oldPath)) {
                            Storage::delete($oldPath);
                            Log::info('Deleted old icon: ' . $oldPath);
                        }
                    }
                } else {
                    return redirect()->route('admin.services.edit', $service)->with('error', 'Failed to upload icon file.');
                }
            } catch (\Exception $e) {
                Log::error('Icon upload error (update service): ' . $e->getMessage());
                return redirect()->route('admin.services.edit', $service)->with('error', 'Error uploading icon: ' . $e->getMessage());
            }
        }
        // If no file uploaded, keep existing icon_path (don't overwrite it)

        $service->update($data);
        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    // Types
    public function types(Service $service)
    {
        $types = $service->types()->orderBy('display_order')->orderBy('name')->get();
        return view('admin.services.types', compact('service', 'types'));
    }

    public function typeStore(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
        ]);

        // Handle file upload - file takes precedence over icon_path
        if ($request->hasFile('icon')) {
            try {
                $file = $request->file('icon');

                // Ensure the directory exists
                $iconsDir = storage_path('app/public/icons');
                if (!file_exists($iconsDir)) {
                    mkdir($iconsDir, 0755, true);
                }

                // Sanitize filename - preserve extension
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nameWithoutExt);
                $filename = time() . '_' . $sanitizedName . '.' . $extension;

                // Ensure directory exists
                $iconsDir = storage_path('app/public/icons');
                if (!file_exists($iconsDir)) {
                    mkdir($iconsDir, 0755, true);
                }

                // Use Storage facade for more reliable file handling
                $stored = Storage::disk('public')->putFileAs('icons', $file, $filename);

                if ($stored) {
                    // Verify file was actually stored
                    $fullPath = Storage::disk('public')->path('icons/' . $filename);

                    if (!file_exists($fullPath)) {
                        Log::error('File stored but not found (new type)', [
                            'stored_path' => $stored,
                            'full_path' => $fullPath,
                            'filename' => $filename
                        ]);
                        return redirect()->route('admin.services.types', $service)->with('error', 'File was saved but could not be verified.');
                    }

                    // Generate the correct storage URL
                    $data['icon_path'] = '/storage/icons/' . $filename;

                    // Log successful upload
                    Log::info('Icon uploaded successfully (new type)', [
                        'filename' => $filename,
                        'stored_path' => $stored,
                        'full_path' => $fullPath,
                        'file_exists' => file_exists($fullPath),
                        'file_size' => filesize($fullPath)
                    ]);
                } else {
                    return redirect()->route('admin.services.types', $service)->with('error', 'Failed to upload icon file.');
                }
            } catch (\Exception $e) {
                Log::error('Icon upload error (new type): ' . $e->getMessage());
                return redirect()->route('admin.services.types', $service)->with('error', 'Error uploading icon: ' . $e->getMessage());
            }
        } elseif (empty($request->icon_path)) {
            // If no file and no icon_path, set default
            $data['icon_path'] = '/assets/icons/civil-services.png';
        }

        // Set display_order to be last
        $maxOrder = $service->types()->max('display_order') ?? 0;
        $data['display_order'] = $maxOrder + 1;

        $type = $service->types()->create($data);

        // Log the creation for debugging
        Log::info('Type created', [
            'type_id' => $type->id,
            'icon_path' => $type->icon_path,
            'service_id' => $service->id
        ]);

        // Force redirect to same page to ensure fresh data is loaded
        return redirect()->route('admin.services.types', $service)->with('status', 'Type added.');
    }

    public function typeUpdate(Request $request, Service $service, ServiceType $type)
    {
        abort_unless($type->service_id === $service->id, 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Handle file upload - file takes precedence over icon_path
        if ($request->hasFile('icon')) {
            try {
                $file = $request->file('icon');

                // Ensure the directory exists
                $iconsDir = storage_path('app/public/icons');
                if (!file_exists($iconsDir)) {
                    mkdir($iconsDir, 0755, true);
                }

                // Sanitize filename - preserve extension
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nameWithoutExt);
                $filename = time() . '_' . $sanitizedName . '.' . $extension;

                // Ensure directory exists
                $iconsDir = storage_path('app/public/icons');
                if (!file_exists($iconsDir)) {
                    mkdir($iconsDir, 0755, true);
                }

                // Use Storage facade for more reliable file handling
                $stored = Storage::disk('public')->putFileAs('icons', $file, $filename);

                if ($stored) {
                    // Verify the file was actually stored
                    $fullPath = Storage::disk('public')->path('icons/' . $filename);

                    if (!file_exists($fullPath)) {
                        Log::error('File stored but not found', [
                            'stored_path' => $stored,
                            'full_path' => $fullPath,
                            'filename' => $filename,
                            'disk_path' => Storage::disk('public')->path('icons')
                        ]);
                        return redirect()->route('admin.services.types', $service)->with('error', 'File was saved but could not be verified.');
                    }

                    // Generate the correct storage URL
                    $data['icon_path'] = '/storage/icons/' . $filename;

                    // Log successful upload
                    Log::info('Icon uploaded successfully', [
                        'filename' => $filename,
                        'stored_path' => $stored,
                        'full_path' => $fullPath,
                        'file_exists' => file_exists($fullPath),
                        'file_size' => filesize($fullPath)
                    ]);

                    // Delete old icon file if it exists and is in storage
                    if ($type->icon_path && strpos($type->icon_path, '/storage/icons/') === 0) {
                        $oldFilename = basename($type->icon_path);
                        $oldPath = 'public/icons/' . $oldFilename;
                        if (Storage::exists($oldPath)) {
                            Storage::delete($oldPath);
                            Log::info('Deleted old icon: ' . $oldPath);
                        }
                    }

                    Log::info('Icon uploaded successfully: ' . $data['icon_path']);
                } else {
                    return back()->with('error', 'Failed to upload icon file.');
                }
            } catch (\Exception $e) {
                Log::error('Icon upload error: ' . $e->getMessage());
                return back()->with('error', 'Error uploading icon: ' . $e->getMessage());
            }
        }
        // If no file uploaded, keep existing icon_path (don't overwrite it)

        $type->update($data);

        // Touch the model to ensure updated_at is refreshed
        $type->touch();

        // Refresh the model to get latest data
        $type->refresh();

        // Log the update for debugging
        Log::info('Type updated', [
            'type_id' => $type->id,
            'icon_path' => $type->icon_path,
            'updated_at' => $type->updated_at
        ]);

        // Force redirect to same page to ensure fresh data is loaded
        return redirect()->route('admin.services.types', $service)->with('status', 'Type updated.');
    }

    public function typeDestroy(Service $service, ServiceType $type)
    {
        abort_unless($type->service_id === $service->id, 404);
        $type->delete();
        return back()->with('status', 'Type deleted.');
    }

    public function typesReorder(Request $request, Service $service)
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'exists:service_types,id']
        ]);

        foreach ($request->order as $index => $typeId) {
            ServiceType::where('id', $typeId)
                ->where('service_id', $service->id)
                ->update(['display_order' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);
    }

    // Steps
    public function steps(Service $service, ServiceType $type)
    {
        abort_unless($type->service_id === $service->id, 404);
        $steps = $type->steps()->orderBy('display_order')->get();
        return view('admin.services.steps', compact('service', 'type', 'steps'));
    }

    public function stepStore(Request $request, Service $service, ServiceType $type)
    {
        abort_unless($type->service_id === $service->id, 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);
        $type->steps()->create($data + ['display_order' => $type->steps()->max('display_order') + 1]);
        return back()->with('status', 'Step added.');
    }

    public function stepUpdate(Request $request, Service $service, ServiceType $type, ServiceStep $step)
    {
        abort_unless($type->service_id === $service->id && $step->service_type_id === $type->id, 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $step->update($data);
        return back()->with('status', 'Step updated.');
    }

    public function stepDestroy(Service $service, ServiceType $type, ServiceStep $step)
    {
        abort_unless($type->service_id === $service->id && $step->service_type_id === $type->id, 404);
        $step->delete();
        return back()->with('status', 'Step deleted.');
    }

}


