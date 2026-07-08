<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bridge for existing deployed databases after alter migrations were
 * folded into create migrations. Safe to run on fresh installs (no-ops).
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->ensureCompanies();
        $this->ensureLeads();
        $this->ensureContacts();
        $this->ensureContactForms();
        $this->ensureDealService();
        $this->ensureSubscriptionService();
        $this->ensureProjects();
        $this->ensureInvoices();
        $this->ensureInvoiceLines();
        $this->ensureProductSales();
        $this->ensureSalaries();
        $this->ensureTestimonials();
        $this->ensureProjectEmployees();
    }

    public function down(): void
    {
        // Irreversible schema sync for existing installs.
    }

    private function ensureCompanies(): void
    {
        if (! Schema::hasTable('companies') || Schema::hasColumn('companies', 'activity_type')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->string('activity_type', 100)->nullable()->after('name')->index();
        });
    }

    private function ensureLeads(): void
    {
        if (! Schema::hasTable('leads')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (! Schema::hasColumn('leads', 'job_title')) {
                $table->string('job_title')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('leads', 'city')) {
                $table->string('city', 100)->nullable()->after('company_name');
            }
            if (! Schema::hasColumn('leads', 'country')) {
                $table->string('country', 100)->nullable()->after('city');
            }
            if (! Schema::hasColumn('leads', 'website')) {
                $table->string('website')->nullable()->after('country');
            }
            if (! Schema::hasColumn('leads', 'industry')) {
                $table->string('industry', 150)->nullable()->after('website');
            }
            if (! Schema::hasColumn('leads', 'status')) {
                $table->string('status', 50)->nullable()->default('new')->index()->after('source');
            }
            if (! Schema::hasColumn('leads', 'attachments')) {
                $table->json('attachments')->nullable()->after('meta');
            }
        });
    }

    private function ensureContacts(): void
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('company_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('contacts', 'phone2')) {
                $table->string('phone2', 50)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('contacts', 'source')) {
                $table->string('source', 50)->nullable()->after('phone2');
            }
        });
    }

    private function ensureContactForms(): void
    {
        if (! Schema::hasTable('contact_forms')) {
            return;
        }

        Schema::table('contact_forms', function (Blueprint $table) {
            if (! Schema::hasColumn('contact_forms', 'lead_id')) {
                $table->foreignId('lead_id')->nullable()->after('blocked')->constrained('leads')->nullOnDelete();
            }
            if (! Schema::hasColumn('contact_forms', 'contact_id')) {
                $table->foreignId('contact_id')->nullable()->after('lead_id')->constrained('contacts')->nullOnDelete();
            }
            if (! Schema::hasColumn('contact_forms', 'converted_at')) {
                $table->timestamp('converted_at')->nullable()->after('contact_id');
            }
        });
    }

    private function ensureDealService(): void
    {
        if (Schema::hasTable('deal_service') || ! Schema::hasTable('deals')) {
            return;
        }

        Schema::create('deal_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained('deals')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['deal_id', 'service_id']);
        });
    }

    private function ensureSubscriptionService(): void
    {
        if (Schema::hasTable('subscription_service') || ! Schema::hasTable('subscriptions')) {
            return;
        }

        Schema::create('subscription_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['subscription_id', 'service_id']);
        });
    }

    private function ensureProjects(): void
    {
        if (! Schema::hasTable('projects') || Schema::hasColumn('projects', 'attachments')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('due_date');
        });
    }

    private function ensureInvoices(): void
    {
        if (! Schema::hasTable('invoices') || Schema::hasColumn('invoices', 'project_id')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('deal_id')->constrained('projects')->nullOnDelete();
            $table->index('project_id');
        });
    }

    private function ensureInvoiceLines(): void
    {
        if (! Schema::hasTable('invoice_lines') || Schema::hasColumn('invoice_lines', 'product_id')) {
            return;
        }

        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('service_id')->constrained('products')->nullOnDelete();
        });
    }

    private function ensureProductSales(): void
    {
        if (! Schema::hasTable('product_sales') || Schema::hasColumn('product_sales', 'invoice_id')) {
            return;
        }

        Schema::table('product_sales', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->after('company_id')->constrained('invoices')->nullOnDelete();
        });
    }

    private function ensureSalaries(): void
    {
        if (! Schema::hasTable('salaries') || Schema::hasColumn('salaries', 'period')) {
            return;
        }

        Schema::table('salaries', function (Blueprint $table) {
            $table->date('period')->nullable()->after('base_salary');
        });

        DB::table('salaries')->orderBy('id')->each(function ($salary) {
            $period = $salary->paid_at ?? $salary->created_at ?? now()->toDateString();

            DB::table('salaries')
                ->where('id', $salary->id)
                ->update(['period' => date('Y-m-01', strtotime((string) $period))]);
        });

        DB::statement('ALTER TABLE salaries MODIFY period DATE NOT NULL');

        Schema::table('salaries', function (Blueprint $table) {
            $table->unique(['employee_id', 'period']);
        });
    }

    private function ensureTestimonials(): void
    {
        if (! Schema::hasTable('testimonials')) {
            return;
        }

        if (Schema::hasColumn('testimonials', 'project_id')) {
            return;
        }

        Schema::dropIfExists('testimonials');

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained('projects')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->json('quote');
            $table->enum('status', ['Published', 'Archived'])->default('Published');
            $table->timestamps();
        });
    }

    private function ensureProjectEmployees(): void
    {
        if (Schema::hasTable('project_employees') || ! Schema::hasTable('projects')) {
            return;
        }

        Schema::create('project_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'employee_id']);
            $table->index(['employee_id', 'ended_at']);
        });
    }
};
