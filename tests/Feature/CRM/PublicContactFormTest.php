<?php

namespace Tests\Feature\CRM;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PublicContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_contact_form_requires_valid_fields(): void
    {
        $this->from(route('contact-us'))
            ->post(route('contact-us.store'), [
                'name' => '',
                'email' => 'not-an-email',
                'mobile' => '',
                'subject' => '',
                'message' => 'short',
            ])
            ->assertRedirect(route('contact-us'))
            ->assertSessionHasErrors(['name', 'email', 'mobile', 'subject', 'message']);
    }

    public function test_contact_form_stores_a_submission(): void
    {
        Mail::fake();

        $this->from(route('contact-us'))
            ->post(route('contact-us.store'), [
                'name' => 'Lina Contact',
                'email' => 'lina.contact@example.com',
                'mobile' => '01055556666',
                'subject' => 'Project inquiry',
                'message' => 'We would like a proposal for a new website.',
            ])
            ->assertRedirect(route('contact-us'));

        $this->assertDatabaseHas('contact_forms', [
            'email' => 'lina.contact@example.com',
            'subject' => 'Project inquiry',
        ]);
    }
}
