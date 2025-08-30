<div class="max-w-2xl mx-auto px-4">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            @switch(app()->getLocale())
                @case('en')
                    Start Quiz
                    @break
                @case('hu')
                    Kvíz indítása
                    @break
                @default
                    Започни квиз
            @endswitch
        </h1>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <!-- Step 1: Grade Selection -->
        @if($step === 1)
            <h2 class="text-xl font-semibold mb-6 text-center">
                @switch(app()->getLocale())
                    @case('en')
                        Select Your Grade
                        @break
                    @case('hu')
                        Válaszd ki az osztályt
                        @break
                    @default
                        Изабери разред
                @endswitch
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button wire:click="selectGrade(0)"
                        class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition duration-200 text-center">
                    <div class="text-lg font-bold text-purple-600 mb-1">
                        @switch(app()->getLocale())
                            @case('en')
                                All Ages
                                @break
                            @case('hu')
                                Minden kor
                                @break
                            @default
                                Сви узрасти
                        @endswitch
                    </div>
                    <div class="text-xs text-gray-600">
                        @switch(app()->getLocale())
                            @case('en')
                                General
                                @break
                            @case('hu')
                                Általános
                                @break
                            @default
                                Опште
                        @endswitch
                    </div>
                </button>
                @for($grade = 1; $grade <= 8; $grade++)
                    <button wire:click="selectGrade({{ $grade }})"
                            class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition duration-200 text-center">
                        <div class="text-2xl font-bold text-blue-600 mb-1">{{ $grade }}</div>
                        <div class="text-sm text-gray-600">
                            @switch(app()->getLocale())
                                @case('en')
                                    Grade
                                    @break
                                @case('hu')
                                    osztály
                                    @break
                                @default
                                    разред
                            @endswitch
                        </div>
                    </button>
                @endfor
            </div>
        @endif

        <!-- Step 2: Subject Selection -->
        @if($step === 2)
            <div class="bg-blue-100 p-4 mb-4 rounded">
                <p>Debug Step 2: Selected Grade = {{ $selectedGrade }}</p>
                <p>Available Quizzes by Subject: {{ json_encode($availableQuizzes->keys()) }}</p>
            </div>
        @endif

        @if($step === 2)
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">
                    @switch(app()->getLocale())
                        @case('en')
                            Select Subject for Grade {{ $selectedGrade }}
                            @break
                        @case('hu')
                            Válaszd ki a tantárgyat - {{ $selectedGrade }}. osztály
                            @break
                        @default
                            Изабери предмет за {{ $selectedGrade }}. разред
                    @endswitch
                </h2>
                <button wire:click="back" class="text-blue-600 hover:text-blue-800">
                    @switch(app()->getLocale())
                        @case('en')
                            ← Back
                            @break
                        @case('hu')
                            ← Vissza
                            @break
                        @default
                            ← Назад
                    @endswitch
                </button>
            </div>

            <div class="space-y-3">
                @foreach($subjects as $subject)
                    @if($availableQuizzes->has($subject->id))
                        <button wire:click="selectSubject({{ $subject->id }})"
                                class="w-full p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition duration-200 text-left">
                            <div class="font-semibold text-gray-900">
                                {{ $subject->getName(app()->getLocale()) }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ $availableQuizzes->get($subject->id)->count() }}
                                @switch(app()->getLocale())
                                    @case('en')
                                        quiz(zes) available
                                        @break
                                    @case('hu')
                                        kvíz elérhető
                                        @break
                                    @default
                                        квиз(ова) доступно
                                @endswitch
                            </div>
                        </button>
                    @endif
                @endforeach
            </div>

            @if($availableQuizzes->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500">
                        @switch(app()->getLocale())
                            @case('en')
                                No quizzes available for this grade yet.
                                @break
                            @case('hu')
                                Még nincsenek kvízek elérhető ehhez az osztályhoz.
                                @break
                            @default
                                Нема доступних квизова за овај разред.
                        @endswitch
                    </p>
                </div>
            @endif
        @endif



        <!-- Step 3: Quiz Selection (when multiple quizzes available) -->
        @if($step === 3 && $quizChoices && count($quizChoices) > 0)
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">
                    @switch(app()->getLocale())
                        @case('en')
                            Choose Quiz
                            @break
                        @case('hu')
                            Kvíz választása
                            @break
                        @default
                            Изабери квиз
                    @endswitch
                </h2>
                <button wire:click="back" class="text-blue-600 hover:text-blue-800">
                    @switch(app()->getLocale())
                        @case('en')
                            ← Back
                            @break
                        @case('hu')
                            ← Vissza
                            @break
                        @default
                            ← Назад
                    @endswitch
                </button>
            </div>

            <div class="space-y-3">
                @foreach($quizChoices as $quiz)
                    <button wire:click="selectQuiz({{ $quiz->id }})"
                            class="w-full p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition duration-200 text-left">
                        <div class="font-semibold text-gray-900">
                            {{ $quiz->getTitle() }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            {{ $quiz->getDescription() }}
                        </div>
                        <div class="text-xs text-gray-400 mt-2">
                            {{ $quiz->questions()->count() }}
                            @switch(app()->getLocale())
                                @case('en')
                                    questions (20 random will be selected)
                                    @break
                                @case('hu')
                                    kérdés (20 véletlenszerű lesz kiválasztva)
                                    @break
                                @default
                                    питања (20 насумичних ће бити изабрано)
                            @endswitch
                        </div>
                    </button>
                @endforeach
            </div>
        @endif

        <!-- Step 4: Guest Name Input -->
        @if($step === 4 || ($step === 3 && (empty($quizChoices) || count($quizChoices) <= 1)))
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">
                    @switch(app()->getLocale())
                        @case('en')
                            Enter Your Name
                            @break
                        @case('hu')
                            Add meg a nevedet
                            @break
                        @default
                            Унеси своје име
                    @endswitch
                </h2>
                <button wire:click="back" class="text-blue-600 hover:text-blue-800">
                    @switch(app()->getLocale())
                        @case('en')
                            ← Back
                            @break
                        @case('hu')
                            ← Vissza
                            @break
                        @default
                            ← Назад
                    @endswitch
                </button>
            </div>

            <form wire:submit.prevent="startQuiz">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        @switch(app()->getLocale())
                            @case('en')
                                Your Name
                                @break
                            @case('hu')
                                Neved
                                @break
                            @default
                                Твоје име
                        @endswitch
                    </label>
                    <input type="text"
                           wire:model="guestName"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="@switch(app()->getLocale())
                               @case('en')
                                   Enter your name...
                                   @break
                               @case('hu')
                                   Add meg a nevedet...
                                   @break
                               @default
                                   Унеси своје име...
                           @endswitch">
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    @switch(app()->getLocale())
                        @case('en')
                            Start Quiz
                            @break
                        @case('hu')
                            Kvíz indítása
                            @break
                        @default
                            Започни квиз
                    @endswitch
                </button>
            </form>
        @endif

        @if(session('error'))
            <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>
