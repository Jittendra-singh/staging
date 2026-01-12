@php
    $pageName = 'Permissions';
@endphp

@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])

@section('content')
    <div class="row">
        <div class="col-12">
            <x-reusables.frontend.alert />
            <form action="{{ route('set-permission') }}" method="post">
                @csrf



                {{-- <div class="accordion-item mb-3 border">
                    <h2 class="accordion-header" id="heading{{ $menuIndex }}">
                        <button class="accordion-button {{ $menuIndex !== 0 ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $menuIndex }}"
                            aria-expanded="{{ $menuIndex === 0 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $menuIndex }}">
                            {{ ucfirst($menu['menu_placeholder']) }}
                        </button>
                    </h2>
                    <div id="collapse{{ $menuIndex }}" class="accordion-collapse collapse show"
                        aria-labelledby="heading{{ $menuIndex }}"> --}}

                {{-- <div class="accordion-body p-3"> --}}
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr class="table-light bg-primary text-white">
                                <th class="bg-primary text-white">Menus</th>
                                @foreach ($roles as $role)
                                    <th class="bg-primary text-white px-3 py-2">
                                        {{ ucfirst($role['role_name']) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($menus as $menuIndex => $menu)
                                @if (!$menu->menu_permissions)
                                    @continue
                                @endif
                                <tr class="border border-bottom table-light">
                                    <th class="whitespace-nowrap font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 bg-slate-200"
                                        colspan="{{ count($menus) + 1 }}">
                                        {{ ucfirst($menu['menu_placeholder']) }}
                                    </th>
                                </tr>
                                @foreach ($menu->menu_permissions as $operation)
                                    <tr>
                                        <td class="">
                                            {{ config('app.menu_permissions')[$operation] ?? implode(',', $menu->menu_permissions) }}
                                        </td>
                                        @foreach ($roles as $role)
                                            @php
                                                $is_allowed = $role
                                                    ->permissions()
                                                    ->where('menu_id', '=', $menu->id)
                                                    ->where('permission_id', '=', $operation)
                                                    ->first();
                                                $inputName = $role->id.'_'.$menu->id.'_'.$operation;
                                            @endphp
                                            <td class="text-start">
                                                <div class="square-switch">
                                                    {{-- <input url="{{ $url }}" class="statusswitch" 
                                                        type="checkbox" id="switch{{$data->id}}" switch="none" {{$data->$fieldName ? 'checked' : ''}} /> --}}
                                                        <input type="checkbox" id="switch{{$inputName}}" switch="none"
                                                            name="permission[{{$inputName}}]" value="1" @checked($is_allowed) />
                                                    <label for="switch{{$inputName}}" data-on-label="On" data-off-label="Off"></label>
                                                </div>

                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- </div>
                    </div>
                </div> --}}
        </div>

        <x-reusables.form-footer route="show-permission" action="update" module="permission" />
        </form>
    </div>
    </div>
@endsection
