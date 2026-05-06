<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

class DashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 5 Kursus Berbeda
        $courseData = [
            ['title' => 'Belajar Laravel Dasar', 'price' => 150000],
            ['title' => 'Mastering Vue.js 3', 'price' => 200000],
            ['title' => 'Ionic Mobile App', 'price' => 250000],
            ['title' => 'UI/UX Design for Web', 'price' => 100000],
            ['title' => 'Database MySQL Advanced', 'price' => 180000],
        ];

        $createdCourses = [];
        foreach ($courseData as $c) {
            $createdCourses[] = Course::firstOrCreate(
                ['title' => $c['title']],
                [
                    'description' => 'Kursus intensif ' . $c['title'],
                    'price' => $c['price'],
                    'image' => null
                ]
            );
        }

        // 2. Data Student Dummy
        $students = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com'],
            ['name' => 'Siti Aminah', 'email' => 'siti@example.com'],
            ['name' => 'Rian Ardianto', 'email' => 'rian@example.com'],
        ];

        foreach ($students as $data) {
            // Gunakan updateOrCreate agar tidak duplikat saat seeder dijalankan ulang
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'student'
                ]
            );

            // 3. Daftarkan SETIAP student ke SEMUA 5 kursus tadi
            foreach ($createdCourses as $course) {
                Enrollment::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'price_bought' => $course->price,
                        'status' => 'success'
                    ]
                );
            }
        }
    }
}