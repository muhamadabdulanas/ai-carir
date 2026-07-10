<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        \App\Models\User::create([
            'name' => 'Admin AI-Carir',
            'email' => 'admin@aicarir.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // 2. Create Student
        $student = \App\Models\User::create([
            'name' => 'Budi Santoso',
            'email' => 'mahasiswa@aicarir.com',
            'password' => bcrypt('mahasiswa123'),
            'role' => 'student',
        ]);

        // 3. Create initial CV Analysis for the student so they have data out of the box
        \App\Models\CvAnalysis::create([
            'user_id' => $student->id,
            'cv_path' => null,
            'raw_text' => 'Nama: Budi Santoso. Email: budi@gmail.com. Mahasiswa Informatika semester 6. Memiliki keahlian dalam pemrograman web khususnya menggunakan PHP dan framework Laravel. Menguasai database MySQL dan PostgreSQL. Mengerti dasar-dasar HTML, CSS, JavaScript, serta Version Control Git. Aktif dalam organisasi kemahasiswaan dan memiliki kemampuan komunikasi yang baik.',
            'skills' => [
                'hard' => ['PHP', 'Laravel', 'MySQL', 'PostgreSQL', 'HTML', 'CSS', 'JavaScript', 'Git'],
                'soft' => ['Komunikasi', 'Kerja Sama Tim', 'Problem Solving', 'Adaptabilitas']
            ],
            'careers' => [
                [
                    'title' => 'Laravel Backend Developer',
                    'match_rate' => 95,
                    'reason' => 'Anda memiliki pemahaman kuat tentang PHP dan framework Laravel, didukung oleh keahlian database relasional (MySQL/PostgreSQL) yang sangat krusial untuk backend developer.'
                ],
                [
                    'title' => 'Fullstack Web Developer',
                    'match_rate' => 80,
                    'reason' => 'Dengan kemampuan dasar HTML, CSS, JavaScript untuk frontend dan keahlian backend (PHP/Laravel), Anda memiliki fondasi yang baik untuk berkembang sebagai Fullstack Developer.'
                ],
                [
                    'title' => 'Database Administrator',
                    'match_rate' => 70,
                    'reason' => 'Kombinasi pemahaman MySQL dan PostgreSQL memberikan dasar yang baik untuk mengelola, mengoptimalkan, dan mengamankan database relasional.'
                ]
            ],
            'improvements' => [
                'Pelajari RESTful API Development dan konsep autentikasi JWT/Sanctum di Laravel.',
                'Tingkatkan keahlian frontend dengan mempelajari framework modern seperti Vue.js atau React.',
                'Pelajari konsep containerization dengan Docker untuk mempermudah deployment.'
            ]
        ]);

        // 4. Create Internship Listings
        \App\Models\Internship::create([
            'title' => 'Laravel Backend Developer Intern',
            'company' => 'PT Techno Inovasi',
            'location' => 'Jakarta (Hybrid)',
            'description' => 'Membantu tim engineering dalam mengembangkan API dan fitur backend menggunakan PHP Laravel. Menulis query database yang efisien serta berkolaborasi menggunakan Git.',
            'required_skills' => ['PHP', 'Laravel', 'MySQL', 'Git', 'REST API']
        ]);

        \App\Models\Internship::create([
            'title' => 'Frontend Web Developer Intern',
            'company' => 'PT GoDigital Indonesia',
            'location' => 'Bandung (On-site)',
            'description' => 'Membangun antarmuka web yang responsif dan dinamis menggunakan HTML, CSS, JavaScript, dan framework Vue/React. Bekerja sama dengan UI/UX Designer.',
            'required_skills' => ['HTML', 'CSS', 'JavaScript', 'React', 'Vue', 'Responsive Design']
        ]);

        \App\Models\Internship::create([
            'title' => 'UI/UX Designer Intern',
            'company' => 'PT Creative Studio',
            'location' => 'Yogyakarta (Remote)',
            'description' => 'Merancang wireframe, mockup, dan prototipe interaktif menggunakan Figma. Melakukan user research dan merancang user flow yang ramah pengguna.',
            'required_skills' => ['Figma', 'Wireframing', 'Prototyping', 'User Research', 'Design System']
        ]);

        \App\Models\Internship::create([
            'title' => 'Data Analyst Intern',
            'company' => 'PT Analitika Utama',
            'location' => 'Jakarta (On-site)',
            'description' => 'Membantu membersihkan data, melakukan analisis statistik dasar, serta membuat visualisasi dashboard interaktif menggunakan Tableau dan SQL.',
            'required_skills' => ['SQL', 'Python', 'Tableau', 'Excel', 'Data Cleaning']
        ]);

        \App\Models\Internship::create([
            'title' => 'Mobile Developer Intern (Flutter)',
            'company' => 'PT Startup Maju Jaya',
            'location' => 'Surabaya (Hybrid)',
            'description' => 'Mengembangkan dan memelihara aplikasi mobile multiplatform (Android/iOS) menggunakan Flutter. Mengintegrasikan API backend.',
            'required_skills' => ['Flutter', 'Dart', 'Git', 'REST API', 'State Management']
        ]);

        \App\Models\Internship::create([
            'title' => 'Digital Marketing Intern',
            'company' => 'PT Sukses Bersama',
            'location' => 'Jakarta (Hybrid)',
            'description' => 'Membantu merencanakan dan menjalankan campaign digital di media sosial, melakukan optimasi SEO dasar, serta memantau analitik performa web.',
            'required_skills' => ['SEO', 'Copywriting', 'Social Media', 'Google Analytics', 'Content Planning']
        ]);
    }
}
