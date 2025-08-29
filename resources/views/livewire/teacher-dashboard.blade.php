<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Teacher Dashboard</h1>
        <div class="text-sm text-gray-600">
            Welcome, {{ auth()->user()->name }}
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <button wire:click="setTab('overview')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Overview
                </button>
                <button wire:click="setTab('quizzes')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'quizzes' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Quizzes
                </button>
                <button wire:click="setTab('attempts')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'attempts' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Quiz Attempts
                </button>
            </nav>
        </div>
    </div>

    <!-- Overview Tab -->
    @if($activeTab === 'overview')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Quizzes</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $totalQuizzes }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Attempts</h3>
                <p class="text-3xl font-bold text-green-600">{{ $totalAttempts }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Average Score</h3>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($avgScore, 1) }}%</p>
            </div>
        </div>

        @if($recentAttempts->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Quiz Attempts</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Player</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentAttempts as $attempt)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attempt->getPlayerName() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Grade {{ $attempt->quiz->grade }} - {{ $attempt->quiz->getTitle() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $attempt->score >= $attempt->total * 0.7 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $attempt->score }}/{{ $attempt->total }}
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
    @endif

    <!-- Quizzes Tab -->
    @if($activeTab === 'quizzes')
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Manage Quizzes</h3>
                    <button wire:click="showCreateQuizForm"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        + Create New Quiz
                    </button>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Grade</label>
                        <select wire:model.live="selectedGrade" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">All Grades</option>
                            <option value="0">All Ages</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">Grade {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Subject</label>
                        <select wire:model.live="selectedSubject" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->getName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        @if($selectedGrade || $selectedSubject)
                            <button wire:click="$set('selectedGrade', ''); $set('selectedSubject', '')"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Clear Filters
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            @if($showCreateQuiz)
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">
                        @if($editingQuiz)
                            Edit Quiz
                        @else
                            Create New Quiz
                        @endif
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                            <select wire:model="newQuiz.grade" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}">Grade {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <select wire:model="newQuiz.subject_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->getName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title (Serbian)</label>
                            <input type="text" wire:model="newQuiz.title.sr" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description (Serbian)</label>
                            <textarea wire:model="newQuiz.description.sr" class="w-full border border-gray-300 rounded-md px-3 py-2" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="flex space-x-3 mt-4">
                        @if($editingQuiz)
                            <button wire:click="updateQuiz"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                Update Quiz
                            </button>
                        @else
                            <button wire:click="createQuiz"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                Create Quiz
                            </button>
                        @endif
                        <button wire:click="cancelEdit"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Cancel
                        </button>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Questions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($quizzes as $quiz)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $quiz->getTitle() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($quiz->grade === 0)
                                        All Ages
                                    @else
                                        {{ $quiz->grade }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $quiz->subject->getName() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $quiz->questions()->count() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button wire:click="editQuiz({{ $quiz->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                        Edit
                                    </button>
                                    <a href="{{ route('teacher.quiz.edit', $quiz->id) }}"
                                       class="text-green-600 hover:text-green-800 text-sm">
                                        Questions
                                    </a>
                                    <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                            wire:click="deleteQuiz({{ $quiz->id }})"
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $quizzes->links() }}
            </div>
        </div>
    @endif

    <!-- Attempts Tab -->
    @if($activeTab === 'attempts')
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quiz Attempts</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Player</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attempts as $attempt)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $attempt->getPlayerName() }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    Grade {{ $attempt->quiz->grade }} - {{ $attempt->quiz->getTitle() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($attempt->finished_at)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $attempt->score >= $attempt->total * 0.7 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $attempt->score }}/{{ $attempt->total }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">In progress</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $attempt->finished_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $attempt->finished_at ? 'Completed' : 'In Progress' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $attempt->started_at->format('M j, Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $attempts->links() }}
            </div>
        </div>
    @endif
</div>
