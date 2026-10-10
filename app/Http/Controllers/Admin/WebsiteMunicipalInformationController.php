<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MunicipalInformation;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebsiteMunicipalInformationController extends Controller
{
    public function index()
    {
        $municipalInfos = MunicipalInformation::orderBy('created_at', 'desc')->get();
        return view('admin.website.municipal-information.index', compact('municipalInfos'));
    }

    public function create()
    {
        return view('admin.website.municipal-information.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'announcements' => 'nullable|string',
            'announcements_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'announcements' => $request->announcements,
        ];

        // Handle announcements image upload
        if ($request->hasFile('announcements_image')) {
            $image = $request->file('announcements_image');
            $directory = 'assets/images/municipal';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $image->move($publicPath, $filename);
            $data['announcements_image'] = $path;
        }

        MunicipalInformation::create($data);

        return redirect()->route('admin.website.municipal-information.index')
            ->with('success', 'Municipal information created successfully!');
    }

    public function edit($id)
    {
        $municipalInfo = MunicipalInformation::findOrFail($id);
        $announcements = Announcement::ordered()->get();
        return view('admin.website.municipal-information.edit', compact('municipalInfo', 'announcements'));
    }

    public function announcements()
    {
        $municipalInfo = MunicipalInformation::first();
        $announcements = Announcement::ordered()->get();
        return view('admin.website.municipal-information.announcements', compact('municipalInfo', 'announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $directory = 'assets/images/announcements';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $image->move($publicPath, $filename);
            $data['image'] = $path;
        }

        Announcement::create($data);

        return redirect()->route('admin.website.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    public function updateAnnouncement(Request $request, $announcementId)
    {
        $announcement = Announcement::findOrFail($announcementId);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($announcement->image && file_exists(public_path($announcement->image))) {
                unlink(public_path($announcement->image));
            }

            $image = $request->file('image');
            $directory = 'assets/images/announcements';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $image->move($publicPath, $filename);
            $data['image'] = $path;
        }

        $announcement->update($data);

        return redirect()->route('admin.website.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function deleteAnnouncement($announcementId)
    {
        try {
            $announcement = Announcement::findOrFail($announcementId);

            // Delete image if exists
            if ($announcement->image && file_exists(public_path($announcement->image))) {
                unlink(public_path($announcement->image));
            }

            $announcement->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Announcement deleted successfully!']);
            }

            return redirect()->route('admin.website.announcements.index')
                ->with('success', 'Announcement deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting announcement: ' . $e->getMessage()], 500);
            }

            return redirect()->route('admin.website.announcements.index')
                ->with('error', 'Error deleting announcement: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $municipalInfo = MunicipalInformation::findOrFail($id);

            // Delete image files
            $imageFields = ['mayor_image', 'banner_image', 'city_seal', 'iso_certification'];
            foreach ($imageFields as $field) {
                if ($municipalInfo->$field && file_exists(public_path($municipalInfo->$field))) {
                    unlink(public_path($municipalInfo->$field));
                }
            }

            $municipalInfo->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Municipal information deleted successfully!']);
            }

            return redirect()->route('admin.website.municipal-information.index')
                ->with('success', 'Municipal information deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting municipal information: ' . $e->getMessage()], 500);
            }

            return redirect()->route('admin.website.municipal-information.index')
                ->with('error', 'Error deleting municipal information: ' . $e->getMessage());
        }
    }
}

