<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Quiz</h1>
            <p class="text-gray-600 mt-1">{{ $quiz->getTitle() }} - Grade {{ $quiz->grade }}</p>
        </div>
        <a href="/teacher" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            ← Back to Dashboard
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Questions List -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">
                Questions ({{ count($questions) }})
            </h3>
            <button wire:click="showAddQuestionForm" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                + Add Question
            </button>
        </div>

        <!-- Add/Edit Question Form -->
        @if($showAddQuestion)
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h4 class="text-md font-semibold text-gray-900 mb-4">
                    @if($editingQuestion)
                        Edit Question
                    @else
                        Add New Question
                    @endif
                </h4>
                
                <div class="space-y-4">
                    <!-- Question Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                        <select wire:model.live="newQuestion.question_type" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="single_choice">Single Choice (Multiple options, one correct)</option>
                            <option value="multiple_choice">Multiple Choice (Multiple options, multiple correct)</option>
                            <option value="true_false">True/False</option>
                        </select>
                    </div>

                    <!-- Question Prompt -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Question (Serbian)</label>
                        <textarea wire:model="newQuestion.prompt.sr" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2" 
                                  rows="3" 
                                  placeholder="Enter your question..."></textarea>
                    </div>

                    <!-- Explanation (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional, Serbian)</label>
                        <textarea wire:model="newQuestion.explanation.sr" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2" 
                                  rows="2" 
                                  placeholder="Explain why this is the correct answer..."></textarea>
                    </div>

                    <!-- Answers -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-medium text-gray-700">Answer Options</label>
                            @if($newQuestion['question_type'] !== 'true_false')
                                <button wire:click="addAnswer" 
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                    + Add Answer
                                </button>
                            @endif
                        </div>
                        
                        <div class="space-y-3">
                            @foreach($newQuestion['answers'] as $index => $answer)
                                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg">
                                    <!-- Correct Answer Checkbox/Radio -->
                                    <div class="flex-shrink-0">
                                        @if($newQuestion['question_type'] === 'multiple_choice')
                                            <input type="checkbox" 
                                                   wire:model="newQuestion.answers.{{ $index }}.is_correct"
                                                   class="w-4 h-4 text-blue-600">
                                        @else
                                            <input type="radio" 
                                                   name="correct_answer"
                                                   wire:click="$set('newQuestion.answers.{{ $index }}.is_correct', true)"
                                                   @if($answer['is_correct']) checked @endif
                                                   class="w-4 h-4 text-blue-600">
                                        @endif
                                    </div>
                                    
                                    <!-- Answer Text -->
                                    <div class="flex-1">
                                        @if($newQuestion['question_type'] === 'true_false')
                                            <input type="text" 
                                                   value="{{ $answer['text']['sr'] }}"
                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100" 
                                                   readonly>
                                        @else
                                            <input type="text" 
                                                   wire:model="newQuestion.answers.{{ $index }}.text.sr"
                                                   class="w-full border border-gray-300 rounded-md px-3 py-2" 
                                                   placeholder="Answer option {{ $index + 1 }}">
                                        @endif
                                    </div>
                                    
                                    <!-- Remove Button -->
                                    @if($newQuestion['question_type'] !== 'true_false' && count($newQuestion['answers']) > 2)
                                        <button wire:click="removeAnswer({{ $index }})"
                                                class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                <!-- Handle radio button exclusivity for single choice and true/false -->
                                @if(($newQuestion['question_type'] === 'single_choice' || $newQuestion['question_type'] === 'true_false') && $answer['is_correct'])
                                    @foreach($newQuestion['answers'] as $otherIndex => $otherAnswer)
                                        @if($otherIndex !== $index && $otherAnswer['is_correct'])
                                            @php $this->newQuestion['answers'][$otherIndex]['is_correct'] = false; @endphp
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-2">
                            @if($newQuestion['question_type'] === 'single_choice')
                                Select exactly one correct answer.
                            @elseif($newQuestion['question_type'] === 'multiple_choice')
                                Select one or more correct answers.
                            @else
                                Select either True or False.
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex space-x-3 mt-6">
                    <button wire:click="saveQuestion" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        @if($editingQuestion)
                            Update Question
                        @else
                            Add Question
                        @endif
                    </button>
                    <button wire:click="cancelQuestion" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Cancel
                    </button>
                </div>
            </div>
        @endif

        <!-- Questions List -->
        @if(count($questions) > 0)
            <div class="divide-y divide-gray-200">
                @foreach($questions as $index => $question)
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="font-semibold text-gray-900 mr-3">
                                        Question {{ $index + 1 }}
                                    </h4>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if($question['question_type'] === 'single_choice') bg-blue-100 text-blue-800
                                        @elseif($question['question_type'] === 'multiple_choice') bg-green-100 text-green-800  
                                        @else bg-purple-100 text-purple-800 @endif">
                                        @if($question['question_type'] === 'single_choice') Single Choice
                                        @elseif($question['question_type'] === 'multiple_choice') Multiple Choice
                                        @else True/False @endif
                                    </span>
                                </div>
                                
                                <p class="text-gray-800 mb-3">{{ $question['prompt']['sr'] ?? 'No prompt' }}</p>
                                
                                @if(isset($question['answers']) && count($question['answers']) > 0)
                                    <div class="space-y-1">
                                        @foreach($question['answers'] as $answer)
                                            <div class="flex items-center text-sm">
                                                @if($answer['is_correct'])
                                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-green-700 font-medium">{{ $answer['text']['sr'] ?? 'No text' }}</span>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <circle cx="10" cy="10" r="3"></circle>
                                                    </svg>
                                                    <span class="text-gray-600">{{ $answer['text']['sr'] ?? 'No text' }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if(isset($question['explanation']['sr']) && !empty($question['explanation']['sr']))
                                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                        <p class="text-sm text-blue-800">
                                            <strong>Explanation:</strong> {{ $question['explanation']['sr'] }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex space-x-2 ml-4">
                                <button wire:click="editQuestion({{ $question['id'] }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                    Edit
                                </button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" 
                                        wire:click="deleteQuestion({{ $question['id'] }})"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-8 text-center">
                <p class="text-gray-500 mb-4">No questions added yet.</p>
                <button wire:click="showAddQuestionForm" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Add Your First Question
                </button>
            </div>
        @endif
    </div>
</div>