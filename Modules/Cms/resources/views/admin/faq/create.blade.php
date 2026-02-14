@section('title' , __('Add New FAQ'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'FAQs', 'url' => route('admin.faqs.index')],
            ['label' => 'Add New FAQ']
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Add New FAQ')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
    </div>
@endsection
@section('js')
    @include('base::shared._tinymce')
    <script>
        $(document).ready(function() {
            // Sync TinyMCE content before form submission
            $('form').on('submit', function(e) {
                if (tinymce.get('tinymce')) {
                    tinymce.get('tinymce').save();
                }
            });
        });
    </script>
@endsection
<x-admin-layout>
    <x-admin.create-card title="Add New FAQ" :formUrl="route('admin.faqs.store')">
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1 "></i>{{__('Question')}}
                    <span class="text-danger">*</span></div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="text" class="form-control form-control-solid" name="question" value="{{old('question')}}"
                       placeholder="{{__('Question')}}" required/>
            </div>
        </div>

        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1 "></i>{{__('Answer')}}
                    <span class="text-danger">*</span></div>
            </div>
            <div class="col-xl-9 fv-row">
                <textarea name="answer" class="form-control form-control-solid "
                          id="tinymce">{!! old('answer') !!}</textarea>
            </div>
        </div>

        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Rank')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="number" class="form-control form-control-solid @error('rank') is-invalid @enderror" name="rank" value="{{old('rank', $minRank)}}"
                       placeholder="{{__('Rank')}}" min="{{$minRank}}" required/>
                <div class="form-text">{{__('Lower numbers appear first. Minimum rank is :min.', ['min' => $minRank])}}</div>
                @error('rank')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Publish Status')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <div class="form-check form-switch form-check-custom form-check-solid me-10">
                    <input class="form-check-input h-30px w-50px"
                           @checked(old('publish') == 'Published') type="checkbox" name="publish"
                           id="flexSwitch30x50"/>
                </div>
            </div>
        </div>

    </x-admin.create-card>
</x-admin-layout>
