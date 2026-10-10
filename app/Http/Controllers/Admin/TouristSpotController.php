<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TouristSpotController extends Controller
{
    public function index()
    {
        $spots = TouristSpot::ordered()->get();
        return view('admin.tourist-spots.index', compact('spots'));
    }

    public function create()
    {
        return view('admin.tourist-spots.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'display_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $directory = 'assets/images/tourist-spots';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            // Move the image first
            $image->move($publicPath, $filename);

            // Optimize the image for better display
            $this->optimizeImage($publicPath . '/' . $filename);

            TouristSpot::create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $path,
                'display_order' => $request->display_order ?: TouristSpot::max('display_order') + 1,
                'is_active' => true
            ]);

            return redirect()->route('admin.tourist-spots.index')
                ->with('success', 'Tourist spot added successfully!');
        }

        return redirect()->back()->with('error', 'No image file provided.');
    }

    private function optimizeImage($imagePath)
    {
        // Get image info
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo)
            return;

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Target dimensions for tourist spot images (4:3 aspect ratio)
        $targetWidth = 400;
        $targetHeight = 300;

        // Calculate new dimensions maintaining aspect ratio
        $ratio = $width / $height;
        if ($ratio > ($targetWidth / $targetHeight)) {
            $newWidth = $targetWidth;
            $newHeight = $targetWidth / $ratio;
        } else {
            $newHeight = $targetHeight;
            $newWidth = $targetHeight * $ratio;
        }

        // Create image resource based on type
        switch ($mimeType) {
            case 'image/jpeg':
                $source = \imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $source = \imagecreatefrompng($imagePath);
                break;
            case 'image/gif':
                $source = \imagecreatefromgif($imagePath);
                break;
            default:
                return;
        }

        if (!$source)
            return;

        // Create new image with target dimensions
        $newImage = \imagecreatetruecolor($targetWidth, $targetHeight);

        // Fill with white background
        $white = \imagecolorallocate($newImage, 255, 255, 255);
        \imagefill($newImage, 0, 0, $white);

        // Calculate position to center the image
        $x = ($targetWidth - $newWidth) / 2;
        $y = ($targetHeight - $newHeight) / 2;

        // Resize and copy image
        \imagecopyresampled($newImage, $source, $x, $y, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save optimized image
        \imagejpeg($newImage, $imagePath, 85);

        // Clean up
        \imagedestroy($source);
        \imagedestroy($newImage);
    }

    public function edit($id)
    {
        $spot = TouristSpot::findOrFail($id);
        return view('admin.tourist-spots.edit', compact('spot'));
    }

    public function update(Request $request, $id)
    {
        $spot = TouristSpot::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'display_order' => $request->display_order ?: $spot->display_order,
            'is_active' => $request->has('is_active')
        ];

        if ($request->hasFile('image')) {
            if (file_exists(public_path($spot->image))) {
                unlink(public_path($spot->image));
            }

            $image = $request->file('image');
            $directory = 'assets/images/tourist-spots';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $image->move($publicPath, $filename);

            // Optimize the image for better display
            $this->optimizeImage($publicPath . '/' . $filename);

            $data['image'] = $path;
        }

        $spot->update($data);

        return redirect()->route('admin.tourist-spots.index')
            ->with('success', 'Tourist spot updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $spot = TouristSpot::findOrFail($id);

            if (file_exists(public_path($spot->image))) {
                unlink(public_path($spot->image));
            }

            $spot->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tourist spot deleted successfully!'
                ]);
            }

            return redirect()->route('admin.tourist-spots.index')
                ->with('success', 'Tourist spot deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting tourist spot. Please try again.'
                ], 500);
            }

            return redirect()->route('admin.tourist-spots.index')
                ->with('error', 'Error deleting tourist spot. Please try again.');
        }
    }
}
