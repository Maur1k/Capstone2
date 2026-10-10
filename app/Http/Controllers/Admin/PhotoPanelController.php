<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoPanelImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PhotoPanelController extends Controller
{
    public function index()
    {
        $images = PhotoPanelImage::ordered()->get();
        return view('admin.photo-panel.index', compact('images'));
    }

    public function create()
    {
        return view('admin.photo-panel.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'alt_text' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Create directory in public/assets
            $directory = 'assets/images/photo-panel';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            // Store the image directly to public directory
            $image->move($publicPath, $filename);

            // Create database record
            PhotoPanelImage::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $path,
                'alt_text' => $request->alt_text ?: $request->title,
                'display_order' => $request->display_order ?: PhotoPanelImage::max('display_order') + 1,
                'is_active' => true
            ]);

            return redirect()->route('admin.photo-panel.index')
                ->with('success', 'Image uploaded successfully!');
        }

        return redirect()->back()->with('error', 'No image file provided.');
    }

    public function edit($id)
    {
        $image = PhotoPanelImage::findOrFail($id);
        return view('admin.photo-panel.edit', compact('image'));
    }

    public function update(Request $request, $id)
    {
        $image = PhotoPanelImage::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'alt_text' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'alt_text' => $request->alt_text,
            'display_order' => $request->display_order ?: $image->display_order,
            'is_active' => $request->has('is_active')
        ];

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if (file_exists(public_path($image->image_path))) {
                unlink(public_path($image->image_path));
            }

            $newImage = $request->file('image');
            $directory = 'assets/images/photo-panel';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $newImage->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            // Store the image directly to public directory
            $newImage->move($publicPath, $filename);
            $data['image_path'] = $path;
        }

        $image->update($data);

        return redirect()->route('admin.photo-panel.index')
            ->with('success', 'Image updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $image = PhotoPanelImage::findOrFail($id);

            // Delete image file
            if (file_exists(public_path($image->image_path))) {
                unlink(public_path($image->image_path));
            }

            $image->delete();

            // Check if request is AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Image deleted successfully!']);
            }

            return redirect()->route('admin.photo-panel.index')
                ->with('success', 'Image deleted successfully!');
        } catch (\Exception $e) {
            // Check if request is AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting image: ' . $e->getMessage()], 500);
            }

            return redirect()->route('admin.photo-panel.index')
                ->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|integer|exists:photo_panel_images,id',
            'images.*.display_order' => 'required|integer|min:0'
        ]);

        foreach ($request->images as $imageData) {
            PhotoPanelImage::where('id', $imageData['id'])
                ->update(['display_order' => $imageData['display_order']]);
        }

        return response()->json(['success' => true]);
    }
}