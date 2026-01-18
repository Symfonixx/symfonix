@section('title' , __('Team'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Team'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Teams')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-primary" href="{{route('admin.teams.create')}}">
            {{__('Add New Team Member')}} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </div>
@endsection
@section('js')
@endsection
<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Team" :form-url="route('admin.teams.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th class="min-w-100px">{{__('Avatar')}}</th>
            <th class="min-w-100px">{{__('Name')}}/{{__('Position')}}</th>
            <th class="min-w-100px">{{__('Published')}}</th>
            <th class="min-w-100px">{{__('Created At')}}</th>
            <th class="min-w-100px text-end rounded-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $team)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{$team->id}}"/>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <a href="{{$team->avatar_link}}" target="_blank"  >
                            <img src="{{$team->avatar_link}}" alt="{{$team->name}}" class="img-fluid h-100px"/>
                        </a>
                    </div>
                </td>
                <td>{{$team->name}} <br/>
                    {{$team->position}}</td>
                <td>
                    <span class="badge badge-light-{{$team->status == 'Published' ? 'success' : 'warning'}} fs-7 fw-bold">
                        {{__($team->status)}}
                    </span>
                </td>
                <td>{{$team->created_at->diffForHumans() }}</td>
                <td>
                    <a href="{{route('admin.teams.edit', $team->id)}}"
                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                        <i class="ki-duotone ki-message-edit fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
