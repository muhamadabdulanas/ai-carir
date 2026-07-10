<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Internship;
use App\Models\CvAnalysis;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $studentsCount = User::where('role', 'student')->count();
        $internshipsCount = Internship::count();
        $cvAnalysesCount = CvAnalysis::count();

        // Load all students and their latest CV analyses
        $students = User::where('role', 'student')
            ->with('latestCvAnalysis')
            ->orderBy('created_at', 'desc')
            ->get();

        // Load all internships
        $internships = Internship::orderBy('created_at', 'desc')->get();

        // Calculate skill distribution for analytics dashboard
        $allAnalyses = CvAnalysis::all();
        $skillCounts = [];
        $careerCounts = [];

        foreach ($allAnalyses as $analysis) {
            $skills = $analysis->skills['hard'] ?? [];
            foreach ($skills as $skill) {
                $skill = trim(strtolower($skill));
                if (!empty($skill)) {
                    $skillCounts[$skill] = ($skillCounts[$skill] ?? 0) + 1;
                }
            }

            $careers = $analysis->careers ?? [];
            foreach ($careers as $career) {
                $title = trim($career['title'] ?? '');
                if (!empty($title)) {
                    $careerCounts[$title] = ($careerCounts[$title] ?? 0) + 1;
                }
            }
        }

        // Sort and get top skills & careers
        arsort($skillCounts);
        arsort($careerCounts);

        $topSkills = array_slice($skillCounts, 0, 5, true);
        $topCareers = array_slice($careerCounts, 0, 5, true);

        // Normalize labels for chart/badges
        $skillLabels = array_map('ucwords', array_keys($topSkills));
        $skillValues = array_values($topSkills);

        return view('admin.dashboard', compact(
            'studentsCount',
            'internshipsCount',
            'cvAnalysesCount',
            'students',
            'internships',
            'skillLabels',
            'skillValues',
            'topCareers'
        ));
    }

    public function storeInternship(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'required_skills' => 'required|string',
        ], [
            'title.required' => 'Judul lowongan wajib diisi.',
            'company.required' => 'Nama perusahaan wajib diisi.',
            'location.required' => 'Lokasi wajib diisi.',
            'description.required' => 'Deskripsi lowongan wajib diisi.',
            'required_skills.required' => 'Keahlian yang dibutuhkan wajib diisi.',
        ]);

        // Convert required skills comma separated string into array
        $skillsArray = array_filter(array_map('trim', explode(',', $request->required_skills)));

        Internship::create([
            'title' => $request->title,
            'company' => $request->company,
            'location' => $request->location,
            'description' => $request->description,
            'required_skills' => $skillsArray,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Lowongan magang baru berhasil ditambahkan!');
    }

    public function updateInternship(Request $request, $id)
    {
        $internship = Internship::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'required_skills' => 'required|string',
        ]);

        $skillsArray = array_filter(array_map('trim', explode(',', $request->required_skills)));

        $internship->update([
            'title' => $request->title,
            'company' => $request->company,
            'location' => $request->location,
            'description' => $request->description,
            'required_skills' => $skillsArray,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Lowongan magang berhasil diperbarui!');
    }

    public function destroyInternship($id)
    {
        $internship = Internship::findOrFail($id);
        $internship->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Lowongan magang berhasil dihapus!');
    }

    public function viewStudentAnalysis($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $latestAnalysis = $student->latestCvAnalysis;

        if (!$latestAnalysis) {
            return back()->with('error', 'Mahasiswa ini belum mengunggah CV untuk dianalisis.');
        }

        return view('student.print', [
            'latestAnalysis' => $latestAnalysis,
            'user' => $student
        ]);
    }
}
