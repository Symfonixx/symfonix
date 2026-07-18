<?php

namespace Modules\Core\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\BlogCategory;
use Modules\Cms\Models\Faq;
use Modules\Cms\Models\Page;
use Modules\Core\Support\DummyImageGenerator;
use Modules\CRM\Database\Seeders\PipelineStageSeeder;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Models\PipelineStage;
use Modules\CRM\Models\Subscription;
use Modules\Finance\Database\Seeders\ExpenseCategorySeeder;
use Modules\Product\Database\Seeders\ProductCategorySeeder;
use Modules\Product\Database\Seeders\ProductSaleScenarioSeeder;
use Modules\Product\Database\Seeders\TechProductSeeder;
use Modules\Product\Models\Product;
use Modules\Project\Database\Seeders\ProjectStatusSeeder;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectEmployee;
use Modules\Project\Models\ProjectStatus;
use Modules\Project\Models\ProjectUseCase;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceCategory;
use Modules\Support\Database\Seeders\TicketCategorySeeder;
use Modules\Support\Models\Subscriber;
use Modules\Support\Models\Ticket;
use Modules\Support\Models\TicketCategory;
use Modules\Support\Models\TicketMessage;
use Modules\Team\Models\Team;
use Modules\Testimonial\Models\Testimonial;
use Modules\User\Database\Seeders\AdminScenarioSeeder;
use Modules\User\Database\Seeders\EmployeeScenarioSeeder;
use Modules\User\Database\Seeders\LeaveScenarioSeeder;
use Modules\User\Database\Seeders\RoleScenarioSeeder;
use Modules\User\Models\Employee;

class SystemDummyDataSeeder extends Seeder
{
    private DummyImageGenerator $images;

    public function run(): void
    {
        $this->images = new DummyImageGenerator;

        $this->seedReferenceData();
        $this->seedUsersAndHr();
        $this->seedCustomers();
        $this->seedTeam();
        $this->seedCms();
        $this->seedServices();
        $this->seedProducts();
        $this->seedCrm();
        $this->seedProjects();
        $this->seedTestimonials();
        $this->seedSupport();
        $this->seedProductSales();
        $this->seedMultiCurrencyFinance();

        $this->command?->info('All modules seeded with dummy data and images.');
    }

    private function seedReferenceData(): void
    {
        $this->call([
            PipelineStageSeeder::class,
            TicketCategorySeeder::class,
            ProjectStatusSeeder::class,
            ExpenseCategorySeeder::class,
            \Modules\Finance\Database\Seeders\CurrencySettingsSeeder::class,
        ]);
    }

    private function seedUsersAndHr(): void
    {
        $this->call([
            RoleScenarioSeeder::class,
            EmployeeScenarioSeeder::class,
            AdminScenarioSeeder::class,
            LeaveScenarioSeeder::class,
        ]);

        User::query()->admins()->each(function (User $user) {
            if ($user->img) {
                return;
            }

            $user->update([
                'img' => $this->images->storeAvatar($user->name, 'avatars'),
            ]);
        });
    }

