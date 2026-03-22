@extends('layouts.app')

@section('title', $role->name)

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ $role->name }}</h3>
        <div class="flex space-x-2">
            <a href="{{ route('roles.edit', $role) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                {{ __('edit') }}
            </a>
            <a href="{{ route('roles.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">
                {{ __('back') }}
            </a>
        </div>
    </div>
    
    <div class="p-6">
        <h4 class="text-sm font-medium text-gray-500 uppercase mb-4">{{ __('permissions') }}</h4>
        <div class="flex flex-wrap gap-2">
            @forelse($role->permissions as $permission)
            <span class="px-3 py-1 bg-primary-100 text-primary-800 rounded-full">{{ $permission->name }}</span>
            @empty
            <p class="text-gray-500">{{ __('no_permissions') }}</p>
            @endforelse
        </div>
        
        <h4 class="text-sm font-medium text-gray-500 uppercase mb-4 mt-8">{{ __('users') }}</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('name') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('email') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($role->users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-center text-gray-500">{{ __('no_users') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
