<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Quiz Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $quiz->getTitle() }}</h1>
                <p class="text-sm text-gray-600">
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
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-blue-600">{{ $questionNumber }}/{{ $totalQuestions }}</div>
                <div class="text-sm text-gray-600">
                    @switch(app()->getLocale())
                        @case('en')
                            Questions
                            @break
                        @case('hu')
                            Kérdések
                            @break
                        @default
                            Питања
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
        </div>

        <div class="text-sm text-gray-600">
            @switch(app()->getLocale())
                @case('en')
                    Player: {{ $attempt->getPlayerName() }}
                    @break
                @case('hu')
                    Játékos: {{ $attempt->getPlayerName() }}
                    @break
                @default
                    Играч: {{ $attempt->getPlayerName() }}
            @endswitch
        </div>
    </div>

    <!-- Question Card -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                @switch(app()->getLocale())
                    @case('en')
                        Question {{ $questionNumber }}
                        @break
                    @case('hu')
                        {{ $questionNumber }}. kérdés
                        @break
                    @default
                        Питање {{ $questionNumber }}
                @endswitch
            </h2>
            <p class="text-lg text-gray-800 leading-relaxed">
                {{ $currentQuestion->getPrompt() }}
            </p>
        </div>

        <!-- Answer Options -->
        <div class="space-y-3 mb-8">
            @foreach($currentQuestion->answers as $answer)
                <button 
                    wire:click="selectAnswer({{ $answer->id }})"
                    @class([
                        'w-full p-4 text-left border-2 rounded-lg transition-all duration-200',
                        'border-blue-500 bg-blue-50' => $selectedAnswer === $answer->id && !$isAnswered,
                        'border-green-500 bg-green-50 text-green-800' => $isAnswered && $answer->is_correct,
                        'border-red-500 bg-red-50 text-red-800' => $isAnswered && $selectedAnswer === $answer->id && !$answer->is_correct,
                        'border-gray-200 hover:border-gray-300 hover:bg-gray-50' => $selectedAnswer !== $answer->id && !$isAnswered,
                        'border-gray-200 bg-gray-50' => $isAnswered && $selectedAnswer !== $answer->id && !$answer->is_correct,
                        'cursor-not-allowed opacity-60' => $isAnswered,
                        'cursor-pointer' => !$isAnswered,
                    ])
                    @if($isAnswered) disabled @endif
                >
                    <div class="flex items-center">
                        <span class="flex-shrink-0 w-8 h-8 rounded-full border-2 border-current flex items-center justify-center mr-3 text-sm font-medium">
                            {{ chr(65 + $loop->index) }}
                        </span>
                        <span class="flex-1">{{ $answer->getText() }}</span>
                        
                        @if($isAnswered && $answer->is_correct)
                            <svg class="w-6 h-6 text-green-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @elseif($isAnswered && $selectedAnswer === $answer->id && !$answer->is_correct)
                            <svg class="w-6 h-6 text-red-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </button>
            @endforeach
        </div>

        <!-- Answer Explanation -->
        @if($showExplanation && $currentQuestion->getExplanation())
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-blue-900 mb-2">
                    @switch(app()->getLocale())
                        @case('en')
                            Explanation
                            @break
                        @case('hu')
                            Magyarázat
                            @break
                        @default
                            Објашњење
                    @endswitch
                </h4>
                <p class="text-blue-800">{{ $currentQuestion->getExplanation() }}</p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            @if(!$isAnswered)
                <button 
                    wire:click="submitAnswer"
                    @disabled(!$selectedAnswer)
                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
                >
                    @switch(app()->getLocale())
                        @case('en')
                            Submit Answer
                            @break
                        @case('hu')
                            Válasz küldése
                            @break
                        @default
                            Пошаљи одговор
                    @endswitch
                </button>
            @else
                @if($questionNumber < $totalQuestions)
                    <button 
                        wire:click="nextQuestion"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
                    >
                        @switch(app()->getLocale())
                            @case('en')
                                Next Question →
                                @break
                            @case('hu')
                                Következő kérdés →
                                @break
                            @default
                                Следеће питање →
                        @endswitch
                    </button>
                @else
                    <button 
                        wire:click="completeQuiz"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
                    >
                        @switch(app()->getLocale())
                            @case('en')
                                View Results →
                                @break
                            @case('hu')
                                Eredmények megtekintése →
                                @break
                            @default
                                Прикажи резултате →
                        @endswitch
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Keyboard Navigation Hint -->
    <div class="text-center text-sm text-gray-500">
        @if(!$isAnswered)
            @switch(app()->getLocale())
                @case('en')
                    💡 Tip: You can use keys A, B, C, D to select answers
                    @break
                @case('hu')
                    💡 Tipp: Az A, B, C, D billentyűkkel is kiválaszthatod a válaszokat
                    @break
                @default
                    💡 Савет: Можеш користити тастере A, B, C, D за избор одговора
            @endswitch
        @endif
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName.toLowerCase() === 'input' || e.target.tagName.toLowerCase() === 'textarea') {
            return;
        }
        
        const key = e.key.toLowerCase();
        const answers = @js($currentQuestion->answers->pluck('id'));
        
        if (['a', 'b', 'c', 'd'].includes(key)) {
            const index = key.charCodeAt(0) - 97; // Convert a-d to 0-3
            if (answers[index]) {
                @this.selectAnswer(answers[index]);
            }
        } else if (key === 'enter') {
            if (!@js($isAnswered) && @js($selectedAnswer)) {
                @this.submitAnswer();
            } else if (@js($isAnswered)) {
                @this.nextQuestion();
            }
        }
    });

    // Auto-advance functionality
    Livewire.on('auto-advance', () => {
        setTimeout(() => {
            @this.nextQuestion();
        }, 3000);
    });
});
</script>