@php($faqData = $faq ?? null)

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('Please fix the following errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row g-6">
    <div class="col-xl-8">
        <x-admin.settings-section
            icon="bi-question-circle"
            title="FAQ Content"
            description="Question and answer shown on the FAQ page."
        >
            <div class="row mb-6">
                <div class="col-lg-4">
                    <label class="settings-field-label" for="question">
                        <i class="bi bi-chat-left-quote text-primary me-1"></i>{{ __('Question') }}
                        <span class="text-danger">*</span>
                    </label>
                </div>
                <div class="col-lg-8">
                    <input id="question" type="text" class="form-control form-control-solid" name="question"
                           value="{{ old('question', $faqData?->question) }}" placeholder="{{ __('Question') }}" required autofocus/>
                </div>
            </div>

            @include('cms::admin.shared._editor', [
                'name' => 'answer',
                'label' => 'Answer',
                'value' => old('answer', $faqData?->answer),
            ])
        </x-admin.settings-section>
    </div>

    <div class="col-xl-4">
        <div class="seo-preview-panel">
            <div class="card mb-6">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-5">
                        <i class="bi bi-sort-numeric-down text-primary me-2"></i>{{ __('Display Order') }}
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <label class="form-label fw-semibold" for="rank">{{ __('Rank') }}</label>
                    <input id="rank" type="number" class="form-control form-control-solid @error('rank') is-invalid @enderror"
                           name="rank" value="{{ old('rank', $faqData?->rank ?? $minRank) }}"
                           placeholder="{{ __('Rank') }}" min="{{ $faqData ? min($faqData->rank, $minRank) : $minRank }}" required/>
                    <div class="form-text">{{ __('Lower numbers appear first. Minimum rank is :min.', ['min' => $faqData ? min($faqData->rank, $minRank) : $minRank]) }}</div>
                    @error('rank')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @include('cms::admin.shared._publish-aside', [
                'item' => $faqData,
                'showFeatured' => false,
            ])

            <div class="card mb-6">
                <div class="card-body">
                    <x-admin.auto-translate-checkbox :default="! $faqData" class="mb-0"/>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-5">
                        <i class="bi bi-eye text-info me-2"></i>{{ __('Preview') }}
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="fw-bold text-gray-900 mb-2" id="faq-preview-question">{{ old('question', $faqData?->question) ?: __('Your question will appear here') }}</div>
                    <div class="text-muted fs-7" id="faq-preview-answer">{{ Str::limit(strip_tags(old('answer', $faqData?->answer ?? '')), 120) ?: __('Answer preview...') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('cms::admin.shared._form-scripts')

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('faq-form');
        if (!form) return;

        const questionInput = form.querySelector('[name="question"]');
        const previewQuestion = document.getElementById('faq-preview-question');
        const previewAnswer = document.getElementById('faq-preview-answer');

        questionInput?.addEventListener('input', function () {
            if (previewQuestion) {
                previewQuestion.textContent = this.value.trim() || @json(__('Your question will appear here'));
            }
        });
    })();
</script>
@endpush
