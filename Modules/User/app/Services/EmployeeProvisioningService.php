<?php

namespace Modules\User\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Cms\Enums\CmsStatus;
use Modules\Team\Models\Team;
use Modules\User\Models\Employee;
use Modules\User\Models\JobApplication;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class EmployeeProvisioningService
{
    public function hireFromApplication(JobApplication $application): Employee
    {
        $application->loadMissing(['candidate', 'position', 'employee']);

        if ($application->employee) {
            return $application->employee;
        }

        $candidate = $application->candidate;
        $email = strtolower(trim((string) $candidate->email));
        $mobile = $this->normalizeMobile($candidate->phone);

        if ($mobile === null) {
            throw ValidationException::withMessages([
                'mobile' => __('The candidate phone number is invalid. Update it before hiring.'),
            ]);
        }

        return DB::transaction(function () use ($application, $candidate, $email, $mobile) {
            $employee = Employee::query()->where('email', $email)->first();

            if (! $employee) {
                $mobileOwner = Employee::query()->where('mobile', $mobile)->first();
                if ($mobileOwner) {
                    throw ValidationException::withMessages([
                        'mobile' => __('An employee with this mobile number already exists.'),
                    ]);
                }

                $employee = Employee::query()->create([
                    'name' => $candidate->full_name,
                    'email' => $email,
                    'mobile' => $mobile,
                    'position' => $application->position?->title,
                    'resume' => $this->copyResume($candidate->resume_path),
                    'status' => Employee::STATUS_ACTIVE,
                ]);
            }

            $application->update([
                'employee_id' => $employee->id,
                'status' => JobApplication::STATUS_HIRED,
            ]);

            return $employee;
        });
    }

    /**
     * @param  list<string>  $permissions
     */
    public function convertToAdmin(Employee $employee, string $password, array $permissions = []): User
    {
        if ($employee->user_id) {
            throw ValidationException::withMessages([
                'email' => __('This employee already has an admin login.'),
            ]);
        }

        return DB::transaction(function () use ($employee, $password, $permissions) {
            $user = User::query()->where('email', $employee->email)->first();

            if ($user && $user->employee && $user->employee->isNot($employee)) {
                throw ValidationException::withMessages([
                    'email' => __('This email is already linked to another employee.'),
                ]);
            }

            $mobile = (string) $employee->mobile;
            $mobileTaken = User::query()
                ->where('mobile', $mobile)
                ->when($user, fn ($query) => $query->where('id', '!=', $user->id))
                ->exists();

            if ($user) {
                $payload = [
                    'name' => $employee->name,
                    'password' => $password,
                    'type' => User::TYPE_ADMIN,
                ];

                if (! $mobileTaken) {
                    $payload['mobile'] = $mobile;
                }

                $user->update($payload);
            } else {
                if ($mobileTaken) {
                    throw ValidationException::withMessages([
                        'mobile' => __('An account with this mobile number already exists.'),
                    ]);
                }

                $user = User::query()->create([
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'mobile' => $mobile,
                    'password' => $password,
                    'type' => User::TYPE_ADMIN,
                ]);
            }

            $this->syncDirectPermissions($user, $permissions);
            $employee->update(['user_id' => $user->id]);

            return $user;
        });
    }

    public function addToWebsiteTeam(Employee $employee): Team
    {
        $employee->loadMissing('team');

        if ($employee->team) {
            return $employee->team;
        }

        $position = filled($employee->position) ? (string) $employee->position : __('Team Member');
        $translations = buildTranslations([
            'name' => $employee->name,
            'position' => $position,
        ], true);

        return DB::transaction(function () use ($employee, $translations) {
            return Team::query()->create([
                'name' => $translations['name'],
                'position' => $translations['position'],
                'avatar' => $this->copyPublicFile($employee->img, 'teams') ?? '',
                'resume' => $this->copyPublicFile($employee->resume, 'teams/resumes'),
                'status' => CmsStatus::PUBLISHED->value,
                'employee_id' => $employee->id,
            ]);
        });
    }

    /**
     * @param  list<string>  $permissions
     */
    private function syncDirectPermissions(User $user, array $permissions): void
    {
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $user->syncPermissions($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function normalizeMobile(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone) ?? '';

        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return null;
        }

        return $digits;
    }

    private function copyResume(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return $path;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $destination = 'employees/resumes/'.Str::uuid().($extension !== '' ? '.'.$extension : '');
        Storage::disk('public')->copy($path, $destination);

        return $destination;
    }

    private function copyPublicFile(?string $path, string $directory): ?string
    {
        if (! $path) {
            return null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return $path;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $destination = trim($directory, '/').'/'.Str::uuid().($extension !== '' ? '.'.$extension : '');
        Storage::disk('public')->copy($path, $destination);

        return $destination;
    }
}
