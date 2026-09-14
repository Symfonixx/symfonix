@php
    $subjectType = $subjectType ?? \Modules\CRM\Support\CrmSubjectResolver::typeFromModel($subject);
    $timeline = $subject->crmTimeline();
@endphp

<div class="card mt-6">
    <div class="card-header align-items-center">
        <h3 class="card-title">
            <span class="sx-form-icon bg-light-info text-info me-3">
                <i class="bi bi-clock-history"></i>
            </span>
            {{ __('crm::timeline.title') }}
        </h3>
    </div>
    <div class="card-body">
        @can('crm.activities.create')
            <form method="POST" action="{{ route('admin.activities.store') }}" class="mb-8 border border-dashed rounded p-5">
                @csrf
                <input type="hidden" name="subject_type" value="{{ $subjectType }}"/>
                <input type="hidden" name="subject_id" value="{{ $subject->getKey() }}"/>
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-plus-circle me-1 text-primary"></i>{{ __('crm::timeline.add_activity') }}
                </h5>
                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label required">{{ __('crm::timeline.fields.type') }}</label>
                        <select name="type" class="form-select form-select-solid" required>
                            @foreach(\Modules\CRM\Models\CrmActivity::TYPES as $type)
                                <option value="{{ $type }}">{{ __('crm::timeline.activity_types.'.$type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('crm::timeline.fields.scheduled_at') }}</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control form-control-solid"/>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('crm::timeline.fields.title') }}</label>
                        <input type="text" name="title" class="form-control form-control-solid"
                               placeholder="{{ __('crm::timeline.placeholders.title') }}" maxlength="255"/>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('crm::timeline.fields.body') }}</label>
                        <textarea name="body" class="form-control form-control-solid" rows="3"
                                  placeholder="{{ __('crm::timeline.placeholders.body') }}"></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('crm::timeline.add_activity') }}
                    </button>
                </div>
            </form>
        @endcan

        <div class="crm-timeline">
        @forelse($timeline as $entry)
            @php
                $item = $entry['item'];
                $isActivity = $entry['kind'] === 'activity';
                $icon = $isActivity ? match($item->type) {
                    'call' => 'telephone',
                    'meeting' => 'people',
                    'task' => 'check2-square',
                    'email' => 'envelope',
                    default => 'journal-text',
                } : match($item->event) {
                    'created' => 'plus-circle',
                    'deleted' => 'trash',
                    'stage_changed' => 'arrow-right-circle',
                    'converted' => 'arrow-repeat',
                    default => 'clock-history',
                };
                $badgeColor = $isActivity ? 'primary' : match($item->event) {
                    'created', 'converted' => 'success',
                    'deleted', 'activity_removed' => 'danger',
                    'stage_changed' => 'info',
                    default => 'secondary',
                };
            @endphp
            <div class="crm-timeline-item">
                <div class="crm-timeline-rail">
                    <span class="crm-timeline-dot bg-light-{{ $badgeColor }} text-{{ $badgeColor }}">
                        <i class="bi bi-{{ $icon }}"></i>
                    </span>
                </div>
                <div class="crm-timeline-card">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            @if($isActivity)
                                <span class="badge badge-light-{{ $badgeColor }} me-2">
                                    {{ __('crm::timeline.activity_types.'.$item->type) }}
                                </span>
                                @if($item->title)
                                    <span class="fw-bold text-gray-900">{{ $item->title }}</span>
                                @endif
                            @else
                                <span class="badge badge-light-{{ $badgeColor }} me-2">
                                    {{ __('crm::timeline.events.'.$item->event) }}
                                </span>
                                <span class="fw-semibold text-gray-800">{{ $item->description }}</span>
                            @endif
                        </div>
                        @if($isActivity)
                            @can('crm.activities.delete')
                                <form method="POST" action="{{ route('admin.activities.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-sm btn-light-danger"
                                            title="{{ __('crm::timeline.delete_activity') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>

                    @if($isActivity && $item->body)
                        <div class="text-gray-700 mt-2">{{ $item->body }}</div>
                    @endif

                    @if(! $isActivity && ($item->old_values || $item->new_values))
                        <div class="mt-3 p-3 bg-light rounded fs-7">
                            <div class="fw-semibold mb-2">{{ __('crm::timeline.fields_changed') }}</div>
                            @foreach(($item->new_values ?? []) as $field => $newValue)
                                @php
                                    $fieldKey = 'crm::timeline.audit_fields.'.$field;
                                    $fieldLabel = \Illuminate\Support\Facades\Lang::has($fieldKey)
                                        ? __($fieldKey)
                                        : \Illuminate\Support\Str::of($field)->replace('_', ' ')->title();
                                    $oldDisplay = $item->old_values[$field] ?? null;
                                @endphp
                                <div class="mb-1">
                                    <span class="fw-bold text-gray-800">{{ $fieldLabel }}</span>:
                                    <span class="text-muted">{{ is_array($oldDisplay) ? json_encode($oldDisplay) : ($oldDisplay ?? '—') }}</span>
                                    →
                                    <span class="fw-semibold">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="text-muted fs-7 mt-2">
                        {{ $entry['occurred_at']->diffForHumans() }}
                        · {{ $item->user?->name ?? __('crm::timeline.system') }}
                        @if($isActivity && $item->scheduled_at)
                            · {{ __('crm::timeline.fields.scheduled_at') }}: {{ $item->scheduled_at->format('Y-m-d H:i') }}
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <span class="crm-empty-icon bg-light-primary text-primary mb-3">
                    <i class="bi bi-clock-history fs-2"></i>
                </span>
                <p class="text-muted mb-0">{{ __('crm::timeline.no_entries') }}</p>
            </div>
        @endforelse
        </div>
    </div>
</div>
