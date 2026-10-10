<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\MunicipalInformation;
use App\Models\About;
use App\Models\Blog;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    /**
     * Display the website home page
     */
    public function index()
    {
        $municipalInfo = MunicipalInformation::first();
        $services = Service::with('types')->active()->get();

        return view('website.home', compact('municipalInfo', 'services'));
    }

    /**
     * Display the about page
     */
    public function about()
    {
        $municipalInfo = MunicipalInformation::first();

        return view('website.about', compact('municipalInfo'));
    }

    /**
     * Display the services page
     */
    public function services()
    {
        $services = Service::with('types')->active()->get();

        return view('website.services', compact('services'));
    }

    /**
     * Display the contact page
     */
    public function contact()
    {
        $municipalInfo = MunicipalInformation::first();

        return view('website.contact', compact('municipalInfo'));
    }

    /**
     * Display civil service appointment booking page
     */
    public function bookAppointment()
    {
        $services = Service::with('types')->active()->get();

        return view('website.book-appointment', compact('services'));
    }

    /**
     * Display document upload page
     */
    public function uploadDocuments()
    {
        return view('website.upload-documents');
    }

    /**
     * Display the website homepage with dynamic content
     */
    public function homepage()
    {
        $municipalInfo = MunicipalInformation::first();
        $about = About::where('is_active', true)->first();
        $blogs = Blog::published()->orderBy('published_at', 'desc')->take(3)->get();
        $announcements = \App\Models\Announcement::active()->ordered()->get();
        
        return view('website.homepage', compact('municipalInfo', 'about', 'blogs', 'announcements'));
    }
}