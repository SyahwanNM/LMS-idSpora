<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Web Development Workshop',
                'speaker' => 'John Doe',
                'description' => 'Learn the latest web development technologies and best practices in this comprehensive workshop.',
                'location' => 'Online',
                'price' => 150000,
                'discount_percentage' => 20,
                'event_date' => Carbon::now()->addDays(7),
                'event_time' => '09:00:00',
                'image' => null, // Will use placeholder
            ],
            [
                'title' => 'Mobile App Development Seminar',
                'speaker' => 'Jane Smith',
                'description' => 'Discover the secrets of building successful mobile applications for iOS and Android platforms.',
                'location' => 'Jakarta Convention Center',
                'price' => 200000,
                'discount_percentage' => 15,
                'event_date' => Carbon::now()->addDays(14),
                'event_time' => '10:00:00',
                'image' => null, // Will use placeholder
            ],
            [
                'title' => 'Data Science Conference',
                'speaker' => 'Dr. Michael Johnson',
                'description' => 'Explore the latest trends in data science, machine learning, and artificial intelligence.',
                'location' => 'Bandung Tech Hub',
                'price' => 300000,
                'discount_percentage' => 0,
                'event_date' => Carbon::now()->addDays(21),
                'event_time' => '08:30:00',
                'image' => null, // Will use placeholder
            ],
            [
                'title' => 'UI/UX Design Masterclass',
                'speaker' => 'Sarah Wilson',
                'description' => 'Master the art of user interface and user experience design with industry experts.',
                'location' => 'Surabaya Creative Center',
                'price' => 180000,
                'discount_percentage' => 25,
                'event_date' => Carbon::now()->addDays(28),
                'event_time' => '14:00:00',
                'image' => null, // Will use placeholder
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }

        $this->command->info('Sample events created successfully!');
    }
}