<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Blog;
use App\Models\MunicipalInformation;
use Illuminate\Database\Seeder;

class WebsiteContentSeeder extends Seeder
{
    /**
     * Seed the website content (About, Blog, Municipal Information)
     */
    public function run(): void
    {
        // Seed About Content
        About::firstOrCreate(
            ['title' => 'About Alaminos City'],
            [
                'content' => '<h2>Welcome to Alaminos City</h2>
                <p>Alaminos City is a vibrant and progressive city located in the province of Pangasinan, Philippines. Known as the "Home of the Hundred Islands National Park," our city is a premier tourist destination that combines natural beauty with modern governance.</p>
                
                <h3>Our Mission</h3>
                <p>To provide efficient, transparent, and citizen-centered public services that enhance the quality of life for all residents and visitors of Alaminos City.</p>
                
                <h3>Our Vision</h3>
                <p>To be a model city in public service delivery, environmental conservation, and sustainable development, recognized for excellence in governance and community engagement.</p>
                
                <h3>What We Offer</h3>
                <p>Through Click2Serve, we provide convenient access to various municipal services including civil registry, business permits, engineering services, and more. Our online platform and kiosk system ensure that services are accessible to everyone, anytime, anywhere.</p>
                
                <p>Experience the convenience of modern public service delivery. We are committed to serving you better!</p>',
                'is_active' => true,
            ]
        );

        // Seed Blog Posts
        $blogPosts = [
            [
                'title' => 'New Online Services Now Available',
                'content' => '<p>We are excited to announce the launch of our new online services platform! Residents can now access various municipal services from the comfort of their homes.</p>
                
                <p>Key features include:</p>
                <ul>
                    <li>Online appointment booking</li>
                    <li>Document tracking system</li>
                    <li>Digital document submission</li>
                    <li>Real-time status updates</li>
                </ul>
                
                <p>This initiative is part of our commitment to digital transformation and improved public service delivery.</p>',
                'author' => 'City Administrator',
                'published_at' => now(),
                'is_published' => true,
            ],
            [
                'title' => 'Hundred Islands Tourism Update',
                'content' => '<p>The Hundred Islands National Park continues to be one of the most popular tourist destinations in the region. We are implementing new measures to ensure sustainable tourism while preserving the natural beauty of our islands.</p>
                
                <p>Recent improvements include:</p>
                <ul>
                    <li>Enhanced visitor facilities</li>
                    <li>Improved safety protocols</li>
                    <li>Eco-friendly initiatives</li>
                    <li>Better transportation services</li>
                </ul>
                
                <p>Plan your visit today and experience the beauty of the Hundred Islands!</p>',
                'author' => 'Tourism Office',
                'published_at' => now()->subDays(5),
                'is_published' => true,
            ],
            [
                'title' => 'Community Health Programs',
                'content' => '<p>Our city continues to prioritize the health and well-being of all residents through various community health programs and initiatives.</p>
                
                <p>Ongoing programs include:</p>
                <ul>
                    <li>Free health check-ups</li>
                    <li>Vaccination campaigns</li>
                    <li>Health education seminars</li>
                    <li>Medical assistance programs</li>
                </ul>
                
                <p>For more information, please visit the City Health Office or contact us through our online platform.</p>',
                'author' => 'City Health Office',
                'published_at' => now()->subDays(10),
                'is_published' => true,
            ],
        ];

        foreach ($blogPosts as $post) {
            Blog::firstOrCreate(
                ['title' => $post['title']],
                $post
            );
        }

        // Update Municipal Information with Announcements
        $municipalInfo = MunicipalInformation::first();
        if ($municipalInfo) {
            if (empty($municipalInfo->announcements)) {
                $municipalInfo->update([
                    'announcements' => '<h3>Important Announcements</h3>
                    <ul>
                        <li><strong>Office Hours:</strong> Monday to Friday, 8:00 AM to 5:00 PM</li>
                        <li><strong>Online Services:</strong> Available 24/7 through our Click2Serve platform</li>
                        <li><strong>Emergency Hotline:</strong> (075) 201-2345</li>
                        <li><strong>Email:</strong> cso.alaminos@yahoo.com</li>
                    </ul>
                    
                    <h3>Upcoming Events</h3>
                    <ul>
                        <li>City Council Meeting - Every first Monday of the month</li>
                        <li>Community Health Day - Last Saturday of each month</li>
                        <li>Tourism Festival - Annual celebration in May</li>
                    </ul>
                    
                    <h3>Service Reminders</h3>
                    <ul>
                        <li>Business permit renewal deadline: March 31</li>
                        <li>Real property tax payment: Quarterly</li>
                        <li>Civil registry services: Available daily</li>
                    </ul>
                    
                    <p><em>Stay updated with the latest news and announcements through our website and official social media pages.</em></p>'
                ]);
            }
        }
    }
}








