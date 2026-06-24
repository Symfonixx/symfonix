@php
    $editorName = $name ?? 'content';
    $editorId = $id ?? 'tinymce';
    $editorValue = $value ?? '';
    $editorLabel = $label ?? 'Content';
    $editorRequired = $required ?? true;
    $editorRows = $rows ?? 12;
@endphp

<div class="row mb-0">
    <div class="col-lg-4">
        <label class="settings-field-label" for="{{ $editorId }}">
            <i class="bi bi-body-text text-primary me-1"></i>{{ __($editorLabel) }}
            @if($editorRequired)<span class="text-danger">*</span>@endif
        </label>
    </div>
    <div class="col-lg-8">
        <textarea name="{{ $editorName }}" class="form-control cms-tinymce-editor" id="{{ $editorId }}" rows="{{ $editorRows }}">{!! $editorValue !!}</textarea>
    </div>
</div>
