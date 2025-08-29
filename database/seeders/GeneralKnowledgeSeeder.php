<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneralKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        // Create General Knowledge subject if it doesn't exist
        $generalSubject = Subject::firstOrCreate(
            ['key' => 'general'],
            [
                'name' => [
                    'sr' => 'Опште знање',
                    'en' => 'General Knowledge',
                    'hu' => 'Általános ismeretek'
                ]
            ]
        );

        // Delete existing General Knowledge quizzes
        Quiz::where('subject_id', $generalSubject->id)->delete();

        // Create continent-based capital quizzes
        $this->createEuropeanCapitalsQuiz($generalSubject->id);
        $this->createAsianCapitalsQuiz($generalSubject->id);
        $this->createAfricanCapitalsQuiz($generalSubject->id);
        $this->createAmericanCapitalsQuiz($generalSubject->id);
    }

    private function createEuropeanCapitalsQuiz($subjectId)
    {
        $quiz = Quiz::create([
            'title' => [
                'sr' => 'Главни градови Европе',
                'en' => 'European Capitals',
                'hu' => 'Európai fővárosok'
            ],
            'description' => [
                'sr' => 'Тест знања главних градова европских земаља',
                'en' => 'Test your knowledge of European capitals',
                'hu' => 'Tesztelje európai fővárosok ismeretét'
            ],
            'grade' => 0,
            'subject_id' => $subjectId,
            'is_published' => true
        ]);

        $capitals = [
            ['country' => 'Serbia', 'capital' => 'Belgrade', 'wrong1' => 'Novi Sad', 'wrong2' => 'Niš'],
            ['country' => 'France', 'capital' => 'Paris', 'wrong1' => 'Lyon', 'wrong2' => 'Marseille'],
            ['country' => 'Germany', 'capital' => 'Berlin', 'wrong1' => 'Munich', 'wrong2' => 'Hamburg'],
            ['country' => 'Italy', 'capital' => 'Rome', 'wrong1' => 'Milan', 'wrong2' => 'Naples'],
            ['country' => 'Spain', 'capital' => 'Madrid', 'wrong1' => 'Barcelona', 'wrong2' => 'Seville'],
            ['country' => 'United Kingdom', 'capital' => 'London', 'wrong1' => 'Manchester', 'wrong2' => 'Birmingham'],
            ['country' => 'Russia', 'capital' => 'Moscow', 'wrong1' => 'St. Petersburg', 'wrong2' => 'Novosibirsk'],
            ['country' => 'Poland', 'capital' => 'Warsaw', 'wrong1' => 'Krakow', 'wrong2' => 'Gdansk'],
            ['country' => 'Netherlands', 'capital' => 'Amsterdam', 'wrong1' => 'Rotterdam', 'wrong2' => 'The Hague'],
            ['country' => 'Sweden', 'capital' => 'Stockholm', 'wrong1' => 'Gothenburg', 'wrong2' => 'Malmö'],
            ['country' => 'Norway', 'capital' => 'Oslo', 'wrong1' => 'Bergen', 'wrong2' => 'Trondheim'],
            ['country' => 'Austria', 'capital' => 'Vienna', 'wrong1' => 'Salzburg', 'wrong2' => 'Graz'],
            ['country' => 'Greece', 'capital' => 'Athens', 'wrong1' => 'Thessaloniki', 'wrong2' => 'Patras'],
            ['country' => 'Portugal', 'capital' => 'Lisbon', 'wrong1' => 'Porto', 'wrong2' => 'Coimbra'],
            ['country' => 'Czech Republic', 'capital' => 'Prague', 'wrong1' => 'Brno', 'wrong2' => 'Ostrava'],
            ['country' => 'Hungary', 'capital' => 'Budapest', 'wrong1' => 'Debrecen', 'wrong2' => 'Szeged'],
            ['country' => 'Croatia', 'capital' => 'Zagreb', 'wrong1' => 'Split', 'wrong2' => 'Rijeka'],
            ['country' => 'Romania', 'capital' => 'Bucharest', 'wrong1' => 'Cluj-Napoca', 'wrong2' => 'Timișoara'],
            ['country' => 'Bulgaria', 'capital' => 'Sofia', 'wrong1' => 'Plovdiv', 'wrong2' => 'Varna'],
            ['country' => 'Denmark', 'capital' => 'Copenhagen', 'wrong1' => 'Aarhus', 'wrong2' => 'Odense']
        ];

        $this->createQuestionsForQuiz($quiz->id, $capitals);
    }

    private function createAsianCapitalsQuiz($subjectId)
    {
        $quiz = Quiz::create([
            'title' => [
                'sr' => 'Главни градови Азије',
                'en' => 'Asian Capitals',
                'hu' => 'Ázsiai fővárosok'
            ],
            'description' => [
                'sr' => 'Тест знања главних градова азијских земаља',
                'en' => 'Test your knowledge of Asian capitals',
                'hu' => 'Tesztelje ázsiai fővárosok ismeretét'
            ],
            'grade' => 0,
            'subject_id' => $subjectId,
            'is_published' => true
        ]);

        $capitals = [
            ['country' => 'China', 'capital' => 'Beijing', 'wrong1' => 'Shanghai', 'wrong2' => 'Guangzhou'],
            ['country' => 'Japan', 'capital' => 'Tokyo', 'wrong1' => 'Osaka', 'wrong2' => 'Kyoto'],
            ['country' => 'India', 'capital' => 'New Delhi', 'wrong1' => 'Mumbai', 'wrong2' => 'Kolkata'],
            ['country' => 'South Korea', 'capital' => 'Seoul', 'wrong1' => 'Busan', 'wrong2' => 'Incheon'],
            ['country' => 'Thailand', 'capital' => 'Bangkok', 'wrong1' => 'Chiang Mai', 'wrong2' => 'Phuket'],
            ['country' => 'Vietnam', 'capital' => 'Hanoi', 'wrong1' => 'Ho Chi Minh City', 'wrong2' => 'Da Nang'],
            ['country' => 'Indonesia', 'capital' => 'Jakarta', 'wrong1' => 'Surabaya', 'wrong2' => 'Bandung'],
            ['country' => 'Malaysia', 'capital' => 'Kuala Lumpur', 'wrong1' => 'George Town', 'wrong2' => 'Johor Bahru'],
            ['country' => 'Philippines', 'capital' => 'Manila', 'wrong1' => 'Cebu City', 'wrong2' => 'Davao City'],
            ['country' => 'Singapore', 'capital' => 'Singapore', 'wrong1' => 'Jurong', 'wrong2' => 'Tampines'],
            ['country' => 'Pakistan', 'capital' => 'Islamabad', 'wrong1' => 'Karachi', 'wrong2' => 'Lahore'],
            ['country' => 'Bangladesh', 'capital' => 'Dhaka', 'wrong1' => 'Chittagong', 'wrong2' => 'Sylhet'],
            ['country' => 'Iran', 'capital' => 'Tehran', 'wrong1' => 'Isfahan', 'wrong2' => 'Mashhad'],
            ['country' => 'Turkey', 'capital' => 'Ankara', 'wrong1' => 'Istanbul', 'wrong2' => 'Izmir'],
            ['country' => 'Saudi Arabia', 'capital' => 'Riyadh', 'wrong1' => 'Jeddah', 'wrong2' => 'Mecca'],
            ['country' => 'Israel', 'capital' => 'Jerusalem', 'wrong1' => 'Tel Aviv', 'wrong2' => 'Haifa'],
            ['country' => 'Afghanistan', 'capital' => 'Kabul', 'wrong1' => 'Kandahar', 'wrong2' => 'Herat'],
            ['country' => 'Kazakhstan', 'capital' => 'Nur-Sultan', 'wrong1' => 'Almaty', 'wrong2' => 'Shymkent'],
            ['country' => 'Uzbekistan', 'capital' => 'Tashkent', 'wrong1' => 'Samarkand', 'wrong2' => 'Bukhara'],
            ['country' => 'Myanmar', 'capital' => 'Naypyidaw', 'wrong1' => 'Yangon', 'wrong2' => 'Mandalay']
        ];

        $this->createQuestionsForQuiz($quiz->id, $capitals);
    }

    private function createAfricanCapitalsQuiz($subjectId)
    {
        $quiz = Quiz::create([
            'title' => [
                'sr' => 'Главни градови Африке',
                'en' => 'African Capitals',
                'hu' => 'Afrikai fővárosok'
            ],
            'description' => [
                'sr' => 'Тест знања главних градова афричких земаља',
                'en' => 'Test your knowledge of African capitals',
                'hu' => 'Tesztelje afrikai fővárosok ismeretét'
            ],
            'grade' => 0,
            'subject_id' => $subjectId,
            'is_published' => true
        ]);

        $capitals = [
            ['country' => 'Egypt', 'capital' => 'Cairo', 'wrong1' => 'Alexandria', 'wrong2' => 'Giza'],
            ['country' => 'South Africa', 'capital' => 'Cape Town', 'wrong1' => 'Johannesburg', 'wrong2' => 'Durban'],
            ['country' => 'Nigeria', 'capital' => 'Abuja', 'wrong1' => 'Lagos', 'wrong2' => 'Kano'],
            ['country' => 'Kenya', 'capital' => 'Nairobi', 'wrong1' => 'Mombasa', 'wrong2' => 'Kisumu'],
            ['country' => 'Morocco', 'capital' => 'Rabat', 'wrong1' => 'Casablanca', 'wrong2' => 'Marrakech'],
            ['country' => 'Ethiopia', 'capital' => 'Addis Ababa', 'wrong1' => 'Dire Dawa', 'wrong2' => 'Mekelle'],
            ['country' => 'Ghana', 'capital' => 'Accra', 'wrong1' => 'Kumasi', 'wrong2' => 'Tamale'],
            ['country' => 'Algeria', 'capital' => 'Algiers', 'wrong1' => 'Oran', 'wrong2' => 'Constantine'],
            ['country' => 'Tunisia', 'capital' => 'Tunis', 'wrong1' => 'Sfax', 'wrong2' => 'Sousse'],
            ['country' => 'Libya', 'capital' => 'Tripoli', 'wrong1' => 'Benghazi', 'wrong2' => 'Misrata'],
            ['country' => 'Sudan', 'capital' => 'Khartoum', 'wrong1' => 'Omdurman', 'wrong2' => 'Port Sudan'],
            ['country' => 'Uganda', 'capital' => 'Kampala', 'wrong1' => 'Gulu', 'wrong2' => 'Mbarara'],
            ['country' => 'Tanzania', 'capital' => 'Dodoma', 'wrong1' => 'Dar es Salaam', 'wrong2' => 'Arusha'],
            ['country' => 'Zambia', 'capital' => 'Lusaka', 'wrong1' => 'Kitwe', 'wrong2' => 'Ndola'],
            ['country' => 'Zimbabwe', 'capital' => 'Harare', 'wrong1' => 'Bulawayo', 'wrong2' => 'Chitungwiza'],
            ['country' => 'Botswana', 'capital' => 'Gaborone', 'wrong1' => 'Francistown', 'wrong2' => 'Molepolole'],
            ['country' => 'Senegal', 'capital' => 'Dakar', 'wrong1' => 'Thiès', 'wrong2' => 'Kaolack'],
            ['country' => 'Mali', 'capital' => 'Bamako', 'wrong1' => 'Sikasso', 'wrong2' => 'Mopti'],
            ['country' => 'Chad', 'capital' => 'N\'Djamena', 'wrong1' => 'Moundou', 'wrong2' => 'Sarh'],
            ['country' => 'Rwanda', 'capital' => 'Kigali', 'wrong1' => 'Butare', 'wrong2' => 'Gisenyi']
        ];

        $this->createQuestionsForQuiz($quiz->id, $capitals);
    }

    private function createAmericanCapitalsQuiz($subjectId)
    {
        $quiz = Quiz::create([
            'title' => [
                'sr' => 'Главни градови Америке',
                'en' => 'American Capitals',
                'hu' => 'Amerikai fővárosok'
            ],
            'description' => [
                'sr' => 'Тест знања главних градова земаља Северне и Јужне Америке',
                'en' => 'Test your knowledge of North and South American capitals',
                'hu' => 'Tesztelje észak- és dél-amerikai fővárosok ismeretét'
            ],
            'grade' => 0,
            'subject_id' => $subjectId,
            'is_published' => true
        ]);

        $capitals = [
            ['country' => 'United States', 'capital' => 'Washington D.C.', 'wrong1' => 'New York', 'wrong2' => 'Los Angeles'],
            ['country' => 'Canada', 'capital' => 'Ottawa', 'wrong1' => 'Toronto', 'wrong2' => 'Vancouver'],
            ['country' => 'Mexico', 'capital' => 'Mexico City', 'wrong1' => 'Guadalajara', 'wrong2' => 'Monterrey'],
            ['country' => 'Brazil', 'capital' => 'Brasília', 'wrong1' => 'Rio de Janeiro', 'wrong2' => 'São Paulo'],
            ['country' => 'Argentina', 'capital' => 'Buenos Aires', 'wrong1' => 'Córdoba', 'wrong2' => 'Rosario'],
            ['country' => 'Chile', 'capital' => 'Santiago', 'wrong1' => 'Valparaíso', 'wrong2' => 'Concepción'],
            ['country' => 'Peru', 'capital' => 'Lima', 'wrong1' => 'Arequipa', 'wrong2' => 'Trujillo'],
            ['country' => 'Colombia', 'capital' => 'Bogotá', 'wrong1' => 'Medellín', 'wrong2' => 'Cali'],
            ['country' => 'Venezuela', 'capital' => 'Caracas', 'wrong1' => 'Maracaibo', 'wrong2' => 'Valencia'],
            ['country' => 'Ecuador', 'capital' => 'Quito', 'wrong1' => 'Guayaquil', 'wrong2' => 'Cuenca'],
            ['country' => 'Bolivia', 'capital' => 'Sucre', 'wrong1' => 'La Paz', 'wrong2' => 'Santa Cruz'],
            ['country' => 'Paraguay', 'capital' => 'Asunción', 'wrong1' => 'Ciudad del Este', 'wrong2' => 'San Lorenzo'],
            ['country' => 'Uruguay', 'capital' => 'Montevideo', 'wrong1' => 'Salto', 'wrong2' => 'Paysandú'],
            ['country' => 'Cuba', 'capital' => 'Havana', 'wrong1' => 'Santiago de Cuba', 'wrong2' => 'Camagüey'],
            ['country' => 'Jamaica', 'capital' => 'Kingston', 'wrong1' => 'Spanish Town', 'wrong2' => 'Montego Bay'],
            ['country' => 'Costa Rica', 'capital' => 'San José', 'wrong1' => 'Cartago', 'wrong2' => 'Alajuela'],
            ['country' => 'Panama', 'capital' => 'Panama City', 'wrong1' => 'Colón', 'wrong2' => 'David'],
            ['country' => 'Guatemala', 'capital' => 'Guatemala City', 'wrong1' => 'Antigua', 'wrong2' => 'Quetzaltenango'],
            ['country' => 'Honduras', 'capital' => 'Tegucigalpa', 'wrong1' => 'San Pedro Sula', 'wrong2' => 'La Ceiba'],
            ['country' => 'Nicaragua', 'capital' => 'Managua', 'wrong1' => 'León', 'wrong2' => 'Granada']
        ];

        $this->createQuestionsForQuiz($quiz->id, array_slice($capitals, 0, 20));
    }

    private function createQuestionsForQuiz($quizId, $capitals)
    {
        foreach ($capitals as $index => $data) {
            $question = Question::create([
                'quiz_id' => $quizId,
                'prompt' => [
                    'sr' => 'Који је главни град ' . $data['country'] . '?',
                    'en' => 'What is the capital of ' . $data['country'] . '?',
                    'hu' => 'Mi ' . $data['country'] . ' fővárosa?'
                ],
                'explanation' => [
                    'sr' => $data['capital'] . ' је главни град ' . $data['country'] . '.',
                    'en' => $data['capital'] . ' is the capital of ' . $data['country'] . '.',
                    'hu' => $data['capital'] . ' ' . $data['country'] . ' fővárosa.'
                ],
                'question_type' => 'single_choice',
                'order' => $index + 1
            ]);

            $answers = [
                ['text' => $data['capital'], 'is_correct' => true],
                ['text' => $data['wrong1'], 'is_correct' => false],
                ['text' => $data['wrong2'], 'is_correct' => false]
            ];
            shuffle($answers);

            foreach ($answers as $answerIndex => $answerData) {
                $question->answers()->create([
                    'text' => [
                        'sr' => $answerData['text'],
                        'en' => $answerData['text'],
                        'hu' => $answerData['text']
                    ],
                    'is_correct' => $answerData['is_correct'],
                    'order' => $answerIndex + 1
                ]);
            }
        }
    }
}
