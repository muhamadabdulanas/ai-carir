<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$view = view('admin.dashboard', [
    'studentsCount' => 1,
    'internshipsCount' => 1,
    'cvAnalysesCount' => 1,
    'students' => App\Models\User::where('role', 'student')->get(),
    'internships' => App\Models\Internship::all(),
    'skillLabels' => [],
    'skillValues' => [],
    'topCareers' => []
])->render();

if (strpos($view, 'admin/internships/1') !== false) {
    echo "SUCCESS: Found compiled route for ID 1!\n";
} else {
    echo "FAILED: Could not find compiled route for ID 1!\n";
}

// Let's print the form HTML for the first internship
if (preg_match('/<form action="[^"]*admin\/internships[^"]*".*?<\/form>/s', $view, $matches)) {
    echo "Form snippet:\n" . $matches[0] . "\n";
} else {
    echo "No matching form found!\n";
}
