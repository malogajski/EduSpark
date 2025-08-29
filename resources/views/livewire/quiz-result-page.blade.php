<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Result Header -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="text-center mb-6">
            @if($passed)
                <div class="w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-green-600 mb-2">
                    @switch(app()->getLocale())
                        @case('en')
                            Congratulations!
                            @break
                        @case('hu')
                            Gratulálunk!
                            @break
                        @default
                            Честитамо!
                    @endswitch
                </h1>
                <p class="text-lg text-gray-600">
                    @switch(app()->getLocale())
                        @case('en')
                            You passed the quiz!
                            @break
                        @case('hu')
                            Sikeresen teljesítette a kvízt!
                            @break
                        @default
                            Успешно сте прошли квиз!
                    @endswitch
                </p>
            @else
                <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-red-600 mb-2">
                    @switch(app()->getLocale())
                        @case('en')
                            Keep Trying!
                            @break
                        @case('hu')
                            Próbálja újra!
                            @break
                        @default
                            Покушајте поново!
                    @endswitch
                </h1>
                <p class="text-lg text-gray-600">
                    @switch(app()->getLocale())
                        @case('en')
                            Don't give up, practice makes perfect!
                            @break
                        @case('hu')
                            Ne adja fel, a gyakorlás teszi a mestert!
                            @break
                        @default
                            Не одустајте, вежба чини мајстора!
                    @endswitch
                </p>
            @endif
        </div>

        <!-- Score Display -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    @switch(app()->getLocale())
                        @case('en')
                            Your Score
                            @break
                        @case('hu')
                            Az Ön eredménye
                            @break
                        @default
                            Ваш резултат
                    @endswitch
                </h3>
                <p class="text-3xl font-bold text-blue-600">{{ $attempt->score }}/{{ $quiz->questions->count() }}</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    @switch(app()->getLocale())
                        @case('en')
                            Percentage
                            @break
                        @case('hu')
                            Százalék
                            @break
                        @default
                            Проценат
                    @endswitch
                </h3>
                <p class="text-3xl font-bold text-purple-600">{{ $percentage }}%</p>
            </div>
            <div class="text-center p-4 {{ $passed ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    @switch(app()->getLocale())
                        @case('en')
                            Status
                            @break
                        @case('hu')
                            Státusz
                            @break
                        @default
                            Статус
                    @endswitch
                </h3>
                <p class="text-2xl font-bold {{ $passed ? 'text-green-600' : 'text-red-600' }}">
                    @if($passed)
                        @switch(app()->getLocale())
                            @case('en')
                                PASSED
                                @break
                            @case('hu')
                                SIKERES
                                @break
                            @default
                                ПРОШАО
                        @endswitch
                    @else
                        @switch(app()->getLocale())
                            @case('en')
                                FAILED
                                @break
                            @case('hu')
                                SIKERTELEN
                                @break
                            @default
                                ПОШАО
                        @endswitch
                    @endif
                </p>
            </div>
        </div>

        <!-- Quiz Info -->
        <div class="border-t border-gray-200 pt-6">
            <div class="text-center text-gray-600">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $quiz->getTitle() }}</h2>
                <p class="text-sm">
                    @switch(app()->getLocale())
                        @case('en')
                            Grade {{ $quiz->grade }} • {{ $quiz->subject->getName() }}
                            @break
                        @case('hu')
                            {{ $quiz->grade }}. osztály • {{ $quiz->subject->getName() }}
                            @break
                        @default
                            {{ $quiz->grade }}. разред • {{ $quiz->subject->getName() }}
                    @endswitch
                </p>
                <p class="text-sm mt-2">
                    @switch(app()->getLocale())
                        @case('en')
                            Player: {{ $attempt->getPlayerName() }} • Completed: {{ $attempt->finished_at->format('M j, Y H:i') }}
                            @break
                        @case('hu')
                            Játékos: {{ $attempt->getPlayerName() }} • Befejezve: {{ $attempt->finished_at->format('M j, Y H:i') }}
                            @break
                        @default
                            Играч: {{ $attempt->getPlayerName() }} • Завршено: {{ $attempt->finished_at->format('M j, Y H:i') }}
                    @endswitch
                </p>
            </div>
        </div>
    </div>

    <!-- Answer Review -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">
            @switch(app()->getLocale())
                @case('en')
                    Answer Review
                    @break
                @case('hu')
                    Válaszok áttekintése
                    @break
                @default
                    Преглед одговора
            @endswitch
        </h3>

        <div class="space-y-6">
            @foreach($answers as $index => $answerData)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">
                            @switch(app()->getLocale())
                                @case('en')
                                    Question {{ $index + 1 }}
                                    @break
                                @case('hu')
                                    {{ $index + 1 }}. kérdés
                                    @break
                                @default
                                    Питање {{ $index + 1 }}
                            @endswitch
                        </h4>
                        @if($answerData['is_correct'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                @switch(app()->getLocale())
                                    @case('en')
                                        Correct
                                        @break
                                    @case('hu')
                                        Helyes
                                        @break
                                    @default
                                        Тачно
                                @endswitch
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                @switch(app()->getLocale())
                                    @case('en')
                                        Incorrect
                                        @break
                                    @case('hu')
                                        Helytelen
                                        @break
                                    @default
                                        Нетачно
                                @endswitch
                            </span>
                        @endif
                    </div>

                    <p class="text-gray-800 mb-3">{{ $answerData['question']->getPrompt() }}</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Your Answer:
                                        @break
                                    @case('hu')
                                        Az Ön válasza:
                                        @break
                                    @default
                                        Ваш одговор:
                                @endswitch
                            </p>
                            <p class="{{ $answerData['is_correct'] ? 'text-green-700 bg-green-50' : 'text-red-700 bg-red-50' }} p-2 rounded border">
                                {{ $answerData['selected_answer']->getText() }}
                            </p>
                        </div>
                        @if(!$answerData['is_correct'])
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">
                                    @switch(app()->getLocale())
                                        @case('en')
                                            Correct Answer:
                                            @break
                                        @case('hu')
                                            Helyes válasz:
                                            @break
                                        @default
                                            Тачан одговор:
                                    @endswitch
                                </p>
                                <p class="text-green-700 bg-green-50 p-2 rounded border">
                                    {{ $answerData['correct_answer']->getText() }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if($answerData['question']->getExplanation())
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                            <h5 class="font-medium text-blue-900 mb-1">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Explanation:
                                        @break
                                    @case('hu')
                                        Magyarázat:
                                        @break
                                    @default
                                        Објашњење:
                                @endswitch
                            </h5>
                            <p class="text-blue-800 text-sm">{{ $answerData['question']->getExplanation() }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-center space-x-4">
        <button 
            wire:click="startNewQuiz"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
        >
            @switch(app()->getLocale())
                @case('en')
                    Take Another Quiz
                    @break
                @case('hu')
                    Új kvíz indítása
                    @break
                @default
                    Почни нови квиз
            @endswitch
        </button>
        
        <button 
            wire:click="viewDashboard"
            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
        >
            @if(auth()->check())
                @switch(app()->getLocale())
                    @case('en')
                        Go to Dashboard
                        @break
                    @case('hu')
                        Irányítópult
                        @break
                    @default
                        Иди на контролну таблу
                @endswitch
            @else
                @switch(app()->getLocale())
                    @case('en')
                        Back to Home
                        @break
                    @case('hu')
                        Vissza a főoldalra
                        @break
                    @default
                        Назад на почетну
                @endswitch
            @endif
        </button>
    </div>
</div>