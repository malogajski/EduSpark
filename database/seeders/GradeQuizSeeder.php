<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Database\Seeder;

class GradeQuizSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = Subject::all();
        
        foreach ($subjects as $subject) {
            // Create quizzes for grades 1-3 for each subject
            for ($grade = 1; $grade <= 3; $grade++) {
                $quiz = Quiz::create([
                    'grade' => $grade,
                    'subject_id' => $subject->id,
                    'title' => $this->getQuizTitle($subject->key, $grade),
                    'description' => $this->getQuizDescription($subject->key, $grade),
                    'is_published' => true,
                ]);

                // Create 3 questions per quiz
                $questions = $this->getQuestions($subject->key, $grade);
                
                foreach ($questions as $index => $questionData) {
                    $question = Question::create([
                        'quiz_id' => $quiz->id,
                        'prompt' => $questionData['prompt'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'order' => $index + 1,
                    ]);

                    // Create 4 answers per question
                    foreach ($questionData['answers'] as $answerIndex => $answerData) {
                        Answer::create([
                            'question_id' => $question->id,
                            'text' => $answerData['text'],
                            'is_correct' => $answerData['is_correct'],
                            'order' => $answerIndex + 1,
                        ]);
                    }
                }
            }
        }
    }

    private function getQuizTitle(string $subjectKey, int $grade): array
    {
        $titles = [
            'math' => [
                'sr' => "Математика - {$grade}. разред",
                'en' => "Mathematics - Grade {$grade}",
                'hu' => "Matematika - {$grade}. osztály"
            ],
            'language' => [
                'sr' => "Српски језик - {$grade}. разред",
                'en' => "Serbian Language - Grade {$grade}",
                'hu' => "Szerb nyelv - {$grade}. osztály"
            ],
            'science' => [
                'sr' => "Природа и друштво - {$grade}. разред",
                'en' => "Science - Grade {$grade}",
                'hu' => "Természettudomány - {$grade}. osztály"
            ],
            'english' => [
                'sr' => "Енглески језик - {$grade}. разред",
                'en' => "English Language - Grade {$grade}",
                'hu' => "Angol nyelv - {$grade}. osztály"
            ],
            'history' => [
                'sr' => "Историја - {$grade}. разред",
                'en' => "History - Grade {$grade}",
                'hu' => "Történelem - {$grade}. osztály"
            ],
        ];

        return $titles[$subjectKey] ?? ['sr' => 'Квиз', 'en' => 'Quiz', 'hu' => 'Kvíz'];
    }

    private function getQuizDescription(string $subjectKey, int $grade): array
    {
        return [
            'sr' => "Тест знања за {$grade}. разред",
            'en' => "Knowledge test for grade {$grade}",
            'hu' => "Tudáspróba {$grade}. osztály számára"
        ];
    }

    private function getQuestions(string $subjectKey, int $grade): array
    {
        $questions = [
            'math' => [
                1 => [
                    [
                        'prompt' => [
                            'sr' => 'Колико је 2 + 3?',
                            'en' => 'How much is 2 + 3?',
                            'hu' => 'Mennyi 2 + 3?'
                        ],
                        'answers' => [
                            ['text' => ['sr' => '4', 'en' => '4', 'hu' => '4'], 'is_correct' => false],
                            ['text' => ['sr' => '5', 'en' => '5', 'hu' => '5'], 'is_correct' => true],
                            ['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => false],
                            ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => false],
                        ]
                    ],
                    [
                        'prompt' => [
                            'sr' => 'Колико је 10 - 4?',
                            'en' => 'How much is 10 - 4?',
                            'hu' => 'Mennyi 10 - 4?'
                        ],
                        'answers' => [
                            ['text' => ['sr' => '5', 'en' => '5', 'hu' => '5'], 'is_correct' => false],
                            ['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => true],
                            ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => false],
                            ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => false],
                        ]
                    ],
                    [
                        'prompt' => [
                            'sr' => 'Која је то геометријска фигура са три странице?',
                            'en' => 'What geometric shape has three sides?',
                            'hu' => 'Melyik geometriai alaknak van három oldala?'
                        ],
                        'answers' => [
                            ['text' => ['sr' => 'Квадрат', 'en' => 'Square', 'hu' => 'Négyzet'], 'is_correct' => false],
                            ['text' => ['sr' => 'Троугао', 'en' => 'Triangle', 'hu' => 'Háromszög'], 'is_correct' => true],
                            ['text' => ['sr' => 'Круг', 'en' => 'Circle', 'hu' => 'Kör'], 'is_correct' => false],
                            ['text' => ['sr' => 'Правоугаоник', 'en' => 'Rectangle', 'hu' => 'Téglalap'], 'is_correct' => false],
                        ]
                    ]
                ]
            ],
            'language' => [
                1 => [
                    [
                        'prompt' => [
                            'sr' => 'Колико слова има српска азбука?',
                            'en' => 'How many letters are in the Serbian alphabet?',
                            'hu' => 'Hány betű van a szerb ábécében?'
                        ],
                        'answers' => [
                            ['text' => ['sr' => '28', 'en' => '28', 'hu' => '28'], 'is_correct' => false],
                            ['text' => ['sr' => '30', 'en' => '30', 'hu' => '30'], 'is_correct' => true],
                            ['text' => ['sr' => '32', 'en' => '32', 'hu' => '32'], 'is_correct' => false],
                            ['text' => ['sr' => '26', 'en' => '26', 'hu' => '26'], 'is_correct' => false],
                        ]
                    ],
                    [
                        'prompt' => [
                            'sr' => 'Шта је глагол?',
                            'en' => 'What is a verb?',
                            'hu' => 'Mi az ige?'
                        ],
                        'answers' => [
                            ['text' => ['sr' => 'Реч која значи радњу', 'en' => 'Word that means action', 'hu' => 'Szó, amely cselekvést jelent'], 'is_correct' => true],
                            ['text' => ['sr' => 'Реч која значи особину', 'en' => 'Word that means property', 'hu' => 'Szó, amely tulajdonságot jelent'], 'is_correct' => false],
                            ['text' => ['sr' => 'Реч која значи предмет', 'en' => 'Word that means object', 'hu' => 'Szó, amely tárgyat jelent'], 'is_correct' => false],
                            ['text' => ['sr' => 'Реч која значи број', 'en' => 'Word that means number', 'hu' => 'Szó, amely számot jelent'], 'is_correct' => false],
                        ]
                    ],
                    [
                        'prompt' => [
                            'sr' => 'Који је главни град Србије?',
                            'en' => 'What is the capital of Serbia?',
                            'hu' => 'Mi Szerbia fővárosa?'
                        ],
                        'answers' => [
                            ['text' => ['sr' => 'Нови Сад', 'en' => 'Novi Sad', 'hu' => 'Újvidék'], 'is_correct' => false],
                            ['text' => ['sr' => 'Београд', 'en' => 'Belgrade', 'hu' => 'Belgrád'], 'is_correct' => true],
                            ['text' => ['sr' => 'Ниш', 'en' => 'Niš', 'hu' => 'Niš'], 'is_correct' => false],
                            ['text' => ['sr' => 'Крагујевац', 'en' => 'Kragujevac', 'hu' => 'Kragujevác'], 'is_correct' => false],
                        ]
                    ]
                ]
            ]
        ];

        // Return questions for the specific subject and grade, or generic questions
        return $questions[$subjectKey][$grade] ?? $this->getGenericQuestions($grade);
    }

    private function getGenericQuestions(int $grade): array
    {
        return [
            [
                'prompt' => [
                    'sr' => 'Колико дана има седмица?',
                    'en' => 'How many days are in a week?',
                    'hu' => 'Hány nap van egy héten?'
                ],
                'answers' => [
                    ['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => false],
                    ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => true],
                    ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => false],
                    ['text' => ['sr' => '5', 'en' => '5', 'hu' => '5'], 'is_correct' => false],
                ]
            ],
            [
                'prompt' => [
                    'sr' => 'Која је боја сунца?',
                    'en' => 'What color is the sun?',
                    'hu' => 'Milyen színű a nap?'
                ],
                'answers' => [
                    ['text' => ['sr' => 'Плава', 'en' => 'Blue', 'hu' => 'Kék'], 'is_correct' => false],
                    ['text' => ['sr' => 'Жута', 'en' => 'Yellow', 'hu' => 'Sárga'], 'is_correct' => true],
                    ['text' => ['sr' => 'Црвена', 'en' => 'Red', 'hu' => 'Piros'], 'is_correct' => false],
                    ['text' => ['sr' => 'Зелена', 'en' => 'Green', 'hu' => 'Zöld'], 'is_correct' => false],
                ]
            ],
            [
                'prompt' => [
                    'sr' => 'Колико ногу има мачка?',
                    'en' => 'How many legs does a cat have?',
                    'hu' => 'Hány lába van egy macskának?'
                ],
                'answers' => [
                    ['text' => ['sr' => '2', 'en' => '2', 'hu' => '2'], 'is_correct' => false],
                    ['text' => ['sr' => '4', 'en' => '4', 'hu' => '4'], 'is_correct' => true],
                    ['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => false],
                    ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => false],
                ]
            ]
        ];
    }
}