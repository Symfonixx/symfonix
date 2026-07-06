@section('title', __('Admin Details'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('Admins'), 'url' => route('admin.admins.index')],
            ['label' => $admin->name],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="$admin->name" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.admins.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to List') }}
        </a>
        <a class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#edit_modal{{ $admin->id }}">
            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
        </a>
        <div class="modal fade" tabindex="-1" id="edit_modal{{ $admin->id }}">
            @include('user::admin.admin._edit_model', ['user' => $admin])
        </div>
    </div>
@endsection

<x-admin-layout>
    <div class="d-flex flex-column flex-xl-row gap-6">
        <div class="flex-column flex-lg-row-auto w-100 w-xl-300px">
            <div class="card card-flush">
                <div class="card-body text-center pt-10 pb-6">
                    <div class="symbol symbol-100px symbol-circle mb-5 mx-auto">
                        <img src="{{ $admin->avatar }}" alt="{{ $admin->name }}"/>
                    </div>
                    <h2 class="fs-2 fw-bold mb-1">{{ $admin->name }}</h2>
                    <a href="mailto:{{ $admin->email }}" class="text-muted text-hover-primary d-block mb-4">
                        {{ $admin->email }}
                    </a>
                    @if($admin->mobile)
                        <a href="tel:{{ $admin->mobile }}" class="text-muted text-hover-primary d-block mb-4">
                            {{ $admin->mobile }}
                        </a>
                    @endif
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <span class="badge badge-light-primary">{{ __('Admin') }}</span>
                    </div>
                </div>
                <div class="card-body border-top pt-6">
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">{{ __('Last Login') }}</span>
                        <span class="fw-semibold">{{ $admin->last_login_human ?? __('N/A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">{{ __('Created At') }}</span>
                        <span class="fw-semibold">{{ $admin->created_at?->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Total Events') }}</span>
                        <span class="fw-semibold">{{ $eventTracks->total() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-lg-row-fluid">
            <div class="card card-flush">
                <div class="card-header align-items-center">
                    <h3 class="card-title">{{ __('Activity Log') }}</h3>
                </div>
                <div class="card-body">
                    @forelse($eventTracks as $track)
                        @php
                            $badgeColor = match($track->event) {
                                'created' => 'success',
                                'deleted' => 'danger',
                                'updated' => 'warning',
                                'viewed' => 'info',
                                default => 'secondary',
                            };
                            $icon = match($track->event) {
                                'created' => 'plus-circle',
                                'deleted' => 'trash',
                                'updated' => 'pencil-square',
                                'viewed' => 'eye',
                                default => 'clock-history',
                            };
                        @endphp
                        <div class="d-flex align-items-start mb-6 pb-6 border-bottom border-gray-200">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-{{ $badgeColor }}">
                                    <i class="bi bi-{{ $icon }} text-{{ $badgeColor }}"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="badge badge-light-{{ $badgeColor }}">
                                        {{ __('user::event_tracks.events.'.$track->event) }}
                                    </span>
                                    <span class="fw-semibold text-gray-800">{{ $track->description }}</span>
                                </div>

                                @if($track->subject_label)
                                    <div class="text-gray-700 fs-7 mb-1">
                                        <span class="text-muted">{{ __('Subject') }}:</span>
                                        {{ $track->subject_label }}
                                    </div>
                                @endif

                                <div class="text-muted fs-7">
                                    {{ $track->created_at->diffForHumans() }}
                                    · {{ $track->created_at->format('Y-m-d H:i') }}
                                    @if($track->route_name)
                                        · <span class="text-muted">{{ $track->route_name }}</span>
                                    @endif
                                    @if($track->ip_address)
                                        · {{ $track->ip_address }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No activity recorded yet.') }}</p>
                    @endforelse

                    @if($eventTracks->hasPages())
                        <div class="mt-6">
                            {{ $eventTracks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
