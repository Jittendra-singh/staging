@php 
    $pageName = "Permissions LOOL";

@endphp

@extends('layouts.backend' , ['pageName' => $pageName])

@section('content')
    <div class="content  mx-0 w-100">
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                <form action="{{ route('set-permission') }}" method="post">
                    @csrf
                    
                        <table class="table">
                            <thead>
                                <tr class="border-slate-500 table-light">
                                    <th
                                        class="whitespace-nowrap   px-4 py-3 bg-slate-300 font-semibold uppercase text-slate-800  lg:px-5 border-slate-500">
                                        #</th>
                                    @foreach ($roles as $role)
                                        <th
                                            class="whitespace-nowrap text-center px-4 bg-slate-300 py-3 font-semibold uppercase text-slate-800  lg:px-5 border-slate-500">
                                            {{ strtoupper($role['role_name']) }}
                                        </th>
                                    @endforeach
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($menus as $menu )

                                    @if(!$menu->menu_permissions)
                                        @continue
                                    @endif

                                    <tr class="border border-bottom table-light" >

                                        <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 bg-slate-200" 
                                            colspan="{{ count($menus) + 1 }}">
                                            {{ ucfirst($menu['menu_placeholder']) }}
                                        </th>
                                    </tr>

                                    @foreach ($menu->menu_permissions as $operation)
                                        <tr class="border border-bottom bg-white"> 

                                            <td class="whitespace-nowrap p-bg-slate-200 px-4 py-3">
                                                {{ config('app.menu_permissions')[$operation] ?? implode(',' , $menu->menu_permissions) }}
                                            </td>

                                            @foreach ($roles as $role)
                                                @php
                                                    // print_r($role);
                                                    $is_allowed = $role
                                                        ->permissions()
                                                        ->where('menu_id', '=', $menu->id)
                                                        ->where('permission_id', '=', $operation)
                                                        ->first();
                                                @endphp

                                                <td class="whitespace-nowrap p-bg-slate-200 px-4 py-3 text-center">
                                                    <input type="checkbox"
                                                        name="permission[{{ $role->id }}_{{ $menu->id }}_{{ $operation }}]"
                                                        value="1" {{ $is_allowed ? 'checked' : '' }} />
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    
                        <x-reusables.form-footer route="show-permission" action="update" module="permission"/>

                </form>
            </div>
        </div>
    </div>
@endsection