<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyPhoto;
use Carbon\Carbon;

class ProfessionalPropertiesSeeder extends Seeder
{
    public function run()
    {
        // CAUTION: Truncate properties for demo freshness
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Property::truncate();
        PropertyPhoto::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ensure storage directory exists
        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('properties/demo');

        // 1. Create a specific Demo Landlord user
        $landlord = User::firstOrCreate(
            ['email' => 'landlord@dwello.com'],
            [
                'name' => 'Dwello Estates',
                'password' => bcrypt('password'), // default password
                'role' => 'landlord',
                'email_verified_at' => now(),
            ]
        );

        // 2. Define Budget-Friendly Properties (5,000 - 100,000)
        $properties = [
            [
                'title' => 'Affordable City Apartment',
                'description' => "Great value 2-bedroom apartment in a convenient location. \n\nFeatures:\n- Close to public transport\n- Secure building\n- Basic furnishings included\n- Ideal for small families or working professionals\n\nWalking distance to markets and shops.",
                'city' => 'Colombo',
                'address' => 'Vauxhall Street, Colombo 02',
                'monthly_rent' => 65000,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'property_type' => 'apartment',
                'available_from' => Carbon::now()->addDays(5),
                'latitude' => 6.9271,
                'longitude' => 79.8471,
                'photos' => [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200&q=80',
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200&q=80',
                ]
            ],
            [
                'title' => 'Cozy Family House',
                'description' => "Charming single-story house in a quiet neighborhood. \n\nIncludes:\n- Small private garden\n- 2 Spacious bedrooms\n- Kitchen with pantry\n- Parking for 1 vehicle\n\nPerfect for a peaceful lifestyle reasonably close to the city.",
                'city' => 'Nugegoda',
                'address' => 'Chapel Road, Nugegoda',
                'monthly_rent' => 45000,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'property_type' => 'house',
                'available_from' => Carbon::now(),
                'latitude' => 6.8742,
                'longitude' => 79.8906,
                'photos' => [
                    'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=1200&q=80',
                    'https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?w=1200&q=80',
                ]
            ],
            [
                'title' => 'Student Room near Campus',
                'description' => "Single room available for male student. \n\nFeatures:\n- Walking distance to SLIIT/CINEC\n- Desk and chair provided\n- Shared bathroom\n- All bills included in rent\n\nBudget friendly choice for focused students.",
                'city' => 'Malabe',
                'address' => 'New Kandy Road, Malabe',
                'monthly_rent' => 15000,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'property_type' => 'room',
                'available_from' => Carbon::now()->addWeek(),
                'latitude' => 6.9041,
                'longitude' => 79.9547,
                'photos' => [
                    'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=1200&q=80',
                    'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=1200&q=80',
                ]
            ],
            [
                'title' => 'Compact Annex for Rent',
                'description' => "Separate entrance annex suitable for a couple or single person. \n\nFeatures:\n- 1 Bedroom with attached bath\n- Small kitchenette area\n- Key money 3 months\n- Quiet residential area\n\nVery affordable.",
                'city' => 'Battaramulla',
                'address' => 'Koswatta, Battaramulla',
                'monthly_rent' => 25000,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'property_type' => 'room',
                'available_from' => Carbon::now()->addMonth(),
                'latitude' => 6.8906,
                'longitude' => 79.9238,
                'photos' => [
                    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=1200&q=80',
                    'https://images.unsplash.com/photo-1522771753035-7a5887592a84?w=1200&q=80',
                ]
            ],
            [
                'title' => 'Modern Apartment Dehiwala',
                'description' => "Newly painted 2 bedroom apartment on the 3rd floor. \n\n- Sea breeze and good ventilation\n- Tiled floors\n- Close to Galle Road\n- Secure parking\n\nGreat balance of comfort and price.",
                'city' => 'Dehiwala',
                'address' => 'Hill Street, Dehiwala',
                'monthly_rent' => 55000,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'property_type' => 'apartment',
                'available_from' => Carbon::now()->addDays(2),
                'latitude' => 6.8511,
                'longitude' => 79.8659,
                'photos' => [
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=1200&q=80',
                    'https://images.unsplash.com/photo-1502005229762-cf1b2da7c5d6?w=1200&q=80',
                ]
            ],
            [
                'title' => 'Shared Room for Working Girls',
                'description' => "Bed space available in a large room shared with 2 others. \n\n- Safe environment\n- Cooking facilities available\n- Close to garment factories\n- Water and electricity shared\n\nSuper budget option.",
                'city' => 'Ratmalana',
                'address' => 'Borupana Road, Ratmalana',
                'monthly_rent' => 8000,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'property_type' => 'room',
                'available_from' => Carbon::now(),
                'latitude' => 6.8195,
                'longitude' => 79.8837,
                'photos' => [
                    'https://images.unsplash.com/photo-1522771753035-7a5887592a84?w=1200&q=80',
                ]
            ],
            [
                'title' => 'Spacious House in Kottawa',
                'description' => "3 Bedroom house with large hall and dining area. \n\n- 15 mins to Highway entrance\n- Well water and tap line\n- Pet friendly\n- Fenced land\n\nIdeal for a large family on a budget.",
                'city' => 'Kottawa',
                'address' => 'High Level Road, Kottawa',
                'monthly_rent' => 35000,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'property_type' => 'house',
                'available_from' => Carbon::now()->addDays(10),
                'latitude' => 6.8412,
                'longitude' => 79.9654,
                'photos' => [
                    'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=1200&q=80',
                    'https://images.unsplash.com/photo-1600596542815-27a9476cac9b?w=1200&q=80',
                ]
            ],
            [
                'title' => 'Luxury Room with AC',
                'description' => "Private room with AC and attached bathroom in a luxury house. \n\n- Furnished with queen bed and wardrobe\n- Hot water\n- Parking available\n- Meals can be arranged\n\nPremium comfort for a single executive.",
                'city' => 'Colombo',
                'address' => 'Thimbirigasyaya, Colombo 05',
                'monthly_rent' => 40000,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'property_type' => 'room',
                'available_from' => Carbon::now(),
                'latitude' => 6.8969,
                'longitude' => 79.8686,
                'photos' => [
                    'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?w=1200&q=80',
                    'https://images.unsplash.com/photo-1505693416388-b0346efee958?w=1200&q=80',
                ]
            ],
            [
                'title' => 'Furnished Flat in Wellawatte',
                'description' => "2 Bedroom flat available for long term rent. \n\n- Marine Drive view\n- Fully tiled\n- 2nd Floor (No Lift)\n- 5 mins to market/railway station\n\nReasonable rent for the area.",
                'city' => 'Wellawatte',
                'address' => 'Frances Road, Colombo 06',
                'monthly_rent' => 85000,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'property_type' => 'apartment',
                'available_from' => Carbon::now()->addDays(5),
                'latitude' => 6.8744,
                'longitude' => 79.8601,
                'photos' => [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200&q=80',
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=1200&q=80',
                ]
            ],
        ];

        $console = new \Symfony\Component\Console\Output\ConsoleOutput();

        foreach ($properties as $propData) {
            $photos = $propData['photos'];
            unset($propData['photos']);

            $property = Property::create(array_merge($propData, [
                'user_id' => $landlord->id,
            ]));

            foreach ($photos as $index => $photoUrl) {
                try {
                    $console->writeln("Downloading photo " . ($index + 1) . " for: " . $property->title);
                    
                    // Generate unique filename
                    $filename = 'demo_' . $property->id . '_' . $index . '_' . md5($photoUrl) . '.jpg';
                    $path = 'properties/demo/' . $filename;

                    // Download content
                    $contents = @file_get_contents($photoUrl);
                    
                    if ($contents) {
                        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $contents);
                        
                        PropertyPhoto::create([
                            'property_id' => $property->id,
                            'path' => $path, 
                        ]);
                    } else {
                        $console->writeln("Failed to download image from: $photoUrl");
                    }
                } catch (\Exception $e) {
                    $console->writeln("Error saving image: " . $e->getMessage());
                }
            }
        }
    }
}
