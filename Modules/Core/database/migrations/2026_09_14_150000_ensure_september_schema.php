<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bridge for existing deployed databases after September alter migrations
 * were folded into create migrations. Safe no-op on fresh installs.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->ensureEmployees();
        $this->ensureTeams();
        $this->ensureLeads();
        $this->ensureTaxColumns('invoice_lines', withPercent: true, withAmount: true);
        $this->ensureTaxColumns('journal_entries', withPercent: false, withAmount: true);
        $this->ensureTaxColumns('products', withPercent: false, withAmount: false);
        $this->ensureTaxColumns('projects', withPercent: false, withAmount: false);
        $this->ensureTaxColumns('product_sales', withPercent: false, withAmount: true);
        $this->ensureQueryIndexes();
    }

    public function down(): void
    {
        // Irreversible schema sync for existing installs.
    }

    private function ensureEmployees(): void
    {
        if (! Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'position')) {
                $table->string('position')->nullable()->after('mobile');
            }
            if (! Schema::hasColumn('employees', 'resume')) {
                $table->string('resume')->nullable()->after('position');
            }
            if (! Schema::hasColumn('employees', 'fingerprint_device_uid')) {
                $table->unsignedInteger('fingerprint_device_uid')->nullable()->after('status');
            }
            if (! Schema::hasColumn('employees', 'fingerprint_enrolled_at')) {
                $table->timestamp('fingerprint_enrolled_at')->nullable()->after('fingerprint_device_uid');
            }
            if (! Schema::hasColumn('employees', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->unique()
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        if (Schema::hasTable('job_applications') && ! Schema::hasColumn('job_applications', 'employee_id')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->foreignId('employee_id')
                    ->nullable()
                    ->constrained('employees')
                    ->nullOnDelete();
            });
        }
    }

    private function ensureTeams(): void
    {
        if (! Schema::hasTable('teams') || Schema::hasColumn('teams', 'employee_id')) {
            return;
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('employee_id')
                ->nullable()
                ->unique()
                ->constrained('employees')
                ->nullOnDelete();
        });
    }

    private function ensureLeads(): void
    {
        if (! Schema::hasTable('leads') || Schema::hasColumn('leads', 'custom_fields')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->json('custom_fields')->nullable()->after('meta');
        });
    }

    private function ensureTaxColumns(string $table, bool $withPercent, bool $withAmount): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table, $withPercent, $withAmount) {
            if (! Schema::hasColumn($table, 'tax_rate_id')) {
                $blueprint->unsignedBigInteger('tax_rate_id')->nullable();
            }

            if ($withPercent && ! Schema::hasColumn($table, 'tax_percent')) {
                $blueprint->decimal('tax_percent', 8, 4)->default(0);
            }

            if ($withAmount && ! Schema::hasColumn($table, 'tax_amount')) {
                $blueprint->decimal('tax_amount', 15, 2)->default(0);
            }
        });

        $this->ensureTaxRateForeignKey($table);
    }

    private function ensureTaxRateForeignKey(string $table): void
    {
        if (
            ! Schema::hasTable($table)
            || ! Schema::hasTable('tax_rates')
            || ! Schema::hasColumn($table, 'tax_rate_id')
            || $this->hasForeignKey($table, 'tax_rate_id')
        ) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->foreign('tax_rate_id')->references('id')->on('tax_rates')->nullOnDelete();
        });
    }

    private function ensureQueryIndexes(): void
    {
        $this->addIndexIfMissing('invoices', 'invoices_status_paid_at_index', ['status', 'paid_at']);
        $this->addIndexIfMissing('projects', 'projects_payment_status_index', 'payment_status');
        $this->addIndexIfMissing('projects', 'projects_due_date_index', 'due_date');
        $this->addIndexIfMissing('whatsapp_campaigns', 'whatsapp_campaigns_status_created_at_index', ['status', 'created_at']);
        $this->addIndexIfMissing('deals', 'deals_status_won_at_index', ['status', 'won_at']);
        $this->addIndexIfMissing('deals', 'deals_status_lost_at_index', ['status', 'lost_at']);
        $this->addIndexIfMissing('deals', 'deals_status_assigned_to_index', ['status', 'assigned_to']);
        $this->addIndexIfMissing('deals', 'deals_status_pipeline_stage_id_index', ['status', 'pipeline_stage_id']);
        $this->addIndexIfMissing('leads', 'leads_assigned_to_status_index', ['assigned_to', 'status']);
        $this->addIndexIfMissing('leads', 'leads_blocked_index', 'blocked');
    }

    /**
     * @param  string|list<string>  $columns
     */
    private function addIndexIfMissing(string $table, string $index, string|array $columns): void
    {
        if (! Schema::hasTable($table) || Schema::hasIndex($table, $index)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($index, $columns) {
            $blueprint->index($columns, $index);
        });
    }

    private function hasForeignKey(string $table, string $column): bool
    {
        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            if (in_array($column, $foreignKey['columns'], true)) {
                return true;
            }
        }

        return false;
    }
};
