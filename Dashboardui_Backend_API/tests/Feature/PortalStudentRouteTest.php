<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalStudentRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_portal_renders_localized_title(): void
    {
        $response = $this->get('/en/student');

        $response->assertOk();
        $response->assertSee('Student portal', false);
    }

    public function test_teacher_portal_renders(): void
    {
        $response = $this->get('/fr/teacher');

        $response->assertOk();
        $response->assertSee('Portail enseignant', false);
    }
}
