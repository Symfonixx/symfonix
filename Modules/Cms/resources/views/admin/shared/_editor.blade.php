@php
    $editorName = $name ?? 'content';
    $editorId = $id ?? 'tinymce';
    $editorValue = $value ?? '';
    $editorLabel = $label ?? 'Content';
    $editorRequired = $required ?? true;
    $editorRows = $rows ?? 16;
@endphp

<div class="cms-editor-field mb-0">
    <label class="settings-field-label d-flex align-items-center mb-3" for="{{ $editorId }}">
        <i class="bi bi-body-text text-primary me-1"></i>{{ __($editorLabel) }}
        @if($editorRequired)<span class="text-danger ms-1">*</span>@endif
    </label>
    <div class="cms-editor-wrap">
        <textarea name="{{ $editorName }}" class="form-control cms-tinymce-editor" id="{{ $editorId }}" rows="{{ $editorRows }}">{!! $editorValue !!}</textarea>
    </div>
</div>
