<?php

namespace Tests\Feature\HR;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\User\Mail\EmployeeAdminAccessMail;
use Modules\User\Mail\EmployeeHiredMail;
use Modules\User\Models\Candidate;
use Modules\User\Models\Employee;
use Modules\User\Models\JobApplication;
use Modules\User\Models\JobPosition;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class EmployeeHireAndAdminConversionTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    public function test_employee_can_be_created_with_optional_position_and_resume(): void
    {
        Storage::fake('public');

        $admin = $this->createAdminWithPermissions(['hr.employees.create']);
        $resume = UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('admin.employees.store'), [
            'name' => 'Sara Nader',
            'email' => 'sara.nader@example.com',
            'mobile' => '01012345678',
            'position' => 'Backend Developer',
            'resume' => $resume,
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseHas('employees', [
            'email' => 'sara.nader@example.com',
            'position' => 'Backend Developer',
        ]);

        $employee = Employee::query()->where('email', 'sara.nader@example.com')->first();
        $this->assertNotNull($employee?->resume);
        Storage::disk('public')->assertExists($employee->resume);
    }

    public function test_employee_can_be_converted_to_admin_with_password_and_permissions(): void
    {
        Mail::fake();

        $actor = $this->createAdminWithPermissions(['hr.admins.create']);
        $employee = Employee::factory()->create([
            'name' => 'Hadi Staff',
            'email' => 'hadi.staff@example.com',
            'mobile' => '01098765432',
        ]);

        $response = $this->actingAs($actor)->post(route('admin.employees.convert-to-admin', $employee), [
            'password' => 'secret123',
            'permissions' => ['hr.employees.view', 'overview.dashboard.view'],
        ]);

        $response->assertRedirect(route('admin.employees.show', $employee));

        $employee->refresh();
        $this->assertNotNull($employee->user_id);

        $user = User::query()->find($employee->user_id);
        $this->assertSame(User::TYPE_ADMIN, $user->type);
        $this->assertTrue(Hash::check('secret123', $user->password));
        $this->assertTrue($user->can('hr.employees.view'));
        $this->assertTrue($user->can('overview.dashboard.view'));

        Mail::assertSent(EmployeeAdminAccessMail::class, function (EmployeeAdminAccessMail $mail) use ($employee) {
            return $mail->hasTo($employee->email)
                && $mail->plainPassword === 'secret123'
                && $mail->employee->is($employee);
        });
    }

    public function test_job_application_hire_creates_employee_from_candidate_data(): void
    {
        Mail::fake();
        Storage::fake('public');
        Storage::disk('public')->put('resumes/candidate.pdf', 'pdf-content');

        $admin = $this->createAdminWithPermissions(['hr.employees.create']);
        $position = JobPosition::factory()->create(['title' => 'Product Designer']);
        $candidate = Candidate::factory()->create([
            'full_name' => 'Lina Hire',
            'email' => 'lina.hire@example.com',
            'phone' => '01055556666',
            'resume_path' => 'resumes/candidate.pdf',
        ]);
        $application = JobApplication::factory()->create([
            'candidate_id' => $candidate->id,
            'job_position_id' => $position->id,
            'status' => JobApplication::STATUS_INTERVIEW,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.job-applications.hire', $application));

        $employee = Employee::query()->where('email', 'lina.hire@example.com')->first();
        $this->assertNotNull($employee);
        $response->assertRedirect(route('admin.employees.show', $employee));
        $this->assertSame('Product Designer', $employee->position);
        $this->assertSame('01055556666', (string) $employee->mobile);
        $this->assertNotSame('resumes/candidate.pdf', $employee->resume);
        Storage::disk('public')->assertExists($employee->resume);

        $application->refresh();
        $this->assertSame(JobApplication::STATUS_HIRED, $application->status);
        $this->assertSame($employee->id, $application->employee_id);

        Mail::assertSent(EmployeeHiredMail::class, function (EmployeeHiredMail $mail) use ($employee) {
            return $mail->hasTo($employee->email) && $mail->employee->is($employee);
        });
    }

    public function test_employee_can_be_published_to_our_team_on_the_website(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('employees/resumes/cv.pdf', 'pdf-content');

        $admin = $this->createAdminWithPermissions(['cms.team.create']);
        $employee = Employee::factory()->create([
            'name' => 'Rami Team',
            'email' => 'rami.team@example.com',
            'mobile' => '01077778888',
            'position' => 'Product Designer',
            'resume' => 'employees/resumes/cv.pdf',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.employees.add-to-team', $employee));

        $response->assertRedirect(route('admin.employees.show', $employee));
        $this->assertDatabaseHas('teams', [
            'employee_id' => $employee->id,
            'status' => 'Published',
        ]);

        $employee->refresh()->load('team');
        $this->assertNotNull($employee->team);
        $this->assertSame('Rami Team', $employee->team->getTranslation('name', app()->getLocale()));
        $this->assertSame('Product Designer', $employee->team->getTranslation('position', app()->getLocale()));
        $this->assertNotSame('employees/resumes/cv.pdf', $employee->team->resume);
        Storage::disk('public')->assertExists($employee->team->resume);
    }
}
