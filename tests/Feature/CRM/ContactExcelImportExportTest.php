<?php

namespace Tests\Feature\CRM;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Modules\CRM\Exports\ContactImportSampleExport;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Contact;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class ContactExcelImportExportTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_user_can_export_contacts_to_excel(): void
    {
        $user = $this->createAdminWithPermissions(['crm.contacts.export', 'crm.contacts.view']);

        Contact::query()->create([
            'name' => 'Export Contact',
            'email' => 'export.contact@example.com',
            'phone' => '+15550001111',
            'source' => 'manual',
        ]);

        $this->actingAs($user)
            ->get(route('admin.contacts.export'))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_user_can_download_import_sample(): void
    {
        $user = $this->createAdminWithPermissions(['crm.contacts.create', 'crm.contacts.view']);

        $this->actingAs($user)
            ->get(route('admin.contacts.importSample'))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_user_can_import_contacts_from_excel(): void
    {
        $user = $this->createAdminWithPermissions(['crm.contacts.create', 'crm.contacts.view']);
        $company = Company::query()->create([
            'user_id' => $user->id,
            'name' => 'Acme Corp',
        ]);

        $this->actingAs($user)
            ->from(route('admin.contacts.index'))
            ->post(route('admin.contacts.import'), [
                'file' => $this->excelUpload([
                    [
                        'John Smith',
                        'john.import@example.com',
                        '+1 555 000 2222',
                        '',
                        'manual',
                        'CTO',
                        'Imported note',
                        'Yes',
                        'Acme Corp',
                        '',
                    ],
                ]),
            ])
            ->assertRedirect(route('admin.contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'name' => 'John Smith',
            'email' => 'john.import@example.com',
            'phone' => '+1 555 000 2222',
            'job_title' => 'CTO',
            'company_id' => $company->id,
            'is_primary' => 1,
        ]);
    }

    public function test_import_updates_existing_contact_by_email(): void
    {
        $user = $this->createAdminWithPermissions(['crm.contacts.create', 'crm.contacts.view']);

        Contact::query()->create([
            'name' => 'Old Name',
            'email' => 'upsert@example.com',
            'phone' => '+15550003333',
            'job_title' => 'Old Title',
            'source' => 'manual',
        ]);

        $this->actingAs($user)
            ->from(route('admin.contacts.index'))
            ->post(route('admin.contacts.import'), [
                'file' => $this->excelUpload([
                    [
                        'New Name',
                        'upsert@example.com',
                        '+15550003333',
                        '',
                        'referral',
                        'New Title',
                        'Updated',
                        'No',
                        '',
                        '',
                    ],
                ]),
            ])
            ->assertRedirect(route('admin.contacts.index'));

        $this->assertSame(1, Contact::query()->where('email', 'upsert@example.com')->count());
        $this->assertDatabaseHas('contacts', [
            'email' => 'upsert@example.com',
            'name' => 'New Name',
            'job_title' => 'New Title',
            'source' => 'referral',
        ]);
    }

    /**
     * @param  list<list<string>>  $rows
     */
    private function excelUpload(array $rows): UploadedFile
    {
        $export = new class($rows) extends ContactImportSampleExport
        {
            /**
             * @param  list<list<string>>  $rows
             */
            public function __construct(private readonly array $rows) {}

            public function array(): array
            {
                return $this->rows;
            }
        };

        $relativePath = 'testing/contacts_import_'.uniqid('', true).'.xlsx';
        Excel::store($export, $relativePath);

        $fullPath = Storage::disk('local')->path($relativePath);

        return new UploadedFile(
            $fullPath,
            'contacts.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }
}
