<?php

namespace App\Http\Controllers;

use App\Models\CvAnalysis;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $latestAnalysis = $user->latestCvAnalysis;
        
        $internships = collect();
        if ($latestAnalysis) {
            $studentSkills = array_merge(
                $latestAnalysis->skills['hard'] ?? [],
                $latestAnalysis->skills['soft'] ?? []
            );
            $studentSkillsLower = array_map('strtolower', $studentSkills);

            $allInternships = Internship::all();

            $internships = $allInternships->map(function ($internship) use ($studentSkillsLower) {
                $required = $internship->required_skills ?? [];
                if (empty($required)) {
                    $internship->match_score = 100;
                    $internship->matched_skills = [];
                    return $internship;
                }

                $matched = [];
                foreach ($required as $reqSkill) {
                    if (in_array(strtolower($reqSkill), $studentSkillsLower)) {
                        $matched[] = $reqSkill;
                    }
                }

                $score = (count($matched) / count($required)) * 100;
                $internship->match_score = round($score);
                $internship->matched_skills = $matched;
                return $internship;
            })->sortByDesc('match_score')->values();
        }

        return view('student.dashboard', compact('latestAnalysis', 'internships'));
    }

    public function uploadCv(Request $request)
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf,png,jpg,jpeg,txt|max:4096',
        ], [
            'cv_file.required' => 'Silakan pilih file CV Anda.',
            'cv_file.mimes' => 'Format file CV harus berupa PDF, PNG, JPG, JPEG, atau TXT.',
            'cv_file.max' => 'Ukuran file CV maksimal 4MB.',
        ]);

        $file = $request->file('cv_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('cv_uploads', $fileName, 'public');

        $extractedText = '';
        $mimeType = $file->getMimeType();
        $filePath = storage_path('app/public/' . $path);

        try {
            // Attempt local text extraction for raw text storage if PDF/TXT
            if ($file->getClientOriginalExtension() === 'txt') {
                $extractedText = file_get_contents($file->getRealPath());
            } elseif ($file->getClientOriginalExtension() === 'pdf') {
                if (class_exists(\Smalot\PdfParser\Parser::class)) {
                    try {
                        $pdfParser = new PdfParser();
                        $pdf = $pdfParser->parseFile($filePath);
                        $extractedText = $pdf->getText();
                    } catch (\Exception $e) {
                        Log::warning('Local text extraction failed: ' . $e->getMessage());
                    }
                }
            }

            // Perform AI analysis supporting multimodal input (PDF/image layouts from Canva)
            $analysisResult = $this->analyzeCvFile($filePath, $mimeType, $extractedText);

            // Populate extractedText placeholder if it was a scanned PDF or image
            if (empty(trim($extractedText))) {
                $careersList = array_column($analysisResult['careers'] ?? [], 'title');
                $skillsList = $analysisResult['skills']['hard'] ?? [];
                $extractedText = "Dokumen Gambar/Desain Canva (" . strtoupper($file->getClientOriginalExtension()) . ").\n" .
                                 "Hasil Deteksi AI:\n" .
                                 "- Rekomendasi Karir: " . implode(', ', $careersList) . ".\n" .
                                 "- Kunci Keahlian Teknis: " . implode(', ', $skillsList) . ".";
            }

            // Save to database
            CvAnalysis::create([
                'user_id' => Auth::id(),
                'cv_path' => $path,
                'skills' => $analysisResult['skills'] ?? ['hard' => [], 'soft' => []],
                'careers' => $analysisResult['careers'] ?? [],
                'improvements' => $analysisResult['improvements'] ?? [],
                'raw_text' => $extractedText,
            ]);

            return redirect()->route('student.dashboard')->with('success', 'CV berhasil diunggah dan dianalisis otomatis oleh AI (Mendukung Teks & Gambar Canva)!');

        } catch (\Exception $e) {
            Log::error('Error processing CV: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses CV: ' . $e->getMessage());
        }
    }

    public function printReport()
    {
        $user = Auth::user();
        $latestAnalysis = $user->latestCvAnalysis;

        if (!$latestAnalysis) {
            return redirect()->route('student.dashboard')->with('error', 'Anda belum mengunggah CV untuk dianalisis.');
        }

        return view('student.print', compact('latestAnalysis', 'user'));
    }

    private function analyzeCvFile(string $filePath, string $mimeType, string $localText): array
    {
        $apiKey = env('GEMINI_API_KEY');

        if (!empty($apiKey)) {
            try {
                $base64Data = base64_encode(file_get_contents($filePath));
                
                $prompt = "Anda adalah AI konsultan karir profesional. Analisislah file CV/Resume terlampir.\n\n" .
                          "Tugas Anda:\n" .
                          "1. Lakukan OCR jika file ini berupa gambar atau scan (seperti desain Canva/gambar/scan PDF).\n" .
                          "2. Ekstrak daftar keahlian teknis (hard skills) dan keahlian interpersonal (soft skills).\n" .
                          "3. Rekomendasikan 3 karir yang paling cocok beserta persentase kecocokan (match rate 0-100%) dan alasan detail mengapa karir tersebut cocok.\n" .
                          "4. Berikan 3 saran konkret untuk meningkatkan skill atau memperluas portofolio demi mendukung karir tersebut.\n\n" .
                          "Format output wajib berupa JSON yang valid dengan struktur berikut:\n" .
                          "{\n" .
                          "  \"skills\": {\n" .
                          "    \"hard\": [\"skill1\", \"skill2\", ...],\n" .
                          "    \"soft\": [\"skill1\", \"skill2\", ...]\n" .
                          "  },\n" .
                          "  \"careers\": [\n" .
                          "    {\n" .
                          "      \"title\": \"Nama Karir\",\n" .
                          "      \"match_rate\": 85,\n" .
                          "      \"reason\": \"Alasan...\"\n" .
                          "    }\n" .
                          "  ],\n" .
                          "  \"improvements\": [\"saran1\", \"saran2\", ...]\n" .
                          "}\n\n" .
                          "Kembalikan HANYA format JSON di atas, tanpa blok markdown (seperti ```json) atau penjelasan tambahan.";

                // Construct parts for Gemini API
                $parts = [
                    ['text' => $prompt]
                ];

                // If it is an image or PDF, we attach the inlineData
                if (in_array($mimeType, ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'])) {
                    $parts[] = [
                        'inlineData' => [
                            'mimeType' => $mimeType,
                            'data' => $base64Data
                        ]
                    ];
                } else {
                    $parts[] = [
                        'text' => "Teks CV:\n" . $localText
                    ];
                }

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => $parts
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $resJson = $response->json();
                    $responseText = $resJson['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    
                    // Clean up markdown wrappers
                    $responseText = trim($responseText);
                    if (str_starts_with($responseText, '```json')) {
                        $responseText = substr($responseText, 7);
                    }
                    if (str_ends_with($responseText, '```')) {
                        $responseText = substr($responseText, 0, -3);
                    }
                    $responseText = trim($responseText);

                    // Robust Regex extraction of the JSON block
                    if (preg_match('/\{.*\}/s', $responseText, $matches)) {
                        $jsonString = $matches[0];
                        $decoded = json_decode($jsonString, true);
                        if (json_last_error() === JSON_ERROR_NONE && !empty($decoded['skills'])) {
                            return $decoded;
                        }
                    }
                    
                    Log::warning('Gemini Multimodal API responded with text that could not be parsed as JSON: ' . $responseText);
                } else {
                    Log::warning('Gemini API request failed. Status: ' . $response->status() . '. Body: ' . $response->body());
                }
                
                Log::warning('Gemini Multimodal API call completed but failed to parse. Falling back to local parser.');
            } catch (\Exception $e) {
                Log::error('Gemini Multimodal API error: ' . $e->getMessage() . '. Falling back to local parser.');
            }
        }

        // Fallback: Smart local keyword parser
        return $this->fallbackLocalParser($localText);
    }

    private function fallbackLocalParser(string $text): array
    {
        $textLower = strtolower($text);
        
        // Define dictionary of hard skills
        $hardDict = [
            'php' => 'PHP', 'laravel' => 'Laravel', 'symfony' => 'Symfony', 'codeigniter' => 'CodeIgniter',
            'javascript' => 'JavaScript', 'js' => 'JavaScript', 'typescript' => 'TypeScript', 'ts' => 'TypeScript',
            'react' => 'React.js', 'vue' => 'Vue.js', 'angular' => 'Angular', 'nextjs' => 'Next.js',
            'html' => 'HTML', 'css' => 'CSS', 'sass' => 'Sass', 'tailwind' => 'Tailwind CSS', 'bootstrap' => 'Bootstrap',
            'python' => 'Python', 'django' => 'Django', 'flask' => 'Flask',
            'java' => 'Java', 'spring' => 'Spring Boot', 'kotlin' => 'Kotlin',
            'dart' => 'Dart', 'flutter' => 'Flutter', 'react native' => 'React Native',
            'swift' => 'Swift', 'objective-c' => 'Objective-C',
            'c++' => 'C++', 'c#' => 'C#', 'asp.net' => 'ASP.NET',
            'sql' => 'SQL', 'mysql' => 'MySQL', 'postgresql' => 'PostgreSQL', 'mongodb' => 'MongoDB', 'sqlite' => 'SQLite',
            'git' => 'Git', 'github' => 'GitHub', 'gitlab' => 'GitLab', 'docker' => 'Docker', 'kubernetes' => 'Kubernetes',
            'aws' => 'AWS', 'gcp' => 'Google Cloud', 'azure' => 'Azure',
            'figma' => 'Figma', 'adobe xd' => 'Adobe XD', 'photoshop' => 'Photoshop', 'illustrator' => 'Illustrator',
            'seo' => 'SEO', 'sem' => 'SEM', 'copywriting' => 'Copywriting', 'digital marketing' => 'Digital Marketing',
            'excel' => 'Microsoft Excel', 'tableau' => 'Tableau', 'power bi' => 'Power BI', 'data science' => 'Data Science'
        ];

        // Define dictionary of soft skills
        $softDict = [
            'komunikasi' => 'Komunikasi', 'communication' => 'Komunikasi',
            'kerja sama' => 'Kerja Sama Tim', 'teamwork' => 'Kerja Sama Tim', 'kolaborasi' => 'Kerja Sama Tim',
            'problem solving' => 'Problem Solving', 'pemecahan masalah' => 'Problem Solving',
            'kepemimpinan' => 'Kepemimpinan', 'leadership' => 'Kepemimpinan',
            'kreatif' => 'Kreativitas', 'creative' => 'Kreativitas', 'kreativitas' => 'Kreativitas',
            'adaptasi' => 'Adaptabilitas', 'adaptability' => 'Adaptabilitas',
            'manajemen waktu' => 'Manajemen Waktu', 'time management' => 'Manajemen Waktu',
            'negosiasi' => 'Negosiasi', 'negotiation' => 'Negosiasi',
            'analitis' => 'Berpikir Analitis', 'analytical' => 'Berpikir Analitis', 'kritis' => 'Berpikir Analitis'
        ];

        // Match skills
        $foundHard = [];
        foreach ($hardDict as $key => $name) {
            if (str_contains($textLower, $key)) {
                $foundHard[] = $name;
            }
        }
        $foundHard = array_unique($foundHard);

        $foundSoft = [];
        foreach ($softDict as $key => $name) {
            if (str_contains($textLower, $key)) {
                $foundSoft[] = $name;
            }
        }
        $foundSoft = array_unique($foundSoft);

        // Fallbacks if nothing detected
        if (empty($foundHard)) {
            $foundHard = ['Web Basics', 'HTML', 'CSS', 'JavaScript'];
        }
        if (empty($foundSoft)) {
            $foundSoft = ['Komunikasi', 'Kerja Sama Tim', 'Problem Solving'];
        }

        // Determine careers based on skills
        $careers = [];
        $improvements = [];

        // Convert found hard skills to lower for easy comparison
        $foundHardLower = array_map('strtolower', $foundHard);

        if (in_array('laravel', $foundHardLower) || in_array('php', $foundHardLower)) {
            $careers[] = [
                'title' => 'Laravel Backend Developer',
                'match_rate' => 90,
                'reason' => 'Berdasarkan keahlian PHP/Laravel di CV Anda, Anda sangat cocok untuk membangun dan mengelola logika aplikasi server serta RESTful API.'
            ];
            $improvements[] = 'Tingkatkan keahlian backend dengan mempelajari Redis Caching dan optimasi query database.';
        }

        if (in_array('react.js', $foundHardLower) || in_array('vue.js', $foundHardLower) || in_array('html', $foundHardLower)) {
            $careers[] = [
                'title' => 'Frontend Web Developer',
                'match_rate' => 85,
                'reason' => 'Keahlian dalam HTML/CSS/JavaScript serta library modern membuat Anda siap untuk membangun antarmuka web yang responsif.'
            ];
            $improvements[] = 'Pelajari Framework CSS modern seperti Tailwind CSS jika belum menguasainya.';
        }

        if (in_array('figma', $foundHardLower) || in_array('adobe xd', $foundHardLower)) {
            $careers[] = [
                'title' => 'UI/UX Designer',
                'match_rate' => 92,
                'reason' => 'Pengalaman menggunakan Figma menunjukkan minat besar dalam perancangan antarmuka pengguna dan riset pengalaman pengguna.'
            ];
            $improvements[] = 'Bangun portfolio desain UI/UX di Behance atau Dribbble secara berkala.';
        }

        if (in_array('flutter', $foundHardLower) || in_array('dart', $foundHardLower)) {
            $careers[] = [
                'title' => 'Mobile Developer (Flutter)',
                'match_rate' => 88,
                'reason' => 'Kemampuan pemrograman Flutter dan Dart membuat Anda dapat membuat aplikasi mobile Android & iOS dengan basis kode tunggal.'
            ];
            $improvements[] = 'Pelajari manajemen state modern di Flutter seperti Bloc atau Provider.';
        }

        if (in_array('python', $foundHardLower) || in_array('tableau', $foundHardLower) || in_array('sql', $foundHardLower)) {
            $careers[] = [
                'title' => 'Data Analyst',
                'match_rate' => 85,
                'reason' => 'Kombinasi analisis data Python, SQL, dan visualisasi data sangat cocok untuk membantu bisnis mengambil keputusan berdasarkan data.'
            ];
            $improvements[] = 'Pelajari library Python khusus data seperti Pandas, NumPy, dan Seaborn.';
        }

        if (in_array('seo', $foundHardLower) || in_array('digital marketing', $foundHardLower) || in_array('copywriting', $foundHardLower)) {
            $careers[] = [
                'title' => 'Digital Marketer & SEO Specialist',
                'match_rate' => 85,
                'reason' => 'Keahlian pemasaran digital dan optimasi mesin pencari sangat relevan untuk meningkatkan eksposur online perusahaan.'
            ];
            $improvements[] = 'Pelajari Google Analytics dan Google Search Console untuk melacak metrik lalu lintas web.';
        }

        // Fill up careers if less than 3
        if (count($careers) < 3) {
            $allPossible = [
                [
                    'title' => 'Fullstack Web Developer',
                    'match_rate' => 75,
                    'reason' => 'Dengan mengembangkan skill frontend dan backend Anda, Anda bisa menjadi Fullstack Developer yang independen.'
                ],
                [
                    'title' => 'Quality Assurance (QA) Engineer',
                    'match_rate' => 70,
                    'reason' => 'Kemampuan analitis dan dasar pemrograman Anda membantu dalam melakukan pengujian manual dan otomatis pada perangkat lunak.'
                ],
                [
                    'title' => 'Technical Support Specialist',
                    'match_rate' => 65,
                    'reason' => 'Keahlian komunikasi yang baik dipadukan dengan pemahaman dasar IT/Teknologi membuat Anda ideal sebagai penghubung teknis.'
                ]
            ];
            foreach ($allPossible as $p) {
                if (count($careers) >= 3) break;
                // Avoid duplicates
                $exists = false;
                foreach ($careers as $c) {
                    if ($c['title'] === $p['title']) $exists = true;
                }
                if (!$exists) {
                    $careers[] = $p;
                }
            }
        }

        // Fill up improvements if less than 3
        if (count($improvements) < 3) {
            $defaultImps = [
                'Pelajari Version Control Git dan biasakan melakukan commit di GitHub secara rutin.',
                'Ikuti kursus online bersertifikasi untuk memperdalam salah satu skill spesifik.',
                'Buat proyek studi kasus nyata untuk dimasukkan ke dalam portofolio Anda.'
            ];
            foreach ($defaultImps as $imp) {
                if (count($improvements) >= 3) break;
                if (!in_array($imp, $improvements)) {
                    $improvements[] = $imp;
                }
            }
        }

        return [
            'skills' => [
                'hard' => array_values($foundHard),
                'soft' => array_values($foundSoft),
            ],
            'careers' => array_slice($careers, 0, 3),
            'improvements' => array_slice($improvements, 0, 3)
        ];
    }
}
