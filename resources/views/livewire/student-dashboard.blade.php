<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            @switch(app()->getLocale())
                @case('en')
                    Student Dashboard
                    @break
                @case('hu')
                    Tanulói felület
                    @break
                @default
                    Ученичка табла
            @endswitch
        </h1>
        <div class="text-sm text-gray-600">
            @switch(app()->getLocale())
                @case('en')
                    Welcome, {{ auth()->user()->name }}
                    @break
                @case('hu')
                    Üdvözöljük, {{ auth()->user()->name }}
                    @break
                @default
                    Добродошли, {{ auth()->user()->name }}
            @endswitch
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                @switch(app()->getLocale())
                    @case('en')
                        Total Quizzes
                        @break
                    @case('hu')
                        Összes kvíz
                        @break
                    @default
                        Укупно квизова
                @endswitch
            </h3>
            <p class="text-3xl font-bold text-blue-600">{{ $totalAttempts }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                @switch(app()->getLocale())
                    @case('en')
                        Average Score
                        @break
                    @case('hu')
                        Átlagos eredmény
                        @break
                    @default
                        Просечан резултат
                @endswitch
            </h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($averageScore, 1) }}%</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                @switch(app()->getLocale())
                    @case('en')
                        Best Score
                        @break
                    @case('hu')
                        Legjobb eredmény
                        @break
                    @default
                        Најбољи резултат
                @endswitch
            </h3>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($bestScore, 1) }}%</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">
                @switch(app()->getLocale())
                    @case('en')
                        Quick Actions
                        @break
                    @case('hu')
                        Gyors műveletek
                        @break
                    @default
                        Брзе акције
                @endswitch
            </h3>
            <button 
                wire:click="startNewQuiz"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium"
            >
                @switch(app()->getLocale())
                    @case('en')
                        + Take New Quiz
                        @break
                    @case('hu')
                        + Új kvíz indítása
                        @break
                    @default
                        + Почни нови квиз
                @endswitch
            </button>
        </div>
    </div>

    <!-- Recent Activity -->
    @if($recentAttempts->count() > 0)
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    @switch(app()->getLocale())
                        @case('en')
                            Recent Activity
                            @break
                        @case('hu')
                            Legutóbbi tevékenységek
                            @break
                        @default
                            Недавне активности
                    @endswitch
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Quiz
                                        @break
                                    @case('hu')
                                        Kvíz
                                        @break
                                    @default
                                        Квиз
                                @endswitch
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Score
                                        @break
                                    @case('hu')
                                        Eredmény
                                        @break
                                    @default
                                        Резултат
                                @endswitch
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Date
                                        @break
                                    @case('hu')
                                        Dátum
                                        @break
                                    @default
                                        Датум
                                @endswitch
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentAttempts as $attempt)
                            @php
                                $totalQuestions = $attempt->quiz->questions->count();
                                $percentage = $totalQuestions > 0 ? round(($attempt->score / $totalQuestions) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div>
                                        <div class="font-medium">{{ $attempt->quiz->getTitle() }}</div>
                                        <div class="text-gray-500 text-xs">
                                            @switch(app()->getLocale())
                                                @case('en')
                                                    Grade {{ $attempt->quiz->grade }} • {{ $attempt->quiz->subject->getName() }}
                                                    @break
                                                @case('hu')
                                                    {{ $attempt->quiz->grade }}. osztály • {{ $attempt->quiz->subject->getName() }}
                                                    @break
                                                @default
                                                    {{ $attempt->quiz->grade }}. разред • {{ $attempt->quiz->subject->getName() }}
                                            @endswitch
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $percentage >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $attempt->score }}/{{ $totalQuestions }} ({{ $percentage }}%)
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $attempt->finished_at->format('M j, Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Quiz History -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">
                    @switch(app()->getLocale())
                        @case('en')
                            Quiz History
                            @break
                        @case('hu')
                            Kvíz történet
                            @break
                        @default
                            Историја квизова
                    @endswitch
                </h3>
            </div>

            <!-- Filters -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        @switch(app()->getLocale())
                            @case('en')
                                Search Quiz
                                @break
                            @case('hu')
                                Kvíz keresése
                                @break
                            @default
                                Претражи квиз
                        @endswitch
                    </label>
                    <input 
                        type="text" 
                        wire:model.live="searchTerm" 
                        placeholder="@switch(app()->getLocale()) @case('en') Enter quiz name... @break @case('hu') Írja be a kvíz nevét... @break @default Унесите назив квиза... @endswitch"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        @switch(app()->getLocale())
                            @case('en')
                                Subject
                                @break
                            @case('hu')
                                Tantárgy
                                @break
                            @default
                                Предмет
                        @endswitch
                    </label>
                    <select wire:model.live="selectedSubject" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="">
                            @switch(app()->getLocale())
                                @case('en')
                                    All Subjects
                                    @break
                                @case('hu')
                                    Minden tantárgy
                                    @break
                                @default
                                    Сви предмети
                            @endswitch
                        </option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->key }}">{{ $subject->getName() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        @switch(app()->getLocale())
                            @case('en')
                                Grade
                                @break
                            @case('hu')
                                Osztály
                                @break
                            @default
                                Разред
                        @endswitch
                    </label>
                    <select wire:model.live="selectedGrade" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="">
                            @switch(app()->getLocale())
                                @case('en')
                                    All Grades
                                    @break
                                @case('hu')
                                    Minden osztály
                                    @break
                                @default
                                    Сви разреди
                            @endswitch
                        </option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Grade {{ $i }}
                                        @break
                                    @case('hu')
                                        {{ $i }}. osztály
                                        @break
                                    @default
                                        {{ $i }}. разред
                                @endswitch
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        @if($attempts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Quiz
                                        @break
                                    @case('hu')
                                        Kvíz
                                        @break
                                    @default
                                        Квиз
                                @endswitch
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Score
                                        @break
                                    @case('hu')
                                        Eredmény
                                        @break
                                    @default
                                        Резултат
                                @endswitch
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Completed
                                        @break
                                    @case('hu')
                                        Befejezve
                                        @break
                                    @default
                                        Завршено
                                @endswitch
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @switch(app()->getLocale())
                                    @case('en')
                                        Actions
                                        @break
                                    @case('hu')
                                        Műveletek
                                        @break
                                    @default
                                        Акције
                                @endswitch
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attempts as $attempt)
                            @php
                                $totalQuestions = $attempt->quiz->questions->count();
                                $percentage = $totalQuestions > 0 ? round(($attempt->score / $totalQuestions) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div>
                                        <div class="font-medium">{{ $attempt->quiz->getTitle() }}</div>
                                        <div class="text-gray-500 text-xs">
                                            @switch(app()->getLocale())
                                                @case('en')
                                                    Grade {{ $attempt->quiz->grade }} • {{ $attempt->quiz->subject->getName() }}
                                                    @break
                                                @case('hu')
                                                    {{ $attempt->quiz->grade }}. osztály • {{ $attempt->quiz->subject->getName() }}
                                                    @break
                                                @default
                                                    {{ $attempt->quiz->grade }}. разред • {{ $attempt->quiz->subject->getName() }}
                                            @endswitch
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $percentage >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $attempt->score }}/{{ $totalQuestions }} ({{ $percentage }}%)
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $attempt->finished_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button 
                                        wire:click="viewResult({{ $attempt->quiz->id }}, {{ $attempt->id }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm"
                                    >
                                        @switch(app()->getLocale())
                                            @case('en')
                                                View Results
                                                @break
                                            @case('hu')
                                                Eredmények
                                                @break
                                            @default
                                                Прикажи резултат
                                        @endswitch
                                    </button>
                                    <button 
                                        wire:click="retakeQuiz({{ $attempt->quiz->id }})"
                                        class="text-green-600 hover:text-green-800 text-sm"
                                    >
                                        @switch(app()->getLocale())
                                            @case('en')
                                                Retake
                                                @break
                                            @case('hu')
                                                Újra
                                                @break
                                            @default
                                                Понови
                                        @endswitch
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4">
                {{ $attempts->links() }}
            </div>
        @else
            <div class="px-6 py-8 text-center">
                <p class="text-gray-500">
                    @switch(app()->getLocale())
                        @case('en')
                            No quiz attempts found.
                            @break
                        @case('hu')
                            Nem található kvíz kísérlet.
                            @break
                        @default
                            Нису пронађени покушаји квизова.
                    @endswitch
                </p>
                <button 
                    wire:click="startNewQuiz"
                    class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium"
                >
                    @switch(app()->getLocale())
                        @case('en')
                            Take Your First Quiz
                            @break
                        @case('hu')
                            Indítsa el az első kvízét
                            @break
                        @default
                            Урадите свој први квиз
                    @endswitch
                </button>
            </div>
        @endif
    </div>
</div>