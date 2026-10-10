<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MunicipalInformation;
use App\Models\CityOfficial;
use App\Models\TouristSpot;
use App\Models\Announcement;

class MunicipalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds municipal information, city officials, tourist spots, and announcements.
     */
    public function run(): void
    {
        // === MUNICIPAL INFORMATION ===
        MunicipalInformation::updateOrCreate(
            ['id' => 1],
            [
                'city_name' => 'Alaminos City',
                'tagline' => 'Gateway to the Hundred Islands',
                'mayor_name' => 'HON. ARTURIO M. CELESTE',
                'mayor_title' => 'City Mayor',
                'mayor_image' => 'assets/images/municipal/mayor.jpg',
                'banner_image' => 'assets/images/municipal/banner.jpg',
                'city_seal' => 'assets/images/municipal/seal.png',
                'iso_certification' => 'ISO 9001:2015 Certified',
                'email' => 'cso.alaminos@yahoo.com',
                'phone' => '(075) 201-2345',
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
                        <li>Bangus Festival - Annual celebration in May</li>
                    </ul>
                    
                    <h3>Service Reminders</h3>
                    <ul>
                        <li>Business permit renewal deadline: March 31</li>
                        <li>Real property tax payment: Quarterly</li>
                        <li>Civil registry services: Available daily</li>
                    </ul>',
                'announcements_image' => null,
            ]
        );

        // === CITY OFFICIALS ===
        $officials = [
            [
                'name' => 'JAN MARIONNE R. PONTELERA',
                'position' => 'CITY VICE MAYOR',
                'image' => 'assets/images/officials/vice-mayor.jpg',
                'department' => null,
                'display_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'MARILOU R. SISON',
                'position' => 'COUNCILOR',
                'image' => 'assets/images/officials/councilor1.jpg',
                'department' => null,
                'display_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'CARMEN A. RUIZ',
                'position' => 'COUNCILOR',
                'image' => 'assets/images/officials/councilor2.jpg',
                'department' => null,
                'display_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'ROBERTO D. CRUZ',
                'position' => 'COUNCILOR',
                'image' => 'assets/images/officials/councilor3.jpg',
                'department' => null,
                'display_order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'ELENA M. FERNANDEZ',
                'position' => 'COUNCILOR',
                'image' => 'assets/images/officials/councilor4.jpg',
                'department' => null,
                'display_order' => 5,
                'is_active' => true
            ],
        ];

        foreach ($officials as $official) {
            CityOfficial::updateOrCreate(
                ['name' => $official['name']],
                $official
            );
        }

        // === TOURIST SPOTS ===
        $touristSpots = [
            [
                'name' => 'Hundred Islands National Park',
                'description' => 'Hundred Islands National Park, established in 1940, is the Philippines\' first national park and comprises 124 islands (123 at high tide) scattered across the Lingayen Gulf. Known for its crystal-clear waters, white sandy beaches, and diverse marine life, the park offers activities such as island hopping, snorkeling, and kayaking. Only a few islands (Governor Island, Quezon Island, and Children\'s Island) are developed for tourism, with the rest remaining untouched to preserve their natural beauty.',
                'image' => 'assets/images/tourist-spots/hundred-islands.jpg',
                'display_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Lucap Wharf',
                'description' => 'Lucap Wharf in Alaminos City, Pangasinan, serves as the primary gateway to the Hundred Islands National Park. This bustling port functions as both a fishing and passenger port, offering essential amenities for travelers including a tourism center, restaurants, souvenir shops, and a recreation area. The wharf facilitates boat tours to the park\'s 124 islands, making it the ideal starting point for exploring this natural wonder. Its scenic location features a lighthouse and picturesque sea views, providing visitors with a memorable introduction to the region\'s natural beauty.',
                'image' => 'assets/images/tourist-spots/lucap-wharf.jpg',
                'display_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Quezon Island',
                'description' => 'Quezon Island is one of the three main developed islands in the Hundred Islands National Park. It features pristine white sand beaches, crystal-clear waters, and excellent facilities for day-trippers including cottages, restaurants, and restrooms. The island is perfect for swimming, sunbathing, and snorkeling. Visitors can also enjoy the famous zipline that offers breathtaking aerial views of the surrounding islands.',
                'image' => 'assets/images/tourist-spots/quezon-island.jpg',
                'display_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Governor Island',
                'description' => 'Governor Island is the most developed island in the Hundred Islands National Park. It offers panoramic views from its viewing deck, which can be reached via a series of steps. The island features cottages for rent, restaurants, and diving platforms. It\'s an ideal destination for those who want to experience island life while enjoying modern amenities.',
                'image' => 'assets/images/tourist-spots/governor-island.jpg',
                'display_order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Children\'s Island',
                'description' => 'Children\'s Island is a favorite among families visiting the Hundred Islands. With its calm, shallow waters and fine white sand, it\'s perfect for children and non-swimmers. The island offers a safe environment for beach activities and is equipped with basic facilities. It\'s the ideal spot for a relaxing family day out.',
                'image' => 'assets/images/tourist-spots/childrens-island.jpg',
                'display_order' => 5,
                'is_active' => true
            ],
        ];

        foreach ($touristSpots as $spot) {
            TouristSpot::updateOrCreate(
                ['name' => $spot['name']],
                $spot
            );
        }

        // === ANNOUNCEMENTS ===
        $announcements = [
            [
                'title' => 'Click2Serve Online Platform Now Available',
                'content' => 'We are pleased to announce that our Click2Serve online platform is now available for all residents. Access government services conveniently from your home or office. Track your requests in real-time and receive notifications on your mobile device.',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Business Permit Renewal Reminder',
                'content' => 'All business establishments are reminded to renew their permits before March 31 to avoid penalties. Early bird discounts are available for those who renew before January 31. Visit the BPLO or use our Click2Serve platform for hassle-free renewal.',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Free Rabies Vaccination Schedule',
                'content' => 'The City Veterinary Office announces free rabies vaccination for all registered pets. Schedule: Every Saturday from 8 AM to 12 NN at the City Veterinary Office. Bring your pet registration form for verification.',
                'is_active' => true,
                'order' => 3,
            ],
        ];

        // Only seed if Announcement model exists
        if (class_exists(Announcement::class)) {
            foreach ($announcements as $announcement) {
                Announcement::updateOrCreate(
                    ['title' => $announcement['title']],
                    $announcement
                );
            }
        }

        $this->command->info('Municipal data seeded successfully!');
        $this->command->info('  - Municipal Information: 1 record');
        $this->command->info('  - City Officials: ' . count($officials) . ' records');
        $this->command->info('  - Tourist Spots: ' . count($touristSpots) . ' records');
        $this->command->info('  - Announcements: ' . count($announcements) . ' records');
    }
}
