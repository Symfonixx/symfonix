<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string('img')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        $userToEmployee = $this->migrateEmployeeUsers();

        $this->repointAssignedTo($userToEmployee);
        $this->repointUserForeignKeys($userToEmployee);

        DB::table('users')->where('type', 'employee')->delete();

        if (Schema::hasColumn('users', 'type')) {
            DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('customer', 'admin') NOT NULL DEFAULT 'customer'");
        }
    }

    public function down(): void
    {
        $this->restoreUserForeignKeys();
        $this->restoreAssignedTo();

        Schema::dropIfExists('employees');

        if (Schema::hasColumn('users', 'type')) {
            DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('customer', 'employee', 'admin') NOT NULL DEFAULT 'customer'");
        }
    }

    /**
     * @return array<int, int> old user id => new employee id
     */
    private function migrateEmployeeUsers(): array
    {
        $mapping = [];

        if (! Schema::hasTable('users')) {
            return $mapping;
        }

        $employeeUsers = DB::table('users')
            ->where('type', 'employee')
            ->orderBy('id')
            ->get();

        foreach ($employeeUsers as $user) {
            $employeeId = DB::table('employees')->insertGetId([
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'img' => $user->img,
                'status' => 'active',
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);

            $mapping[(int) $user->id] = $employeeId;
        }

        return $mapping;
    }

    /**
     * @param  array<int, int>  $userToEmployee
     */
    private function repointAssignedTo(array $userToEmployee): void
    {
        foreach (['deals', 'leads'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'assigned_to')) {
                continue;
            }

            if ($this->foreignKeyTargets($table, 'assigned_to', 'users')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropForeign(['assigned_to']);
                });
            }

            foreach ($userToEmployee as $userId => $employeeId) {
                DB::table($table)->where('assigned_to', $userId)->update(['assigned_to' => $employeeId]);
            }

            DB::table($table)
                ->whereNotNull('assigned_to')
                ->whereNotIn('assigned_to', array_values($userToEmployee))
                ->update(['assigned_to' => null]);

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreign('assigned_to')->references('id')->on('employees')->nullOnDelete();
            });
        }
    }

    private function restoreAssignedTo(): void
    {
        foreach (['deals', 'leads'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'assigned_to')) {
                continue;
            }

            if ($this->foreignKeyTargets($table, 'assigned_to', 'employees')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropForeign(['assigned_to']);
                });
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * @param  array<int, int>  $userToEmployee
     */
    private function repointUserForeignKeys(array $userToEmployee): void
    {
        $tables = [
            'salaries' => ['user_id', 'employee_id'],
            'commissions' => ['user_id', 'employee_id'],
            'crm_sales_targets' => ['user_id', 'employee_id'],
        ];

        foreach ($tables as $table => [$oldColumn, $newColumn]) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $oldColumn)) {
                continue;
            }

            if ($this->foreignKeyTargets($table, $oldColumn, 'users')) {
                Schema::table($table, function (Blueprint $blueprint) use ($oldColumn) {
                    $blueprint->dropForeign([$oldColumn]);
                });
            }

            Schema::table($table, function (Blueprint $blueprint) use ($newColumn) {
                $blueprint->unsignedBigInteger($newColumn)->nullable()->after('id');
            });

            foreach ($userToEmployee as $userId => $employeeId) {
                DB::table($table)->where($oldColumn, $userId)->update([$newColumn => $employeeId]);
            }

            DB::table($table)->whereNull($newColumn)->delete();

            Schema::table($table, function (Blueprint $blueprint) use ($oldColumn) {
                $blueprint->dropColumn($oldColumn);
            });

            DB::statement("ALTER TABLE `{$table}` MODIFY `{$newColumn}` BIGINT UNSIGNED NOT NULL");

            Schema::table($table, function (Blueprint $blueprint) use ($newColumn, $table) {
                $blueprint->foreign($newColumn)->references('id')->on('employees')->cascadeOnDelete();

                if ($table === 'crm_sales_targets') {
                    $blueprint->unique($newColumn);
                }
            });
        }
    }

    private function restoreUserForeignKeys(): void
    {
        $tables = [
            'salaries' => 'employee_id',
            'commissions' => 'employee_id',
            'crm_sales_targets' => 'employee_id',
        ];

        foreach ($tables as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            if ($this->foreignKeyTargets($table, $column, 'employees')) {
                Schema::table($table, function (Blueprint $blueprint) use ($column) {
                    $blueprint->dropForeign([$column]);
                });
            }

            Schema::table($table, function (Blueprint $blueprint) use ($column) {
                $blueprint->renameColumn($column, 'user_id');
            });

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    private function foreignKeyTargets(string $table, string $column, string $referencedTable): bool
    {
        $database = Schema::getConnection()->getDatabaseName();

        $constraint = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->where('REFERENCED_TABLE_NAME', $referencedTable)
            ->value('CONSTRAINT_NAME');

        return $constraint !== null;
    }
};
