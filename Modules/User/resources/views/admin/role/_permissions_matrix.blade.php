@php
    use Modules\User\Support\PermissionCatalog;

    $assigned = collect($assigned ?? []);
    $crudActions = PermissionCatalog::CRUD_ACTIONS;
    $selectAllId = $selectAllId ?? 'kt_roles_select_all';
@endphp

<div class="table-responsive permission-matrix">
    <table class="table align-middle table-row-dashed fs-6 gy-3">
        <tbody class="text-gray-700 fw-semibold">
        <tr>
            <td class="text-gray-800 min-w-200px">
                {{ __('user::permissions.ui.administrator_access') }}
                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                   title="{{ __('user::permissions.ui.administrator_access_hint') }}"></i>
            </td>
            <td>
                <label class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input js-permission-select-all" type="checkbox"
                           id="{{ $selectAllId }}"/>
                    <span class="form-check-label" for="{{ $selectAllId }}">
                        {{ __('user::permissions.ui.select_all') }}
                    </span>
                </label>
            </td>
        </tr>
        </tbody>
    </table>

    @foreach($groups as $group)
        @php
            $extraActions = collect($group['tabs'])
                ->pluck('actions')
                ->flatten()
                ->unique()
                ->reject(fn (string $action) => in_array($action, $crudActions, true))
                ->values();
            $columns = array_merge($crudActions, $extraActions->all());
            $sectionKeys = [];
            foreach ($group['tabs'] as $tab) {
                foreach ($tab['actions'] as $action) {
                    $sectionKeys[] = PermissionCatalog::key($tab['key'], $action);
                }
            }
        @endphp
        <div class="mb-8 permission-section" data-section="{{ $group['key'] }}">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="fw-bold mb-0">{{ PermissionCatalog::groupLabel($group['key']) }}</h4>
                <label class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input js-permission-section" type="checkbox"
                           data-section="{{ $group['key'] }}"/>
                    <span class="form-check-label">{{ __('user::permissions.ui.select_section') }}</span>
                </label>
            </div>
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle fs-6 gy-3">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-200px">{{ __('user::permissions.ui.tab') }}</th>
                        @foreach($columns as $column)
                            <th class="text-center min-w-80px">{{ PermissionCatalog::actionLabel($column) }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($group['tabs'] as $tab)
                        <tr>
                            <td class="text-gray-800">{{ PermissionCatalog::tabLabel($tab['key']) }}</td>
                            @foreach($columns as $column)
                                <td class="text-center">
                                    @if(in_array($column, $tab['actions'], true))
                                        @php $permissionKey = PermissionCatalog::key($tab['key'], $column); @endphp
                                        <label class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                                            <input class="form-check-input js-permission-box"
                                                   type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permissionKey }}"
                                                   data-section="{{ $group['key'] }}"
                                                   @checked($assigned->contains($permissionKey))/>
                                        </label>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
