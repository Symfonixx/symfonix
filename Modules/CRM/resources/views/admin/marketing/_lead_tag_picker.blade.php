@php
    $leadTags = $leadTags ?? collect();
    $selectedTagIds = collect(old('lead_tag_ids', []))->map(fn ($id) => (int) $id)->all();
@endphp

@if($leadTags->isNotEmpty())
    <fieldset id="marketing-lead-tag-picker" class="mt-6 border-0 p-0 m-0 min-w-100">
        <label class="form-label fw-semibold">{{ __('crm::marketing.fields.lead_tags') }}</label>
        <p class="text-muted fs-7 mb-3">{{ __('crm::marketing.hints.lead_tags') }}</p>
        <div class="d-flex flex-wrap gap-2">
            @foreach($leadTags as $tag)
                @php($isSelected = in_array((int) $tag->id, $selectedTagIds, true))
                <input type="checkbox"
                       class="btn-check"
                       name="lead_tag_ids[]"
                       id="marketing_lead_tag_{{ $tag->id }}"
                       value="{{ $tag->id }}"
                       autocomplete="off"
                       @checked($isSelected)>
                <label for="marketing_lead_tag_{{ $tag->id }}"
                       class="btn btn-sm border {{ $isSelected ? 'btn-'.$tag->color : 'btn-light-'.$tag->color }}">
                    <i class="bi bi-tag-fill me-1"></i>{{ $tag->display_name }}
                    <span class="badge badge-light ms-1">{{ $tag->recipient_count ?? 0 }}</span>
                </label>
            @endforeach
        </div>
        @error('lead_tag_ids')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </fieldset>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var allLeads = document.getElementById('all_leads');
        var picker = document.getElementById('marketing-lead-tag-picker');
        var leadSelect = document.getElementById('lead_ids');
        if (!allLeads) {
            return;
        }

        function syncLeadTargeting() {
            var disabled = allLeads.checked;
            if (picker) {
                picker.disabled = disabled;
                picker.classList.toggle('opacity-50', disabled);
            }
            if (leadSelect) {
                leadSelect.disabled = disabled;
                if (window.jQuery && jQuery.fn.select2) {
                    jQuery(leadSelect).prop('disabled', disabled).trigger('change.select2');
                }
            }
        }

        allLeads.addEventListener('change', syncLeadTargeting);
        syncLeadTargeting();
    });
</script>