    private function seedCustomers(): void
    {
        $customers = [
            ['name' => 'Ahmed Farouk', 'email' => 'customer1@demo.symfonix.com', 'mobile' => '01030000001'],
            ['name' => 'Sara Ibrahim', 'email' => 'customer2@demo.symfonix.com', 'mobile' => '01030000002'],
            ['name' => 'Hassan Ali', 'email' => 'customer3@demo.symfonix.com', 'mobile' => '01030000003'],
            ['name' => 'Mona Saeed', 'email' => 'customer4@demo.symfonix.com', 'mobile' => '01030000004'],
            ['name' => 'Tarek Nassar', 'email' => 'customer5@demo.symfonix.com', 'mobile' => '01030000005'],
        ];

        foreach ($customers as $customer) {
            User::query()->updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'mobile' => $customer['mobile'],
                    'password' => Hash::make('password'),
                    'type' => User::TYPE_CUSTOMER,
                    'img' => $this->images->storeAvatar($customer['name'], 'avatars'),
                ]
            );
        }
    }

    private function seedTeam(): void
    {
        $members = [
            ['name' => 'Layla Mansour', 'position' => 'CEO & Founder', 'skills' => 'Leadership, Strategy, Product'],
            ['name' => 'Karim Haddad', 'position' => 'CTO', 'skills' => 'Architecture, Laravel, Cloud'],
            ['name' => 'Nadine Salim', 'position' => 'Head of Design', 'skills' => 'UI/UX, Branding, Figma'],
            ['name' => 'Rami Khouri', 'position' => 'Lead Developer', 'skills' => 'PHP, Vue, DevOps'],
            ['name' => 'Salma Aziz', 'position' => 'Marketing Lead', 'skills' => 'Growth, Content, SEO'],
            ['name' => 'Omar Farid', 'position' => 'Customer Success', 'skills' => 'Support, Onboarding, CRM'],
        ];

        foreach ($members as $member) {
            $slug = Str::slug($member['name']);

            Team::query()->updateOrCreate(
                ['linked_in' => 'https://linkedin.com/in/'.$slug],
                [
                    'name' => ['en' => $member['name'], 'ar' => $member['name'], 'tr' => $member['name']],
                    'position' => ['en' => $member['position'], 'ar' => $member['position'], 'tr' => $member['position']],
                    'facebook' => 'https://facebook.com/'.$slug,
                    'github' => 'https://github.com/'.$slug,
                    'behance' => null,
                    'resume' => null,
                    'key_skills' => $member['skills'],
                    'avatar' => $this->images->storeAvatar($member['name'], 'teams'),
                    'status' => 'Published',
                ]
            );
        }
    }

    private function seedCms(): void
    {
        $categories = [
            ['name' => 'Product Updates', 'slug' => 'product-updates'],
            ['name' => 'Engineering', 'slug' => 'engineering'],
            ['name' => 'Company News', 'slug' => 'company-news'],
        ];

        foreach ($categories as $category) {
            BlogCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => [
                        'en' => $category['name'],
                        'ar' => $category['name'],
                        'tr' => $category['name'],
                    ],
                ]
            );
        }

        $categoryIds = BlogCategory::query()->pluck('id', 'slug');

        $posts = [
            [
                'slug' => 'introducing-symfonix-crm',
                'category' => 'product-updates',
                'title' => 'Introducing Symfonix CRM',
                'description' => 'A closer look at our all-in-one CRM built for growing tech teams.',
            ],
            [
                'slug' => 'scaling-laravel-apps',
                'category' => 'engineering',
                'title' => 'Scaling Laravel Applications',
                'description' => 'Practical patterns we use to keep Laravel apps fast and reliable.',
            ],
            [
                'slug' => 'design-system-refresh',
                'category' => 'company-news',
                'title' => 'Design System Refresh',
                'description' => 'How we rebuilt our design language for clarity and speed.',
            ],
            [
                'slug' => 'api-first-integrations',
                'category' => 'engineering',
                'title' => 'API-First Integrations',
                'description' => 'Why we design every product feature with integrations in mind.',
            ],
            [
                'slug' => 'customer-success-playbook',
                'category' => 'company-news',
                'title' => 'Customer Success Playbook',
                'description' => 'The onboarding rituals that help new clients see value fast.',
            ],
            [
                'slug' => 'roadmap-highlights',
                'category' => 'product-updates',
                'title' => 'Roadmap Highlights',
                'description' => 'Upcoming features across CRM, finance, and project delivery.',
            ],
        ];

        foreach ($posts as $index => $post) {
            Blog::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'category_id' => $categoryIds[$post['category']] ?? $categoryIds->first(),
                    'title' => ['en' => $post['title'], 'ar' => $post['title'], 'tr' => $post['title']],
                    'description' => ['en' => $post['description'], 'ar' => $post['description'], 'tr' => $post['description']],
                    'content' => [
                        'en' => '<p>'.$post['description'].'</p><p>This is seeded demo content for Symfonix.</p>',
                        'ar' => '<p>'.$post['description'].'</p>',
                        'tr' => '<p>'.$post['description'].'</p>',
                    ],
                    'keywords' => ['en' => 'symfonix, demo, blog', 'ar' => 'symfonix', 'tr' => 'symfonix'],
                    'image' => $this->images->store('blogs', $post['title'], 1200, 675),
                    'status' => CmsStatus::PUBLISHED->value,
                    'featured' => $index < 3 ? 1 : 0,
                    'visits' => random_int(20, 500),
                ]
            );
        }

        $pages = [
            [
                'slug' => 'about-us',
                'title' => 'About Us',
                'description' => 'Learn more about the Symfonix team and mission.',
                'nav' => true,
                'footer' => true,
            ],
            [
                'slug' => 'careers',
                'title' => 'Careers',
                'description' => 'Join a product-minded team building business software.',
                'nav' => true,
                'footer' => true,
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'description' => 'How we collect, use, and protect your data.',
                'nav' => false,
                'footer' => true,
            ],
            [
                'slug' => 'terms-of-service',
                'title' => 'Terms of Service',
                'description' => 'The terms that govern use of Symfonix products.',
                'nav' => false,
                'footer' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => ['en' => $page['title'], 'ar' => $page['title'], 'tr' => $page['title']],
                    'description' => ['en' => $page['description'], 'ar' => $page['description'], 'tr' => $page['description']],
                    'content' => [
                        'en' => '<p>'.$page['description'].'</p><p>Seeded page content for demonstration purposes.</p>',
                        'ar' => '<p>'.$page['description'].'</p>',
                        'tr' => '<p>'.$page['description'].'</p>',
                    ],
                    'keywords' => ['en' => 'symfonix, '.$page['slug'], 'ar' => 'symfonix', 'tr' => 'symfonix'],
                    'image' => $this->images->store('pages', $page['title'], 1400, 700),
                    'status' => CmsStatus::PUBLISHED->value,
                    'featured' => 0,
                    'add_to_nav' => $page['nav'],
                    'add_to_footer' => $page['footer'],
                    'add_to_top_bar' => false,
                    'visits' => random_int(10, 200),
                ]
            );
        }

        $faqs = [
            ['q' => 'What is Symfonix?', 'a' => 'Symfonix is an all-in-one business platform covering CRM, projects, finance, and CMS.'],
            ['q' => 'Can I try demo data?', 'a' => 'Yes. Run php artisan app:seed-dummy to populate a full demo environment.'],
            ['q' => 'Does it support multiple languages?', 'a' => 'Yes. Content models support English, Arabic, and Turkish translations.'],
            ['q' => 'How do I contact support?', 'a' => 'Open a ticket from the customer portal or email support@symfonix.com.'],
            ['q' => 'Is billing included?', 'a' => 'Finance modules cover invoices, expenses, commissions, and subscription renewals.'],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::query()->updateOrCreate(
                ['question->en' => $faq['q']],
                [
                    'question' => ['en' => $faq['q'], 'ar' => $faq['q'], 'tr' => $faq['q']],
                    'answer' => ['en' => $faq['a'], 'ar' => $faq['a'], 'tr' => $faq['a']],
                    'rank' => $index + 1,
                    'status' => CmsStatus::PUBLISHED->value,
                ]
            );
        }
    }

    private function seedServices(): void
    {
        $categories = [
            [
                'slug' => 'software-development',
                'title' => 'Software Development',
                'description' => 'Custom web and mobile application development.',
                'color' => '#2563eb',
            ],
            [
                'slug' => 'cloud-devops',
                'title' => 'Cloud & DevOps',
                'description' => 'Infrastructure, CI/CD, and managed cloud services.',
                'color' => '#059669',
            ],
            [
                'slug' => 'digital-consulting',
                'title' => 'Digital Consulting',
                'description' => 'Strategy, discovery, and digital transformation.',
                'color' => '#d97706',
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'title' => ['en' => $category['title'], 'ar' => $category['title'], 'tr' => $category['title']],
                    'description' => ['en' => $category['description'], 'ar' => $category['description'], 'tr' => $category['description']],
                    'color_code' => $category['color'],
                    'image' => $this->images->store('service_categories', $category['title'], 900, 600),
                ]
            );
        }

        $categoryIds = ServiceCategory::query()->pluck('id', 'slug');

        $services = [
            [
                'slug' => 'web-application-development',
                'category' => 'software-development',
                'title' => 'Web Application Development',
                'description' => 'End-to-end Laravel and Vue web platforms.',
            ],
            [
                'slug' => 'mobile-app-development',
                'category' => 'software-development',
                'title' => 'Mobile App Development',
                'description' => 'Native and cross-platform mobile experiences.',
            ],
            [
                'slug' => 'managed-cloud-hosting',
                'category' => 'cloud-devops',
                'title' => 'Managed Cloud Hosting',
                'description' => 'Secure, monitored hosting with backups and scaling.',
            ],
            [
                'slug' => 'devops-automation',
                'category' => 'cloud-devops',
                'title' => 'DevOps Automation',
                'description' => 'CI/CD pipelines, containers, and observability.',
            ],
            [
                'slug' => 'product-discovery',
                'category' => 'digital-consulting',
                'title' => 'Product Discovery',
                'description' => 'Workshops that turn ideas into actionable roadmaps.',
            ],
            [
                'slug' => 'ux-audit',
                'category' => 'digital-consulting',
                'title' => 'UX Audit',
                'description' => 'Usability reviews with prioritized improvement plans.',
            ],
        ];

        foreach ($services as $index => $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                [
                    'service_category_id' => $categoryIds[$service['category']] ?? $categoryIds->first(),
                    'title' => ['en' => $service['title'], 'ar' => $service['title'], 'tr' => $service['title']],
                    'description' => ['en' => $service['description'], 'ar' => $service['description'], 'tr' => $service['description']],
                    'content' => [
                        'en' => '<p>'.$service['description'].'</p><p>Seeded service offering for demo environments.</p>',
                        'ar' => '<p>'.$service['description'].'</p>',
                        'tr' => '<p>'.$service['description'].'</p>',
                    ],
                    'keywords' => ['en' => 'service, demo', 'ar' => 'service', 'tr' => 'service'],
                    'image' => $this->images->store('services', $service['title'], 1100, 700),
                    'status' => CmsStatus::PUBLISHED->value,
                    'featured' => $index < 3 ? 1 : 0,
                    'visits' => random_int(15, 350),
                ]
            );
        }
    }

    private function seedProducts(): void
    {
        $this->call([
            ProductCategorySeeder::class,
            TechProductSeeder::class,
        ]);

        Product::query()->each(function (Product $product) {
            $name = $product->getTranslation('name', 'en') ?: (string) $product->name;
            $description = $product->getTranslation('description', 'en') ?: '';

            $product->update([
                'main_image' => $this->images->store('products', $name, 1000, 750),
                'seo_meta_img' => $this->images->store('products', $name.' SEO', 1200, 630),
                'is_published' => true,
                'short_description' => $description,
            ]);
        });
    }

    private function seedCrm(): void
    {
        $customers = User::query()->customers()->orderBy('id')->get();
        $employees = Employee::query()->where('status', Employee::STATUS_ACTIVE)->orderBy('id')->get();
        $stages = PipelineStage::query()->orderBy('sort_order')->get();
        $services = Service::query()->orderBy('id')->get();

        $companies = [
            [
                'name' => 'Nova Tech Labs',
                'activity_type' => Company::ACTIVITY_TECHNOLOGY,
                'email' => 'hello@novatech.demo',
                'phone' => '+201000000101',
                'country' => 'Egypt',
                'city' => 'Cairo',
            ],
            [
                'name' => 'Green Retail Co',
                'activity_type' => Company::ACTIVITY_RETAIL,
                'email' => 'ops@greenretail.demo',
                'phone' => '+201000000102',
                'country' => 'UAE',
                'city' => 'Dubai',
            ],
            [
                'name' => 'CarePlus Clinics',
                'activity_type' => Company::ACTIVITY_HEALTHCARE,
                'email' => 'admin@careplus.demo',
                'phone' => '+201000000103',
                'country' => 'Saudi Arabia',
                'city' => 'Riyadh',
            ],
            [
                'name' => 'Finora Capital',
                'activity_type' => Company::ACTIVITY_FINANCE,
                'email' => 'contact@finora.demo',
                'phone' => '+201000000104',
                'country' => 'Egypt',
                'city' => 'Alexandria',
            ],
            [
                'name' => 'EduSpark Academy',
                'activity_type' => Company::ACTIVITY_EDUCATION,
                'email' => 'team@eduspark.demo',
                'phone' => '+201000000105',
                'country' => 'Turkey',
                'city' => 'Istanbul',
            ],
            [
                'name' => 'Atlas Consulting',
                'activity_type' => Company::ACTIVITY_CONSULTING,
                'email' => 'hello@atlasconsult.demo',
                'phone' => '+201000000106',
                'country' => 'Jordan',
                'city' => 'Amman',
            ],
        ];

        $companyModels = collect();

        foreach ($companies as $index => $company) {
            $customer = $customers[$index % max($customers->count(), 1)] ?? null;

            $model = Company::query()->updateOrCreate(
                ['email' => $company['email']],
                [
                    'user_id' => $customer?->id,
                    'name' => $company['name'],
                    'activity_type' => $company['activity_type'],
                    'phone' => $company['phone'],
                    'country' => $company['country'],
                    'city' => $company['city'],
                    'address' => $company['city'].' Business District',
                    'notes' => 'Seeded demo company for Symfonix.',
                    'status' => Company::STATUS_ACTIVE,
                ]
            );

            $companyModels->push($model);

            Contact::query()->updateOrCreate(
                ['email' => 'contact+'.$index.'@'.$model->id.'.demo'],
                [
                    'company_id' => $model->id,
                    'user_id' => $customer?->id,
                    'name' => ($customer?->name ?? 'Primary Contact').' ('.$model->name.')',
                    'phone' => $company['phone'],
                    'source' => Lead::SOURCES[$index % count(Lead::SOURCES)],
                    'job_title' => 'Decision Maker',
                    'notes' => 'Primary seeded contact.',
                    'is_primary' => true,
                ]
            );
        }

        foreach ($companyModels->take(5) as $index => $company) {
            $assignee = $employees[$index % max($employees->count(), 1)] ?? null;
            $service = $services[$index % max($services->count(), 1)] ?? null;

            Lead::query()->updateOrCreate(
                ['email' => 'lead'.$index.'@'.$company->id.'.demo'],
                [
                    'name' => 'Lead '.$company->name,
                    'phone' => $company->phone,
                    'job_title' => 'Procurement Manager',
                    'company_name' => $company->name,
                    'company_id' => $company->id,
                    'city' => $company->city,
                    'country' => $company->country,
                    'website' => 'https://'.Str::slug($company->name).'.demo',
                    'industry' => $company->activity_type,
                    'assigned_to' => $assignee?->id,
                    'source' => Lead::SOURCES[$index % count(Lead::SOURCES)],
                    'status' => Lead::STATUSES[$index % count(Lead::STATUSES)],
                    'project_budget' => 5000 + ($index * 2500),
                    'service_interest' => $service?->getTranslation('title', 'en'),
                    'service_id' => $service?->id,
                    'problem_statement' => 'Looking for a scalable digital platform.',
                    'locale' => 'en',
                    'blocked' => false,
                ]
            );
        }

        $openStage = $stages->firstWhere('is_won', false);
        $wonStage = $stages->firstWhere('is_won', true);

        foreach ($companyModels->take(4) as $index => $company) {
            $assignee = $employees[$index % max($employees->count(), 1)] ?? null;
            $stage = $index === 0 ? $wonStage : ($stages[$index % max($stages->count(), 1)] ?? $openStage);
            $currencies = ['USD', 'EUR', 'GBP', 'TRY'];

            Deal::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'title' => $company->name.' Platform Deal',
                ],
                [
                    'pipeline_stage_id' => $stage?->id,
                    'assigned_to' => $assignee?->id,
                    'value' => 8000 + ($index * 4500),
                    'currency' => $currencies[$index % count($currencies)],
                    'probability' => $stage?->probability ?? 25,
                    'expected_close_date' => now()->addDays(14 + ($index * 7))->toDateString(),
                    'source' => Lead::SOURCE_WEBSITE,
                    'description' => 'Seeded demo opportunity.',
                    'status' => $stage?->is_won ? Deal::STATUS_WON : Deal::STATUS_OPEN,
                    'won_at' => $stage?->is_won ? now()->subDays(3) : null,
                    'closed_at' => $stage?->is_won ? now()->subDays(3) : null,
                ]
            );
        }

        foreach ($companyModels->take(3) as $index => $company) {
            $service = $services[$index % max($services->count(), 1)] ?? null;

            if ($service === null) {
                continue;
            }

            $subscriptionCurrencies = ['USD', 'EUR', 'GBP'];

            Subscription::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'service_id' => $service->id,
                    'name' => $service->getTranslation('title', 'en').' Retainer',
                ],
                [
                    'status' => Subscription::STATUS_ACTIVE,
                    'billing_cycle' => Subscription::BILLING_MONTHLY,
                    'amount' => 1200 + ($index * 300),
                    'currency' => $subscriptionCurrencies[$index % count($subscriptionCurrencies)],
                    'starts_at' => now()->subMonths(2)->toDateString(),
                    'ends_at' => null,
                    'renewal_at' => now()->addMonth()->toDateString(),
                    'auto_renew' => true,
                    'notes' => 'Seeded active subscription.',
                ]
            );
        }

        $admin = User::query()->admins()->orderBy('id')->first();

        if ($admin) {
            MarketingCampaign::query()->updateOrCreate(
                ['subject' => 'Welcome to Symfonix Demo'],
                [
                    'user_id' => $admin->id,
                    'body' => '<p>Thanks for exploring Symfonix. This campaign was seeded for demo purposes.</p>',
                    'recipients_count' => $customers->count(),
                    'status' => MarketingCampaign::STATUS_FINISHED,
                    'recipient_sources' => ['customers'],
                ]
            );
        }
    }

    private function seedProjects(): void
    {
        $companies = Company::query()->orderBy('id')->get();
        $statuses = ProjectStatus::query()->orderBy('sort_order')->get();
        $employees = Employee::query()->where('status', Employee::STATUS_ACTIVE)->orderBy('id')->get();
        $deals = Deal::query()->orderBy('id')->get();

        if ($companies->isEmpty() || $statuses->isEmpty()) {
            return;
        }

        $projects = [
            ['title' => 'CRM Rollout', 'budget' => 25000, 'currency' => 'USD', 'payment' => Project::PAYMENT_PARTIALLY_PAID],
            ['title' => 'E-Commerce Rebuild', 'budget' => 44000, 'currency' => 'EUR', 'payment' => Project::PAYMENT_UNPAID],
            ['title' => 'Patient Portal', 'budget' => 32000, 'currency' => 'GBP', 'payment' => Project::PAYMENT_FULLY_PAID],
            ['title' => 'Learning Platform', 'budget' => 41000, 'currency' => 'USD', 'payment' => Project::PAYMENT_PARTIALLY_PAID],
            ['title' => 'Analytics Dashboard', 'budget' => 620000, 'currency' => 'TRY', 'payment' => Project::PAYMENT_UNPAID],
        ];

        foreach ($projects as $index => $item) {
            $company = $companies[$index % $companies->count()];
            $status = $statuses[$index % $statuses->count()];
            $deal = $deals[$index % max($deals->count(), 1)] ?? null;
            $currencyService = app(\Modules\Finance\Services\CurrencyService::class);

            $project = Project::query()->updateOrCreate(
                [
                    'title' => $item['title'],
                    'company_id' => $company->id,
                ],
                [
                    'description' => 'Seeded demo project for '.$company->name.'.',
                    'project_status_id' => $status->id,
                    'deal_id' => $deal?->id,
                    'budget' => $item['budget'],
                    'currency' => $item['currency'],
                    'budget_exchange_rate' => $currencyService->snapshotRateToBase($item['currency']),
                    'payment_status' => $item['payment'],
                    'start_date' => now()->subDays(40 - ($index * 5))->toDateString(),
                    'due_date' => now()->addDays(30 + ($index * 10))->toDateString(),
                ]
            );

            if ($employees->isNotEmpty()) {
                ProjectEmployee::query()->updateOrCreate(
                    [
                        'project_id' => $project->id,
                        'employee_id' => $employees[$index % $employees->count()]->id,
                    ],
                    [
                        'role' => $index % 2 === 0 ? 'Project Lead' : 'Developer',
                        'started_at' => $project->start_date,
                        'ended_at' => null,
                        'notes' => 'Seeded assignment.',
                    ]
                );
            }

            ProjectUseCase::query()->updateOrCreate(
                ['slug' => Str::slug($item['title']).'-case-study'],
                [
                    'project_id' => $project->id,
                    'title' => ['en' => $item['title'].' Case Study', 'ar' => $item['title'], 'tr' => $item['title']],
                    'client_name' => ['en' => $company->name, 'ar' => $company->name, 'tr' => $company->name],
                    'summary' => [
                        'en' => 'How '.$company->name.' modernized operations with Symfonix.',
                        'ar' => 'How '.$company->name.' modernized operations with Symfonix.',
                        'tr' => 'How '.$company->name.' modernized operations with Symfonix.',
                    ],
                    'challenge' => [
                        'en' => 'Legacy tools made collaboration and reporting difficult.',
                        'ar' => 'Legacy tools made collaboration and reporting difficult.',
                        'tr' => 'Legacy tools made collaboration and reporting difficult.',
                    ],
                    'solution' => [
                        'en' => 'We delivered a tailored platform covering CRM, delivery, and finance.',
                        'ar' => 'We delivered a tailored platform covering CRM, delivery, and finance.',
                        'tr' => 'We delivered a tailored platform covering CRM, delivery, and finance.',
                    ],
                    'results' => [
                        'en' => 'Faster sales cycles and clearer project visibility.',
                        'ar' => 'Faster sales cycles and clearer project visibility.',
                        'tr' => 'Faster sales cycles and clearer project visibility.',
                    ],
                    'content' => [
                        'en' => '<p>Full case study content seeded for demo.</p>',
                        'ar' => '<p>Full case study content seeded for demo.</p>',
                        'tr' => '<p>Full case study content seeded for demo.</p>',
                    ],
                    'image' => $this->images->store('project-use-cases', $item['title'], 1200, 800),
                    'technologies' => ['Laravel', 'Vue', 'MySQL', 'Redis'],
                    'category_tag' => 'Product Delivery',
                    'project_url' => 'https://example.com/'.Str::slug($item['title']),
                    'completed_year' => (int) now()->year,
                    'featured' => $index < 3,
                    'status' => CmsStatus::PUBLISHED->value,
                    'sort_order' => $index + 1,
                    'visits' => random_int(25, 400),
                ]
            );
        }
    }

    private function seedTestimonials(): void
    {
        $projects = Project::query()->with('company')->orderBy('id')->get();
        $customers = User::query()->customers()->orderBy('id')->get();

        if ($projects->isEmpty() || $customers->isEmpty()) {
            return;
        }

        $quotes = [
            'Symfonix helped us centralize sales and delivery in one place.',
            'The onboarding was smooth and the team understood our workflow quickly.',
            'We finally have clear visibility across projects, invoices, and customer success.',
            'A practical platform that feels built for real agency operations.',
        ];

        foreach ($quotes as $index => $quote) {
            $project = $projects[$index % $projects->count()];
            $customer = $customers[$index % $customers->count()];

            Testimonial::query()->updateOrCreate(
                [
                    'project_id' => $project->id,
                    'customer_id' => $customer->id,
                ],
                [
                    'quote' => ['en' => $quote, 'ar' => $quote, 'tr' => $quote],
                    'status' => CmsStatus::PUBLISHED->value,
                ]
            );
        }
    }

    private function seedSupport(): void
    {
        $customers = User::query()->customers()->orderBy('id')->get();
        $admins = User::query()->admins()->orderBy('id')->get();
        $categories = TicketCategory::query()->orderBy('sort_order')->get();

        if ($customers->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $tickets = [
            ['subject' => 'Cannot access analytics dashboard', 'priority' => Ticket::PRIORITY_HIGH, 'status' => Ticket::STATUS_OPEN],
            ['subject' => 'Invoice PDF download fails', 'priority' => Ticket::PRIORITY_MEDIUM, 'status' => Ticket::STATUS_IN_PROGRESS],
            ['subject' => 'Need help inviting team members', 'priority' => Ticket::PRIORITY_LOW, 'status' => Ticket::STATUS_RESOLVED],
            ['subject' => 'API rate limit questions', 'priority' => Ticket::PRIORITY_MEDIUM, 'status' => Ticket::STATUS_CLOSED],
        ];

        foreach ($tickets as $index => $item) {
            $customer = $customers[$index % $customers->count()];
            $category = $categories[$index % $categories->count()];
            $assignee = $admins[$index % max($admins->count(), 1)] ?? null;
            $ticketNumber = 'TKT-DEMO-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            $ticket = Ticket::query()->updateOrCreate(
                ['ticket_number' => $ticketNumber],
                [
                    'user_id' => $customer->id,
                    'ticket_category_id' => $category->id,
                    'assigned_to' => $assignee?->id,
                    'subject' => $item['subject'],
                    'description' => $item['subject'].'. Seeded support ticket for demo purposes.',
                    'priority' => $item['priority'],
                    'status' => $item['status'],
                    'closed_at' => in_array($item['status'], [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED], true)
                        ? now()->subDays(2)
                        : null,
                ]
            );

            TicketMessage::query()->updateOrCreate(
                [
                    'ticket_id' => $ticket->id,
                    'user_id' => $customer->id,
                    'body' => 'Hi, I need help with: '.$item['subject'],
                ],
                []
            );

            if ($assignee) {
                TicketMessage::query()->updateOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'user_id' => $assignee->id,
                        'body' => 'Thanks for reaching out. We are looking into this demo ticket.',
                    ],
                    []
                );
            }
        }

        foreach (['news1@demo.symfonix.com', 'news2@demo.symfonix.com', 'news3@demo.symfonix.com'] as $email) {
            Subscriber::query()->updateOrCreate(
                ['email' => $email],
                [
                    'ip_address' => '127.0.0.1',
                    'lang' => 'en',
                    'blocked' => false,
                ]
            );
        }
    }

    private function seedProductSales(): void
    {
        $this->call(ProductSaleScenarioSeeder::class);
    }

    private function seedMultiCurrencyFinance(): void
    {
        $this->call(\Modules\Finance\Database\Seeders\MultiCurrencyTransactionSeeder::class);
    }
}
