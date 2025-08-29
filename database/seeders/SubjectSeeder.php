<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['key' => 'math', 'name' => ['sr' => 'Математика', 'en' => 'Mathematics', 'hu' => 'Matematika']],
            ['key' => 'language', 'name' => ['sr' => 'Српски језик', 'en' => 'Serbian Language', 'hu' => 'Szerb nyelv']],
            ['key' => 'science', 'name' => ['sr' => 'Природа и друштво', 'en' => 'Science', 'hu' => 'Természettudomány']],
            ['key' => 'english', 'name' => ['sr' => 'Енглески језик', 'en' => 'English Language', 'hu' => 'Angol nyelv']],
            ['key' => 'history', 'name' => ['sr' => 'Историја', 'en' => 'History', 'hu' => 'Történelem']],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}