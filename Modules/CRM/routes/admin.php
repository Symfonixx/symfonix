<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\Admin\ActivityController;
use Modules\CRM\Http\Controllers\Admin\CalendarController;
use Modules\CRM\Http\Controllers\Admin\CompanyController;
use Modules\CRM\Http\Controllers\Admin\ContactController;
use Modules\CRM\Http\Controllers\Admin\ContactFormController;
use Modules\CRM\Http\Controllers\Admin\CrmDashboardController;
use Modules\CRM\Http\Controllers\Admin\DealController;
use Modules\CRM\Http\Controllers\Admin\LeadController;
use Modules\CRM\Http\Controllers\Admin\LeadCustomFieldController;
use Modules\CRM\Http\Controllers\Admin\LeadTagController;
use Modules\CRM\Http\Controllers\Admin\MarketingController;
use Modules\CRM\Http\Controllers\Admin\QuoteController;
use Modules\CRM\Http\Controllers\Admin\SalesForecastController;
use Modules\CRM\Http\Controllers\Admin\SalesTargetController;
use Modules\CRM\Http\Controllers\Admin\SubscriptionController;
use Modules\CRM\Http\Controllers\Admin\WhatsAppMarketingController;
use Modules\CRM\Http\Controllers\Admin\WhatsAppTemplateController;

