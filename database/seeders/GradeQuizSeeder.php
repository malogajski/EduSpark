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
            // Create quizzes for grades 1-8 for each subject
            for ($grade = 1; $grade <= 8; $grade++) {
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
        $mathQuestions = $this->getMathQuestions();
        $languageQuestions = $this->getLanguageQuestions();
        $scienceQuestions = $this->getScienceQuestions();
        $englishQuestions = $this->getEnglishQuestions();
        $historyQuestions = $this->getHistoryQuestions();

        $questions = [
            'math' => $mathQuestions,
            'language' => $languageQuestions,
            'science' => $scienceQuestions,
            'english' => $englishQuestions,
            'history' => $historyQuestions,
        ];

        return $questions[$subjectKey][$grade] ?? $this->getGenericQuestions($grade);
    }

    private function getMathQuestions(): array
    {
        return [
            1 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 2 + 3?', 'en' => 'How much is 2 + 3?', 'hu' => 'Mennyi 2 + 3?'], 'answers' => [['text' => ['sr' => '4', 'en' => '4', 'hu' => '4'], 'is_correct' => false], ['text' => ['sr' => '5', 'en' => '5', 'hu' => '5'], 'is_correct' => true], ['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => false], ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 10 - 4?', 'en' => 'How much is 10 - 4?', 'hu' => 'Mennyi 10 - 4?'], 'answers' => [['text' => ['sr' => '5', 'en' => '5', 'hu' => '5'], 'is_correct' => false], ['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => true], ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => false], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Која геометријска фигура има три странице?', 'en' => 'What geometric shape has three sides?', 'hu' => 'Melyik geometriai alaknak van három oldala?'], 'answers' => [['text' => ['sr' => 'Квадрат', 'en' => 'Square', 'hu' => 'Négyzet'], 'is_correct' => false], ['text' => ['sr' => 'Троугао', 'en' => 'Triangle', 'hu' => 'Háromszög'], 'is_correct' => true], ['text' => ['sr' => 'Круг', 'en' => 'Circle', 'hu' => 'Kör'], 'is_correct' => false], ['text' => ['sr' => 'Правоугаоник', 'en' => 'Rectangle', 'hu' => 'Téglalap'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => '1 + 1 = 2', 'en' => '1 + 1 = 2', 'hu' => '1 + 1 = 2'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 3 × 2?', 'en' => 'How much is 3 × 2?', 'hu' => 'Mennyi 3 × 2?'], 'answers' => [['text' => ['sr' => '5', 'en' => '5', 'hu' => '5'], 'is_correct' => false], ['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => true], ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => false], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => false]]],
            ],
            2 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 15 + 17?', 'en' => 'How much is 15 + 17?', 'hu' => 'Mennyi 15 + 17?'], 'answers' => [['text' => ['sr' => '31', 'en' => '31', 'hu' => '31'], 'is_correct' => false], ['text' => ['sr' => '32', 'en' => '32', 'hu' => '32'], 'is_correct' => true], ['text' => ['sr' => '33', 'en' => '33', 'hu' => '33'], 'is_correct' => false], ['text' => ['sr' => '34', 'en' => '34', 'hu' => '34'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 48 ÷ 6?', 'en' => 'How much is 48 ÷ 6?', 'hu' => 'Mennyi 48 ÷ 6?'], 'answers' => [['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => false], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => true], ['text' => ['sr' => '9', 'en' => '9', 'hu' => '9'], 'is_correct' => false], ['text' => ['sr' => '10', 'en' => '10', 'hu' => '10'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Који бројеви су парни?', 'en' => 'Which numbers are even?', 'hu' => 'Melyik számok párosak?'], 'answers' => [['text' => ['sr' => '2', 'en' => '2', 'hu' => '2'], 'is_correct' => true], ['text' => ['sr' => '3', 'en' => '3', 'hu' => '3'], 'is_correct' => false], ['text' => ['sr' => '4', 'en' => '4', 'hu' => '4'], 'is_correct' => true], ['text' => ['sr' => '5', 'en' => '5', 'hu' => '5'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Квадрат има 4 једнаке странице', 'en' => 'Square has 4 equal sides', 'hu' => 'A négyzetnek 4 egyenlő oldala van'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 7 × 8?', 'en' => 'How much is 7 × 8?', 'hu' => 'Mennyi 7 × 8?'], 'answers' => [['text' => ['sr' => '54', 'en' => '54', 'hu' => '54'], 'is_correct' => false], ['text' => ['sr' => '56', 'en' => '56', 'hu' => '56'], 'is_correct' => true], ['text' => ['sr' => '58', 'en' => '58', 'hu' => '58'], 'is_correct' => false], ['text' => ['sr' => '60', 'en' => '60', 'hu' => '60'], 'is_correct' => false]]],
            ],
            3 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 125 + 289?', 'en' => 'How much is 125 + 289?', 'hu' => 'Mennyi 125 + 289?'], 'answers' => [['text' => ['sr' => '414', 'en' => '414', 'hu' => '414'], 'is_correct' => true], ['text' => ['sr' => '424', 'en' => '424', 'hu' => '424'], 'is_correct' => false], ['text' => ['sr' => '404', 'en' => '404', 'hu' => '404'], 'is_correct' => false], ['text' => ['sr' => '434', 'en' => '434', 'hu' => '434'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колики је обим квадрата са страницом 5 cm?', 'en' => 'What is the perimeter of a square with side 5 cm?', 'hu' => 'Mennyi az 5 cm oldalú négyzet kerülete?'], 'answers' => [['text' => ['sr' => '15 cm', 'en' => '15 cm', 'hu' => '15 cm'], 'is_correct' => false], ['text' => ['sr' => '20 cm', 'en' => '20 cm', 'hu' => '20 cm'], 'is_correct' => true], ['text' => ['sr' => '25 cm', 'en' => '25 cm', 'hu' => '25 cm'], 'is_correct' => false], ['text' => ['sr' => '10 cm', 'en' => '10 cm', 'hu' => '10 cm'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Који бројеви су дељиви са 3?', 'en' => 'Which numbers are divisible by 3?', 'hu' => 'Melyik számok oszthatók 3-mal?'], 'answers' => [['text' => ['sr' => '9', 'en' => '9', 'hu' => '9'], 'is_correct' => true], ['text' => ['sr' => '12', 'en' => '12', 'hu' => '12'], 'is_correct' => true], ['text' => ['sr' => '14', 'en' => '14', 'hu' => '14'], 'is_correct' => false], ['text' => ['sr' => '18', 'en' => '18', 'hu' => '18'], 'is_correct' => true]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'У 1 метру има 100 центиметара', 'en' => 'There are 100 centimeters in 1 meter', 'hu' => '1 méterben 100 centiméter van'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 144 ÷ 12?', 'en' => 'How much is 144 ÷ 12?', 'hu' => 'Mennyi 144 ÷ 12?'], 'answers' => [['text' => ['sr' => '11', 'en' => '11', 'hu' => '11'], 'is_correct' => false], ['text' => ['sr' => '12', 'en' => '12', 'hu' => '12'], 'is_correct' => true], ['text' => ['sr' => '13', 'en' => '13', 'hu' => '13'], 'is_correct' => false], ['text' => ['sr' => '14', 'en' => '14', 'hu' => '14'], 'is_correct' => false]]],
            ]
        ];
    }

    private function getMathQuestions(): array
    {
        // Add grades 4-8 math questions here
        return [];
    }

    private function getLanguageQuestions(): array
    {
        return [];
    }

    private function getScienceQuestions(): array
    {
        return [];
    }

    private function getEnglishQuestions(): array
    {
        return [];
    }

    private function getHistoryQuestions(): array
    {
        return [];
    }

    private function getGenericQuestions(int $grade): array
    {
        return [
            [
                'type' => 'single_choice',
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
            ]
        ];
    }
}
