<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Database\Seeder;

class ComprehensiveQuizSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate existing data
        \DB::table('attempt_answers')->truncate();
        \DB::table('attempts')->truncate();
        \DB::table('answers')->truncate();
        \DB::table('questions')->truncate();
        \DB::table('quizzes')->truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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

                // Create 5 questions per quiz
                $questions = $this->getQuestions($subject->key, $grade);

                foreach ($questions as $index => $questionData) {
                    $question = Question::create([
                        'quiz_id' => $quiz->id,
                        'prompt' => $questionData['prompt'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'question_type' => $questionData['type'],
                        'order' => $index + 1,
                    ]);

                    // Create answers for each question
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
            ],
            4 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 234 × 5?', 'en' => 'How much is 234 × 5?', 'hu' => 'Mennyi 234 × 5?'], 'answers' => [['text' => ['sr' => '1170', 'en' => '1170', 'hu' => '1170'], 'is_correct' => true], ['text' => ['sr' => '1160', 'en' => '1160', 'hu' => '1160'], 'is_correct' => false], ['text' => ['sr' => '1180', 'en' => '1180', 'hu' => '1180'], 'is_correct' => false], ['text' => ['sr' => '1150', 'en' => '1150', 'hu' => '1150'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта је површина правоугаоника 6×4?', 'en' => 'What is the area of rectangle 6×4?', 'hu' => 'Mennyi a 6×4-es téglalap területe?'], 'answers' => [['text' => ['sr' => '20 cm²', 'en' => '20 cm²', 'hu' => '20 cm²'], 'is_correct' => false], ['text' => ['sr' => '24 cm²', 'en' => '24 cm²', 'hu' => '24 cm²'], 'is_correct' => true], ['text' => ['sr' => '28 cm²', 'en' => '28 cm²', 'hu' => '28 cm²'], 'is_correct' => false], ['text' => ['sr' => '22 cm²', 'en' => '22 cm²', 'hu' => '22 cm²'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Који од ових бројева су прости?', 'en' => 'Which of these numbers are prime?', 'hu' => 'Melyik számok prímek?'], 'answers' => [['text' => ['sr' => '2', 'en' => '2', 'hu' => '2'], 'is_correct' => true], ['text' => ['sr' => '4', 'en' => '4', 'hu' => '4'], 'is_correct' => false], ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => true], ['text' => ['sr' => '9', 'en' => '9', 'hu' => '9'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'У 1 килограму има 1000 грама', 'en' => 'There are 1000 grams in 1 kilogram', 'hu' => '1 kilogrammban 1000 gramm van'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 3/4 + 1/4?', 'en' => 'How much is 3/4 + 1/4?', 'hu' => 'Mennyi 3/4 + 1/4?'], 'answers' => [['text' => ['sr' => '4/4 = 1', 'en' => '4/4 = 1', 'hu' => '4/4 = 1'], 'is_correct' => true], ['text' => ['sr' => '4/8', 'en' => '4/8', 'hu' => '4/8'], 'is_correct' => false], ['text' => ['sr' => '2/4', 'en' => '2/4', 'hu' => '2/4'], 'is_correct' => false], ['text' => ['sr' => '1/2', 'en' => '1/2', 'hu' => '1/2'], 'is_correct' => false]]],
            ],
            5 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 2³?', 'en' => 'How much is 2³?', 'hu' => 'Mennyi 2³?'], 'answers' => [['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => false], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => true], ['text' => ['sr' => '4', 'en' => '4', 'hu' => '4'], 'is_correct' => false], ['text' => ['sr' => '9', 'en' => '9', 'hu' => '9'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колика је површина круга са полупречником 3?', 'en' => 'What is the area of circle with radius 3?', 'hu' => 'Mennyi a 3 sugarú kör területe?'], 'answers' => [['text' => ['sr' => '6π', 'en' => '6π', 'hu' => '6π'], 'is_correct' => false], ['text' => ['sr' => '9π', 'en' => '9π', 'hu' => '9π'], 'is_correct' => true], ['text' => ['sr' => '12π', 'en' => '12π', 'hu' => '12π'], 'is_correct' => false], ['text' => ['sr' => '3π', 'en' => '3π', 'hu' => '3π'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је 0.5 + 0.25?', 'en' => 'How much is 0.5 + 0.25?', 'hu' => 'Mennyi 0.5 + 0.25?'], 'answers' => [['text' => ['sr' => '0.75', 'en' => '0.75', 'hu' => '0.75'], 'is_correct' => true], ['text' => ['sr' => '0.25', 'en' => '0.25', 'hu' => '0.25'], 'is_correct' => false], ['text' => ['sr' => '0.55', 'en' => '0.55', 'hu' => '0.55'], 'is_correct' => false], ['text' => ['sr' => '0.50', 'en' => '0.50', 'hu' => '0.50'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Троугао може имати два тупа угла', 'en' => 'Triangle can have two obtuse angles', 'hu' => 'A háromszögnek lehet két tompa szöge'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => false], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => true]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Које од ових операција дају резултат 12?', 'en' => 'Which operations give result 12?', 'hu' => 'Melyik műveletek eredménye 12?'], 'answers' => [['text' => ['sr' => '3 × 4', 'en' => '3 × 4', 'hu' => '3 × 4'], 'is_correct' => true], ['text' => ['sr' => '24 ÷ 2', 'en' => '24 ÷ 2', 'hu' => '24 ÷ 2'], 'is_correct' => true], ['text' => ['sr' => '15 - 3', 'en' => '15 - 3', 'hu' => '15 - 3'], 'is_correct' => true], ['text' => ['sr' => '6 + 7', 'en' => '6 + 7', 'hu' => '6 + 7'], 'is_correct' => false]]],
            ],
            6 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је √64?', 'en' => 'How much is √64?', 'hu' => 'Mennyi √64?'], 'answers' => [['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => false], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => true], ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => false], ['text' => ['sr' => '9', 'en' => '9', 'hu' => '9'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Ако је x + 5 = 12, колико је x?', 'en' => 'If x + 5 = 12, what is x?', 'hu' => 'Ha x + 5 = 12, mennyi x?'], 'answers' => [['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => false], ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => true], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => false], ['text' => ['sr' => '17', 'en' => '17', 'hu' => '17'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико процената је 1/4?', 'en' => 'What percentage is 1/4?', 'hu' => 'Hány százalék 1/4?'], 'answers' => [['text' => ['sr' => '20%', 'en' => '20%', 'hu' => '20%'], 'is_correct' => false], ['text' => ['sr' => '25%', 'en' => '25%', 'hu' => '25%'], 'is_correct' => true], ['text' => ['sr' => '30%', 'en' => '30%', 'hu' => '30%'], 'is_correct' => false], ['text' => ['sr' => '40%', 'en' => '40%', 'hu' => '40%'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Негативан број помножен негативним бројем даје позитиван резултат', 'en' => 'Negative number multiplied by negative number gives positive result', 'hu' => 'Negatív szám szorozva negatív számmal pozitív eredményt ad'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је обим круга са полупречником 7? (π ≈ 3.14)', 'en' => 'What is circumference of circle with radius 7? (π ≈ 3.14)', 'hu' => 'Mennyi a 7 sugarú kör kerülete? (π ≈ 3.14)'], 'answers' => [['text' => ['sr' => '42', 'en' => '42', 'hu' => '42'], 'is_correct' => false], ['text' => ['sr' => '43.96', 'en' => '43.96', 'hu' => '43.96'], 'is_correct' => true], ['text' => ['sr' => '21.98', 'en' => '21.98', 'hu' => '21.98'], 'is_correct' => false], ['text' => ['sr' => '49', 'en' => '49', 'hu' => '49'], 'is_correct' => false]]],
            ],
            7 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Решите једначину: 2x - 3 = 7', 'en' => 'Solve equation: 2x - 3 = 7', 'hu' => 'Oldja meg az egyenletet: 2x - 3 = 7'], 'answers' => [['text' => ['sr' => 'x = 4', 'en' => 'x = 4', 'hu' => 'x = 4'], 'is_correct' => false], ['text' => ['sr' => 'x = 5', 'en' => 'x = 5', 'hu' => 'x = 5'], 'is_correct' => true], ['text' => ['sr' => 'x = 6', 'en' => 'x = 6', 'hu' => 'x = 6'], 'is_correct' => false], ['text' => ['sr' => 'x = 2', 'en' => 'x = 2', 'hu' => 'x = 2'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колика је вредност израза (3 + 2)²?', 'en' => 'What is the value of (3 + 2)²?', 'hu' => 'Mennyi (3 + 2)² értéke?'], 'answers' => [['text' => ['sr' => '10', 'en' => '10', 'hu' => '10'], 'is_correct' => false], ['text' => ['sr' => '25', 'en' => '25', 'hu' => '25'], 'is_correct' => true], ['text' => ['sr' => '15', 'en' => '15', 'hu' => '15'], 'is_correct' => false], ['text' => ['sr' => '20', 'en' => '20', 'hu' => '20'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Ако је угао А = 30°, колики је комплементаран угао?', 'en' => 'If angle A = 30°, what is complementary angle?', 'hu' => 'Ha az A szög 30°, mennyi a kiegészítő szög?'], 'answers' => [['text' => ['sr' => '50°', 'en' => '50°', 'hu' => '50°'], 'is_correct' => false], ['text' => ['sr' => '60°', 'en' => '60°', 'hu' => '60°'], 'is_correct' => true], ['text' => ['sr' => '70°', 'en' => '70°', 'hu' => '70°'], 'is_correct' => false], ['text' => ['sr' => '150°', 'en' => '150°', 'hu' => '150°'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Збир углова у троуглу је 180°', 'en' => 'Sum of angles in triangle is 180°', 'hu' => 'A háromszög szögeinek összege 180°'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је -5 + 3?', 'en' => 'How much is -5 + 3?', 'hu' => 'Mennyi -5 + 3?'], 'answers' => [['text' => ['sr' => '-2', 'en' => '-2', 'hu' => '-2'], 'is_correct' => true], ['text' => ['sr' => '2', 'en' => '2', 'hu' => '2'], 'is_correct' => false], ['text' => ['sr' => '-8', 'en' => '-8', 'hu' => '-8'], 'is_correct' => false], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => false]]],
            ],
            8 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Решите систем: x + y = 5, x - y = 1', 'en' => 'Solve system: x + y = 5, x - y = 1', 'hu' => 'Oldja meg a rendszert: x + y = 5, x - y = 1'], 'answers' => [['text' => ['sr' => 'x=3, y=2', 'en' => 'x=3, y=2', 'hu' => 'x=3, y=2'], 'is_correct' => true], ['text' => ['sr' => 'x=2, y=3', 'en' => 'x=2, y=3', 'hu' => 'x=2, y=3'], 'is_correct' => false], ['text' => ['sr' => 'x=4, y=1', 'en' => 'x=4, y=1', 'hu' => 'x=4, y=1'], 'is_correct' => false], ['text' => ['sr' => 'x=1, y=4', 'en' => 'x=1, y=4', 'hu' => 'x=1, y=4'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колика је дискриминанта једначине x² - 4x + 3 = 0?', 'en' => 'What is discriminant of equation x² - 4x + 3 = 0?', 'hu' => 'Mennyi az x² - 4x + 3 = 0 egyenlet diszkriminánsa?'], 'answers' => [['text' => ['sr' => '2', 'en' => '2', 'hu' => '2'], 'is_correct' => false], ['text' => ['sr' => '4', 'en' => '4', 'hu' => '4'], 'is_correct' => true], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => false], ['text' => ['sr' => '16', 'en' => '16', 'hu' => '16'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико је sin(30°)?', 'en' => 'How much is sin(30°)?', 'hu' => 'Mennyi sin(30°)?'], 'answers' => [['text' => ['sr' => '1/2', 'en' => '1/2', 'hu' => '1/2'], 'is_correct' => true], ['text' => ['sr' => '√3/2', 'en' => '√3/2', 'hu' => '√3/2'], 'is_correct' => false], ['text' => ['sr' => '1', 'en' => '1', 'hu' => '1'], 'is_correct' => false], ['text' => ['sr' => '√2/2', 'en' => '√2/2', 'hu' => '√2/2'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Функција y = x² је парна функција', 'en' => 'Function y = x² is even function', 'hu' => 'Az y = x² függvény páros függvény'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колики је волумен коцке са страницом 4?', 'en' => 'What is volume of cube with side 4?', 'hu' => 'Mennyi a 4 élű kocka térfogata?'], 'answers' => [['text' => ['sr' => '16', 'en' => '16', 'hu' => '16'], 'is_correct' => false], ['text' => ['sr' => '48', 'en' => '48', 'hu' => '48'], 'is_correct' => false], ['text' => ['sr' => '64', 'en' => '64', 'hu' => '64'], 'is_correct' => true], ['text' => ['sr' => '24', 'en' => '24', 'hu' => '24'], 'is_correct' => false]]],
            ]
        ];
    }

    private function getLanguageQuestions(): array
    {
        return [
            1 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико слова има српска азбука?', 'en' => 'How many letters are in Serbian alphabet?', 'hu' => 'Hány betű van a szerb ábécében?'], 'answers' => [['text' => ['sr' => '28', 'en' => '28', 'hu' => '28'], 'is_correct' => false], ['text' => ['sr' => '30', 'en' => '30', 'hu' => '30'], 'is_correct' => true], ['text' => ['sr' => '32', 'en' => '32', 'hu' => '32'], 'is_correct' => false], ['text' => ['sr' => '26', 'en' => '26', 'hu' => '26'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта је глагол?', 'en' => 'What is a verb?', 'hu' => 'Mi az ige?'], 'answers' => [['text' => ['sr' => 'Реч која значи радњу', 'en' => 'Word that means action', 'hu' => 'Cselekvést jelentő szó'], 'is_correct' => true], ['text' => ['sr' => 'Реч која значи особину', 'en' => 'Word that means property', 'hu' => 'Tulajdonságot jelentő szó'], 'is_correct' => false], ['text' => ['sr' => 'Реч која значи предмет', 'en' => 'Word that means object', 'hu' => 'Tárgyat jelentő szó'], 'is_correct' => false], ['text' => ['sr' => 'Реч која значи број', 'en' => 'Word that means number', 'hu' => 'Számot jelentő szó'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Реченица мора да има глагол', 'en' => 'Sentence must have a verb', 'hu' => 'A mondatnak igét kell tartalmaznia'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Које слово долази после "Н" у азбуци?', 'en' => 'Which letter comes after "Н" in alphabet?', 'hu' => 'Melyik betű jön "Н" után az ábécében?'], 'answers' => [['text' => ['sr' => 'М', 'en' => 'М', 'hu' => 'М'], 'is_correct' => false], ['text' => ['sr' => 'Њ', 'en' => 'Њ', 'hu' => 'Њ'], 'is_correct' => true], ['text' => ['sr' => 'О', 'en' => 'О', 'hu' => 'О'], 'is_correct' => false], ['text' => ['sr' => 'П', 'en' => 'П', 'hu' => 'П'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Које су врсте речи?', 'en' => 'What are types of words?', 'hu' => 'Milyen szófajok vannak?'], 'answers' => [['text' => ['sr' => 'Именица', 'en' => 'Noun', 'hu' => 'Főnév'], 'is_correct' => true], ['text' => ['sr' => 'Глагол', 'en' => 'Verb', 'hu' => 'Ige'], 'is_correct' => true], ['text' => ['sr' => 'Придев', 'en' => 'Adjective', 'hu' => 'Melléknév'], 'is_correct' => true], ['text' => ['sr' => 'Број', 'en' => 'Number', 'hu' => 'Szám'], 'is_correct' => false]]],
            ],
            2 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта је именица?', 'en' => 'What is a noun?', 'hu' => 'Mi a főnév?'], 'answers' => [['text' => ['sr' => 'Реч која означава особу, животињу или ствар', 'en' => 'Word that denotes person, animal or thing', 'hu' => 'Személyt, állatot vagy dolgot jelölő szó'], 'is_correct' => true], ['text' => ['sr' => 'Реч која означава радњу', 'en' => 'Word that denotes action', 'hu' => 'Cselekvést jelölő szó'], 'is_correct' => false], ['text' => ['sr' => 'Реч која означава особину', 'en' => 'Word that denotes property', 'hu' => 'Tulajdonságot jelölő szó'], 'is_correct' => false], ['text' => ['sr' => 'Реч која означава место', 'en' => 'Word that denotes place', 'hu' => 'Helyet jelölő szó'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико падежа има српски језик?', 'en' => 'How many cases does Serbian language have?', 'hu' => 'Hány eset van a szerb nyelvben?'], 'answers' => [['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => false], ['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => true], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => false], ['text' => ['sr' => '5', 'en' => '5', 'hu' => '5'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Велико слово се пише на почетку реченице', 'en' => 'Capital letter is written at beginning of sentence', 'hu' => 'A mondat elején nagy betűt írunk'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта је антоним?', 'en' => 'What is antonym?', 'hu' => 'Mi az antonima?'], 'answers' => [['text' => ['sr' => 'Реч истог значења', 'en' => 'Word with same meaning', 'hu' => 'Azonos jelentésű szó'], 'is_correct' => false], ['text' => ['sr' => 'Реч супротног значења', 'en' => 'Word with opposite meaning', 'hu' => 'Ellentétes jelentésű szó'], 'is_correct' => true], ['text' => ['sr' => 'Реч сличног значења', 'en' => 'Word with similar meaning', 'hu' => 'Hasonló jelentésű szó'], 'is_correct' => false], ['text' => ['sr' => 'Нова реч', 'en' => 'New word', 'hu' => 'Új szó'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Која слова су самогласници?', 'en' => 'Which letters are vowels?', 'hu' => 'Melyik betűk magánhangzók?'], 'answers' => [['text' => ['sr' => 'А', 'en' => 'А', 'hu' => 'А'], 'is_correct' => true], ['text' => ['sr' => 'Е', 'en' => 'Е', 'hu' => 'Е'], 'is_correct' => true], ['text' => ['sr' => 'Б', 'en' => 'Б', 'hu' => 'Б'], 'is_correct' => false], ['text' => ['sr' => 'И', 'en' => 'И', 'hu' => 'И'], 'is_correct' => true]]],
            ],
            3 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта је синоним?', 'en' => 'What is synonym?', 'hu' => 'Mi a szinonima?'], 'answers' => [['text' => ['sr' => 'Реч истог значења', 'en' => 'Word with same meaning', 'hu' => 'Azonos jelentésű szó'], 'is_correct' => true], ['text' => ['sr' => 'Реч супротног значења', 'en' => 'Word with opposite meaning', 'hu' => 'Ellentétes jelentésű szó'], 'is_correct' => false], ['text' => ['sr' => 'Нова реч', 'en' => 'New word', 'hu' => 'Új szó'], 'is_correct' => false], ['text' => ['sr' => 'Страна реч', 'en' => 'Foreign word', 'hu' => 'Idegen szó'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Који је род речи "мачка"?', 'en' => 'What is gender of word "мачка"?', 'hu' => 'Mi a "мачка" szó neme?'], 'answers' => [['text' => ['sr' => 'Мушки', 'en' => 'Masculine', 'hu' => 'Hímnem'], 'is_correct' => false], ['text' => ['sr' => 'Женски', 'en' => 'Feminine', 'hu' => 'Nőnem'], 'is_correct' => true], ['text' => ['sr' => 'Средњи', 'en' => 'Neuter', 'hu' => 'Semleges'], 'is_correct' => false], ['text' => ['sr' => 'Нема род', 'en' => 'No gender', 'hu' => 'Nincs nem'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Придев се слаже са именицом у роду, броју и падежу', 'en' => 'Adjective agrees with noun in gender, number and case', 'hu' => 'A melléknév egyezik a főnévvel nemben, számban és esetben'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта је основа речи "играти"?', 'en' => 'What is stem of word "играти"?', 'hu' => 'Mi az "играти" szó töve?'], 'answers' => [['text' => ['sr' => 'игр', 'en' => 'игр', 'hu' => 'игр'], 'is_correct' => true], ['text' => ['sr' => 'игра', 'en' => 'игра', 'hu' => 'игра'], 'is_correct' => false], ['text' => ['sr' => 'играт', 'en' => 'играт', 'hu' => 'играт'], 'is_correct' => false], ['text' => ['sr' => 'ати', 'en' => 'ати', 'hu' => 'ати'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Које су врсте глагола?', 'en' => 'What are types of verbs?', 'hu' => 'Milyen fajtái vannak az igéknek?'], 'answers' => [['text' => ['sr' => 'Свршени', 'en' => 'Perfective', 'hu' => 'Befejezett'], 'is_correct' => true], ['text' => ['sr' => 'Несвршени', 'en' => 'Imperfective', 'hu' => 'Befejezetlen'], 'is_correct' => true], ['text' => ['sr' => 'Прелазни', 'en' => 'Transitive', 'hu' => 'Tárgyas'], 'is_correct' => true], ['text' => ['sr' => 'Главни', 'en' => 'Main', 'hu' => 'Fő'], 'is_correct' => false]]],
            ],
            4 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта је књижевна врста "басна"?', 'en' => 'What literary genre is "fable"?', 'hu' => 'Milyen irodalmi műfaj a "mese"?'], 'answers' => [['text' => ['sr' => 'Кратка прича са поуком', 'en' => 'Short story with moral', 'hu' => 'Rövid történet tanulsággal'], 'is_correct' => true], ['text' => ['sr' => 'Дуга прича', 'en' => 'Long story', 'hu' => 'Hosszú történet'], 'is_correct' => false], ['text' => ['sr' => 'Песма', 'en' => 'Poem', 'hu' => 'Vers'], 'is_correct' => false], ['text' => ['sr' => 'Драма', 'en' => 'Drama', 'hu' => 'Dráma'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Које време глагола је у реченици "Јуче сам читао"?', 'en' => 'What tense is in sentence "Yesterday I was reading"?', 'hu' => 'Milyen igeidő van a "Tegnap olvastam" mondatban?'], 'answers' => [['text' => ['sr' => 'Садашње', 'en' => 'Present', 'hu' => 'Jelen'], 'is_correct' => false], ['text' => ['sr' => 'Прошло', 'en' => 'Past', 'hu' => 'Múlt'], 'is_correct' => true], ['text' => ['sr' => 'Будуће', 'en' => 'Future', 'hu' => 'Jövő'], 'is_correct' => false], ['text' => ['sr' => 'Условно', 'en' => 'Conditional', 'hu' => 'Feltételes'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Придев "велики" има све три степена поређења', 'en' => 'Adjective "big" has all three degrees of comparison', 'hu' => 'A "nagy" melléknevnek mind a három fokozata van'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта је рима?', 'en' => 'What is rhyme?', 'hu' => 'Mi a rím?'], 'answers' => [['text' => ['sr' => 'Понављање истих гласова на крају стихова', 'en' => 'Repetition of same sounds at end of verses', 'hu' => 'Azonos hangok ismétlése a verssorok végén'], 'is_correct' => true], ['text' => ['sr' => 'Понављање речи', 'en' => 'Repetition of words', 'hu' => 'Szavak ismétlése'], 'is_correct' => false], ['text' => ['sr' => 'Брзо говорење', 'en' => 'Fast speaking', 'hu' => 'Gyors beszéd'], 'is_correct' => false], ['text' => ['sr' => 'Тихо говорење', 'en' => 'Quiet speaking', 'hu' => 'Halk beszéd'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Које су врсте придева?', 'en' => 'What are types of adjectives?', 'hu' => 'Milyen fajtái vannak a mellékneveknek?'], 'answers' => [['text' => ['sr' => 'Описни', 'en' => 'Descriptive', 'hu' => 'Leíró'], 'is_correct' => true], ['text' => ['sr' => 'Присвојни', 'en' => 'Possessive', 'hu' => 'Birtokos'], 'is_correct' => true], ['text' => ['sr' => 'Градни', 'en' => 'Gradable', 'hu' => 'Fokozható'], 'is_correct' => false], ['text' => ['sr' => 'Показни', 'en' => 'Demonstrative', 'hu' => 'Mutató'], 'is_correct' => false]]],
            ]
        ];
    }

    private function getScienceQuestions(): array
    {
        return [
            1 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Која је најближа звезда Земљи?', 'en' => 'What is closest star to Earth?', 'hu' => 'Melyik a Földhöz legközelebbi csillag?'], 'answers' => [['text' => ['sr' => 'Месец', 'en' => 'Moon', 'hu' => 'Hold'], 'is_correct' => false], ['text' => ['sr' => 'Сунце', 'en' => 'Sun', 'hu' => 'Nap'], 'is_correct' => true], ['text' => ['sr' => 'Венера', 'en' => 'Venus', 'hu' => 'Vénusz'], 'is_correct' => false], ['text' => ['sr' => 'Марс', 'en' => 'Mars', 'hu' => 'Mars'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико ногу има паук?', 'en' => 'How many legs does spider have?', 'hu' => 'Hány lába van a póknak?'], 'answers' => [['text' => ['sr' => '6', 'en' => '6', 'hu' => '6'], 'is_correct' => false], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => true], ['text' => ['sr' => '4', 'en' => '4', 'hu' => '4'], 'is_correct' => false], ['text' => ['sr' => '10', 'en' => '10', 'hu' => '10'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Биљке праве кисеоник', 'en' => 'Plants make oxygen', 'hu' => 'A növények oxigént termelnek'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико планета има наш Сунчев систем?', 'en' => 'How many planets are in our Solar system?', 'hu' => 'Hány bolygó van a Naprendszerünkben?'], 'answers' => [['text' => ['sr' => '7', 'en' => '7', 'hu' => '7'], 'is_correct' => false], ['text' => ['sr' => '8', 'en' => '8', 'hu' => '8'], 'is_correct' => true], ['text' => ['sr' => '9', 'en' => '9', 'hu' => '9'], 'is_correct' => false], ['text' => ['sr' => '10', 'en' => '10', 'hu' => '10'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Која стања материје постоје?', 'en' => 'What states of matter exist?', 'hu' => 'Milyen halmazállapotok léteznek?'], 'answers' => [['text' => ['sr' => 'Чврсто', 'en' => 'Solid', 'hu' => 'Szilárd'], 'is_correct' => true], ['text' => ['sr' => 'Течно', 'en' => 'Liquid', 'hu' => 'Folyékony'], 'is_correct' => true], ['text' => ['sr' => 'Гасовито', 'en' => 'Gas', 'hu' => 'Gáznemű'], 'is_correct' => true], ['text' => ['sr' => 'Магично', 'en' => 'Magical', 'hu' => 'Mágikus'], 'is_correct' => false]]],
            ],
            5 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта је фотосинтеза?', 'en' => 'What is photosynthesis?', 'hu' => 'Mi a fotoszintézis?'], 'answers' => [['text' => ['sr' => 'Процес којим биљке праве храну', 'en' => 'Process by which plants make food', 'hu' => 'Folyamat, amellyel a növények táplálékot készítenek'], 'is_correct' => true], ['text' => ['sr' => 'Процес дисања биљака', 'en' => 'Plant breathing process', 'hu' => 'Növények légzési folyamata'], 'is_correct' => false], ['text' => ['sr' => 'Процес раста биљака', 'en' => 'Plant growth process', 'hu' => 'Növények növekedési folyamata'], 'is_correct' => false], ['text' => ['sr' => 'Процес размножавања', 'en' => 'Reproduction process', 'hu' => 'Szaporodási folyamat'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Вода се кључа на 100°C', 'en' => 'Water boils at 100°C', 'hu' => 'A víz 100°C-on forr'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Који орган је одговоран за пумпање крви?', 'en' => 'Which organ is responsible for pumping blood?', 'hu' => 'Melyik szerv felelős a vér pumpálásáért?'], 'answers' => [['text' => ['sr' => 'Плућа', 'en' => 'Lungs', 'hu' => 'Tüdő'], 'is_correct' => false], ['text' => ['sr' => 'Срце', 'en' => 'Heart', 'hu' => 'Szív'], 'is_correct' => true], ['text' => ['sr' => 'Јетра', 'en' => 'Liver', 'hu' => 'Máj'], 'is_correct' => false], ['text' => ['sr' => 'Бубрези', 'en' => 'Kidneys', 'hu' => 'Vesék'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Које животиње су сисари?', 'en' => 'Which animals are mammals?', 'hu' => 'Melyik állatok emlősök?'], 'answers' => [['text' => ['sr' => 'Пас', 'en' => 'Dog', 'hu' => 'Kutya'], 'is_correct' => true], ['text' => ['sr' => 'Кит', 'en' => 'Whale', 'hu' => 'Bálna'], 'is_correct' => true], ['text' => ['sr' => 'Риба', 'en' => 'Fish', 'hu' => 'Hal'], 'is_correct' => false], ['text' => ['sr' => 'Мишљи', 'en' => 'Mouse', 'hu' => 'Egér'], 'is_correct' => true]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Од чега се састоји вода?', 'en' => 'What does water consist of?', 'hu' => 'Miből áll a víz?'], 'answers' => [['text' => ['sr' => 'H₂O', 'en' => 'H₂O', 'hu' => 'H₂O'], 'is_correct' => true], ['text' => ['sr' => 'CO₂', 'en' => 'CO₂', 'hu' => 'CO₂'], 'is_correct' => false], ['text' => ['sr' => 'O₂', 'en' => 'O₂', 'hu' => 'O₂'], 'is_correct' => false], ['text' => ['sr' => 'H₂', 'en' => 'H₂', 'hu' => 'H₂'], 'is_correct' => false]]],
            ]
        ];
    }

    private function getEnglishQuestions(): array
    {
        return [
            1 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Како се каже "здраво" на енглеском?', 'en' => 'How do you say "hello" in English?', 'hu' => 'Hogyan mondják angolul, hogy "szia"?'], 'answers' => [['text' => ['sr' => 'Hello', 'en' => 'Hello', 'hu' => 'Hello'], 'is_correct' => true], ['text' => ['sr' => 'Goodbye', 'en' => 'Goodbye', 'hu' => 'Goodbye'], 'is_correct' => false], ['text' => ['sr' => 'Thank you', 'en' => 'Thank you', 'hu' => 'Thank you'], 'is_correct' => false], ['text' => ['sr' => 'Please', 'en' => 'Please', 'hu' => 'Please'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Како се каже "мачка" на енглеском?', 'en' => 'How do you say "cat" in English?', 'hu' => 'Hogyan mondják angolul, hogy "macska"?'], 'answers' => [['text' => ['sr' => 'Dog', 'en' => 'Dog', 'hu' => 'Dog'], 'is_correct' => false], ['text' => ['sr' => 'Cat', 'en' => 'Cat', 'hu' => 'Cat'], 'is_correct' => true], ['text' => ['sr' => 'Bird', 'en' => 'Bird', 'hu' => 'Bird'], 'is_correct' => false], ['text' => ['sr' => 'Fish', 'en' => 'Fish', 'hu' => 'Fish'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => '"Apple" значи јабука на енглеском', 'en' => '"Apple" means apple in English', 'hu' => 'Az "Apple" almát jelent angolul'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Колико слова има енглески алфабет?', 'en' => 'How many letters are in English alphabet?', 'hu' => 'Hány betű van az angol ábécében?'], 'answers' => [['text' => ['sr' => '24', 'en' => '24', 'hu' => '24'], 'is_correct' => false], ['text' => ['sr' => '26', 'en' => '26', 'hu' => '26'], 'is_correct' => true], ['text' => ['sr' => '28', 'en' => '28', 'hu' => '28'], 'is_correct' => false], ['text' => ['sr' => '30', 'en' => '30', 'hu' => '30'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Које су боје на енглеском?', 'en' => 'What are colors in English?', 'hu' => 'Melyek a színek angolul?'], 'answers' => [['text' => ['sr' => 'Red', 'en' => 'Red', 'hu' => 'Red'], 'is_correct' => true], ['text' => ['sr' => 'Blue', 'en' => 'Blue', 'hu' => 'Blue'], 'is_correct' => true], ['text' => ['sr' => 'House', 'en' => 'House', 'hu' => 'House'], 'is_correct' => false], ['text' => ['sr' => 'Green', 'en' => 'Green', 'hu' => 'Green'], 'is_correct' => true]]],
            ],
            3 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Који је правилан облик глагола "to be" за "I"?', 'en' => 'What is correct form of verb "to be" for "I"?', 'hu' => 'Mi a "to be" ige helyes alakja "I"-hez?'], 'answers' => [['text' => ['sr' => 'am', 'en' => 'am', 'hu' => 'am'], 'is_correct' => true], ['text' => ['sr' => 'is', 'en' => 'is', 'hu' => 'is'], 'is_correct' => false], ['text' => ['sr' => 'are', 'en' => 'are', 'hu' => 'are'], 'is_correct' => false], ['text' => ['sr' => 'be', 'en' => 'be', 'hu' => 'be'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Шта значи "book" на српском?', 'en' => 'What does "book" mean in Serbian?', 'hu' => 'Mit jelent a "book" szerbül?'], 'answers' => [['text' => ['sr' => 'Књига', 'en' => 'Књига', 'hu' => 'Књига'], 'is_correct' => true], ['text' => ['sr' => 'Стол', 'en' => 'Стол', 'hu' => 'Стол'], 'is_correct' => false], ['text' => ['sr' => 'Столица', 'en' => 'Столица', 'hu' => 'Столица'], 'is_correct' => false], ['text' => ['sr' => 'Прозор', 'en' => 'Прозор', 'hu' => 'Прозор'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => '"She" се користи за женски род', 'en' => '"She" is used for feminine gender', 'hu' => 'A "She"-t nőnemre használjuk'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Који је множински облик речи "child"?', 'en' => 'What is plural form of "child"?', 'hu' => 'Mi a "child" szó többes száma?'], 'answers' => [['text' => ['sr' => 'childs', 'en' => 'childs', 'hu' => 'childs'], 'is_correct' => false], ['text' => ['sr' => 'children', 'en' => 'children', 'hu' => 'children'], 'is_correct' => true], ['text' => ['sr' => 'childes', 'en' => 'childes', 'hu' => 'childes'], 'is_correct' => false], ['text' => ['sr' => 'child', 'en' => 'child', 'hu' => 'child'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Које речи означавају чланове породице?', 'en' => 'Which words mean family members?', 'hu' => 'Melyik szavak jelentenek családtagokat?'], 'answers' => [['text' => ['sr' => 'Mother', 'en' => 'Mother', 'hu' => 'Mother'], 'is_correct' => true], ['text' => ['sr' => 'Father', 'en' => 'Father', 'hu' => 'Father'], 'is_correct' => true], ['text' => ['sr' => 'Table', 'en' => 'Table', 'hu' => 'Table'], 'is_correct' => false], ['text' => ['sr' => 'Sister', 'en' => 'Sister', 'hu' => 'Sister'], 'is_correct' => true]]],
            ]
        ];
    }

    private function getHistoryQuestions(): array
    {
        return [
            3 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Који је главни град Србије?', 'en' => 'What is capital of Serbia?', 'hu' => 'Mi Szerbia fővárosa?'], 'answers' => [['text' => ['sr' => 'Нови Сад', 'en' => 'Novi Sad', 'hu' => 'Újvidék'], 'is_correct' => false], ['text' => ['sr' => 'Београд', 'en' => 'Belgrade', 'hu' => 'Belgrád'], 'is_correct' => true], ['text' => ['sr' => 'Ниш', 'en' => 'Niš', 'hu' => 'Niš'], 'is_correct' => false], ['text' => ['sr' => 'Крагујевац', 'en' => 'Kragujevac', 'hu' => 'Kragujevác'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'На којој реци се налази Београд?', 'en' => 'On which river is Belgrade located?', 'hu' => 'Melyik folyó mellett fekszik Belgrád?'], 'answers' => [['text' => ['sr' => 'Дунав', 'en' => 'Danube', 'hu' => 'Duna'], 'is_correct' => true], ['text' => ['sr' => 'Сава', 'en' => 'Sava', 'hu' => 'Száva'], 'is_correct' => false], ['text' => ['sr' => 'Морава', 'en' => 'Morava', 'hu' => 'Morava'], 'is_correct' => false], ['text' => ['sr' => 'Тиса', 'en' => 'Tisa', 'hu' => 'Tisza'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Србија је била део Југославије', 'en' => 'Serbia was part of Yugoslavia', 'hu' => 'Szerbia Jugoszlávia része volt'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Која је највећа планина у Србији?', 'en' => 'What is highest mountain in Serbia?', 'hu' => 'Melyik a legmagasabb hegy Szerbiában?'], 'answers' => [['text' => ['sr' => 'Копаоник', 'en' => 'Kopaonik', 'hu' => 'Kopaonik'], 'is_correct' => false], ['text' => ['sr' => 'Тара', 'en' => 'Tara', 'hu' => 'Tara'], 'is_correct' => false], ['text' => ['sr' => 'Ђердап', 'en' => 'Đerdap', 'hu' => 'Đerdap'], 'is_correct' => true], ['text' => ['sr' => 'Авала', 'en' => 'Avala', 'hu' => 'Avala'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Које су покрајине Србије?', 'en' => 'What are provinces of Serbia?', 'hu' => 'Melyek Szerbia tartományai?'], 'answers' => [['text' => ['sr' => 'Војводина', 'en' => 'Vojvodina', 'hu' => 'Vajdaság'], 'is_correct' => true], ['text' => ['sr' => 'Косово и Метохија', 'en' => 'Kosovo and Metohija', 'hu' => 'Koszovó és Metohija'], 'is_correct' => true], ['text' => ['sr' => 'Шумадија', 'en' => 'Šumadija', 'hu' => 'Šumadija'], 'is_correct' => false], ['text' => ['sr' => 'Банат', 'en' => 'Banat', 'hu' => 'Bánság'], 'is_correct' => false]]],
            ],
            5 => [
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Када је основана Српска православна црква?', 'en' => 'When was Serbian Orthodox Church founded?', 'hu' => 'Mikor alapították a Szerb Ortodox Egyházat?'], 'answers' => [['text' => ['sr' => '1219. година', 'en' => '1219', 'hu' => '1219'], 'is_correct' => true], ['text' => ['sr' => '1389. година', 'en' => '1389', 'hu' => '1389'], 'is_correct' => false], ['text' => ['sr' => '1459. година', 'en' => '1459', 'hu' => '1459'], 'is_correct' => false], ['text' => ['sr' => '1804. година', 'en' => '1804', 'hu' => '1804'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Ко је био Стефан Немања?', 'en' => 'Who was Stefan Nemanja?', 'hu' => 'Ki volt Stefán Nemanja?'], 'answers' => [['text' => ['sr' => 'Српски владар', 'en' => 'Serbian ruler', 'hu' => 'Szerb uralkodó'], 'is_correct' => true], ['text' => ['sr' => 'Песник', 'en' => 'Poet', 'hu' => 'Költő'], 'is_correct' => false], ['text' => ['sr' => 'Ратник', 'en' => 'Warrior', 'hu' => 'Harcos'], 'is_correct' => false], ['text' => ['sr' => 'Трговац', 'en' => 'Merchant', 'hu' => 'Kereskedő'], 'is_correct' => false]]],
                ['type' => 'true_false', 'prompt' => ['sr' => 'Битка на Косову се одиграла 1389. године', 'en' => 'Battle of Kosovo happened in 1389', 'hu' => 'A rigómezei csata 1389-ben volt'], 'answers' => [['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true], ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false]]],
                ['type' => 'single_choice', 'prompt' => ['sr' => 'Ко је био Милош Обреновић?', 'en' => 'Who was Miloš Obrenović?', 'hu' => 'Ki volt Miloš Obrenović?'], 'answers' => [['text' => ['sr' => 'Кнез Србије', 'en' => 'Prince of Serbia', 'hu' => 'Szerbia fejedelme'], 'is_correct' => true], ['text' => ['sr' => 'Краљ Србије', 'en' => 'King of Serbia', 'hu' => 'Szerbia királya'], 'is_correct' => false], ['text' => ['sr' => 'Цар Србије', 'en' => 'Emperor of Serbia', 'hu' => 'Szerbia császára'], 'is_correct' => false], ['text' => ['sr' => 'Председник Србије', 'en' => 'President of Serbia', 'hu' => 'Szerbia elnöke'], 'is_correct' => false]]],
                ['type' => 'multiple_choice', 'prompt' => ['sr' => 'Које су биле српске династије?', 'en' => 'What were Serbian dynasties?', 'hu' => 'Melyek voltak a szerb dinasztiák?'], 'answers' => [['text' => ['sr' => 'Немањићи', 'en' => 'Nemanjić', 'hu' => 'Nemanjić'], 'is_correct' => true], ['text' => ['sr' => 'Обреновићи', 'en' => 'Obrenović', 'hu' => 'Obrenović'], 'is_correct' => true], ['text' => ['sr' => 'Карађорђевићи', 'en' => 'Karađorđević', 'hu' => 'Karađorđević'], 'is_correct' => true], ['text' => ['sr' => 'Хабзбурговци', 'en' => 'Habsburg', 'hu' => 'Habsburg'], 'is_correct' => false]]],
            ]
        ];
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
            ],
            [
                'type' => 'true_false',
                'prompt' => [
                    'sr' => 'Сунце је звезда',
                    'en' => 'Sun is a star',
                    'hu' => 'A Nap csillag'
                ],
                'answers' => [
                    ['text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'], 'is_correct' => true],
                    ['text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'], 'is_correct' => false],
                ]
            ],
            [
                'type' => 'single_choice',
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
                'type' => 'single_choice',
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
            ],
            [
                'type' => 'multiple_choice',
                'prompt' => [
                    'sr' => 'Које животиње живе у води?',
                    'en' => 'Which animals live in water?',
                    'hu' => 'Melyik állatok élnek vízben?'
                ],
                'answers' => [
                    ['text' => ['sr' => 'Риба', 'en' => 'Fish', 'hu' => 'Hal'], 'is_correct' => true],
                    ['text' => ['sr' => 'Мачка', 'en' => 'Cat', 'hu' => 'Macska'], 'is_correct' => false],
                    ['text' => ['sr' => 'Делфин', 'en' => 'Dolphin', 'hu' => 'Delfin'], 'is_correct' => true],
                    ['text' => ['sr' => 'Пас', 'en' => 'Dog', 'hu' => 'Kutya'], 'is_correct' => false],
                ]
            ]
        ];
    }
}
