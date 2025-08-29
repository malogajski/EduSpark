<?php

namespace App\Livewire;

use App\Models\Subject;
use Livewire\Component;

class WelcomePage extends Component
{
    public function render()
    {
        $subjects = Subject::all();
        
        return view('livewire.welcome-page', [
            'subjects' => $subjects
        ])->layout('components.layouts.app', [
            'title' => 'Welcome to EduSpark'
        ]);
    }
}