Route::group([], function () {
    Route::get('crm/dashboard', [CrmDashboardController::class, 'index'])->name('crm.dashboard');
    Route::match(['put', 'post'], 'crm/dashboard/layout', [CrmDashboardController::class, 'updateLayout'])->name('crm.dashboard.layout');
    Route::get('crm/calendar', [CalendarController::class, 'index'])->name('crm.calendar');
    Route::get('crm/calendar/events', [CalendarController::class, 'events'])->name('crm.calendar.events');
    Route::get('crm/sales-targets', [SalesTargetController::class, 'index'])->name('crm.sales-targets.index');
    Route::put('crm/sales-targets', [SalesTargetController::class, 'update'])->name('crm.sales-targets.update');
    Route::get('crm/sales-forecasts', [SalesForecastController::class, 'index'])->name('crm.sales-forecasts.index');
    Route::get('crm/sales-forecasts/data', [SalesForecastController::class, 'data'])->name('crm.sales-forecasts.data');

    Route::get('crm/lead-tags', [LeadTagController::class, 'index'])->name('crm.lead-tags.index');
    Route::get('crm/lead-tags/create', [LeadTagController::class, 'create'])->name('crm.lead-tags.create');
    Route::post('crm/lead-tags', [LeadTagController::class, 'store'])->name('crm.lead-tags.store');
    Route::get('crm/lead-tags/{leadTag}/edit', [LeadTagController::class, 'edit'])->name('crm.lead-tags.edit');
    Route::put('crm/lead-tags/{leadTag}', [LeadTagController::class, 'update'])->name('crm.lead-tags.update');
    Route::delete('crm/lead-tags/{leadTag}', [LeadTagController::class, 'destroy'])->name('crm.lead-tags.destroy');

    Route::get('crm/custom-fields', [LeadCustomFieldController::class, 'index'])->name('crm.custom-fields.index');
    Route::get('crm/custom-fields/create', [LeadCustomFieldController::class, 'create'])->name('crm.custom-fields.create');
    Route::post('crm/custom-fields', [LeadCustomFieldController::class, 'store'])->name('crm.custom-fields.store');
    Route::get('crm/custom-fields/{leadCustomField}/edit', [LeadCustomFieldController::class, 'edit'])->name('crm.custom-fields.edit');
    Route::put('crm/custom-fields/{leadCustomField}', [LeadCustomFieldController::class, 'update'])->name('crm.custom-fields.update');
    Route::delete('crm/custom-fields/{leadCustomField}', [LeadCustomFieldController::class, 'destroy'])->name('crm.custom-fields.destroy');

    Route::get('crm/marketing', [MarketingController::class, 'index'])->name('crm.marketing.index');
    Route::get('crm/marketing/create', [MarketingController::class, 'create'])->name('crm.marketing.create');
    Route::post('crm/marketing', [MarketingController::class, 'store'])->name('crm.marketing.store');

    Route::get('crm/marketing/whatsapp/create', [WhatsAppMarketingController::class, 'create'])->name('crm.marketing.whatsapp.create');
    Route::post('crm/marketing/whatsapp', [WhatsAppMarketingController::class, 'store'])->name('crm.marketing.whatsapp.store');
    Route::get('crm/marketing/whatsapp/templates/{template}/variables', [WhatsAppMarketingController::class, 'templateVariables'])->name('crm.marketing.whatsapp.templates.variables');
    Route::get('crm/marketing/whatsapp/{campaign}', [WhatsAppMarketingController::class, 'show'])->name('crm.marketing.whatsapp.show');

    Route::get('crm/marketing/whatsapp-templates', [WhatsAppTemplateController::class, 'index'])->name('crm.marketing.whatsapp-templates.index');
    Route::get('crm/marketing/whatsapp-templates/create', [WhatsAppTemplateController::class, 'create'])->name('crm.marketing.whatsapp-templates.create');
    Route::post('crm/marketing/whatsapp-templates', [WhatsAppTemplateController::class, 'store'])->name('crm.marketing.whatsapp-templates.store');
    Route::get('crm/marketing/whatsapp-templates/{template}/edit', [WhatsAppTemplateController::class, 'edit'])->name('crm.marketing.whatsapp-templates.edit');
    Route::put('crm/marketing/whatsapp-templates/{template}', [WhatsAppTemplateController::class, 'update'])->name('crm.marketing.whatsapp-templates.update');
    Route::delete('crm/marketing/whatsapp-templates/{template}', [WhatsAppTemplateController::class, 'destroy'])->name('crm.marketing.whatsapp-templates.destroy');

    Route::get('crm/marketing/{marketing}', [MarketingController::class, 'show'])->name('crm.marketing.show');

    Route::delete('companies/deleteMulti', [CompanyController::class, 'deleteMulti'])->name('companies.deleteMulti');
    Route::resource('companies', CompanyController::class);

    Route::delete('contacts/deleteMulti', [ContactController::class, 'deleteMulti'])->name('contacts.deleteMulti');
    Route::resource('contacts', ContactController::class);

    Route::delete('deals/deleteMulti', [DealController::class, 'deleteMulti'])->name('deals.deleteMulti');
    Route::patch('deals/{deal}/stage', [DealController::class, 'moveStage'])->name('deals.moveStage');
    Route::resource('deals', DealController::class);

    Route::post('quotes/from-deal/{deal}', [QuoteController::class, 'createFromDeal'])->name('quotes.from-deal');
    Route::post('quotes/{quote}/sent', [QuoteController::class, 'markSent'])->name('quotes.sent');
    Route::post('quotes/{quote}/void', [QuoteController::class, 'void'])->name('quotes.void');
    Route::get('quotes/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('quotes.pdf');
    Route::resource('quotes', QuoteController::class);

    Route::delete('subscriptions/deleteMulti', [SubscriptionController::class, 'deleteMulti'])->name('subscriptions.deleteMulti');
    Route::resource('subscriptions', SubscriptionController::class);

    Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    Route::delete('contact_forms', [ContactFormController::class, 'deleteMulti'])->name('contact_forms.deleteMulti');
    Route::get('contact_forms/export', [ContactFormController::class, 'export'])->name('contact_forms.export');
    Route::get('contact_forms/create', [ContactFormController::class, 'create'])->name('contact_forms.create');
    Route::post('contact_forms', [ContactFormController::class, 'store'])->name('contact_forms.store');
    Route::get('contact_forms/{contact_form}/edit', [ContactFormController::class, 'edit'])->name('contact_forms.edit');
    Route::patch('contact_forms/{contact_form}', [ContactFormController::class, 'update'])->name('contact_forms.update');
    Route::post('contact_forms/{contact_form}/convert-lead', [ContactFormController::class, 'convertToLead'])->name('contact_forms.convertLead');
    Route::post('contact_forms/{contact_form}/convert-contact', [ContactFormController::class, 'convertToContact'])->name('contact_forms.convertContact');
    Route::get('contact_forms', [ContactFormController::class, 'index'])->name('contact_forms.index');

    Route::delete('leads', [LeadController::class, 'deleteMulti'])->name('leads.deleteMulti');
    Route::get('leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::post('leads/{lead}/block', [LeadController::class, 'block'])->name('leads.block');
    Route::post('leads/{lead}/unblock', [LeadController::class, 'unblock'])->name('leads.unblock');
    Route::post('leads/{lead}/convert', [LeadController::class, 'convertToDeal'])->name('leads.convert');
    Route::post('leads/{lead}/convert-customer', [LeadController::class, 'convertToCustomer'])->name('leads.convertCustomer');
});
