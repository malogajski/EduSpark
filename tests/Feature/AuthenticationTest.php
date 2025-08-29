<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_access_teacher_dashboard()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher);

        $response = $this->get('/teacher');
        $response->assertStatus(200);
        $response->assertSeeLivewire('teacher-dashboard');
    }

    public function test_student_cannot_access_teacher_dashboard()
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student);

        $response = $this->get('/teacher');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_teacher_dashboard()
    {
        $response = $this->get('/teacher');
        $response->assertRedirect(route('login'));
    }

    public function test_student_can_access_student_dashboard()
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student);

        $response = $this->get('/student');
        $response->assertStatus(200);
        $response->assertSeeLivewire('student-dashboard');
    }

    public function test_teacher_cannot_access_student_dashboard()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher);

        $response = $this->get('/student');
        $response->assertStatus(403);
    }

    public function test_dashboard_redirects_based_on_role()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        // Test teacher redirect
        $this->actingAs($teacher);
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('teacher.dashboard'));

        // Test student redirect
        $this->actingAs($student);
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_login_redirects_to_appropriate_dashboard()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        // Test teacher login
        $response = $this->post('/login', [
            'email' => $teacher->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('dashboard'));

        // Test student login
        $this->post('/logout');
        $response = $this->post('/login', [
            'email' => $student->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_registration_creates_student_by_default()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'student',
        ]);
    }
}