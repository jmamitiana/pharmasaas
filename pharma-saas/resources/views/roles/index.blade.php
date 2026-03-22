@extends('layouts.app')

@section('title', __('roles'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('roles') }}</h3>
        <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
            {{ __('add') }} {{ __('role') }}
        </a>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('name') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('permissions') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('users') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($roles as $role)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $role->name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions as $permission)
                                <span class="px-2 py-1 text-xs bg-gray-100 rounded">{{ $permission->name }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ $role->users->count() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <a href="{{ route('roles.show', $role) }}" class="text-primary-600 hover:text-primary-900">{{ __('view') }}</a>
                                <a href="{{ route('roles.edit', $role) }}" class="text-blue-600 hover:text-blue-900">{{ __('edit') }}</a>
                                @if($role->name !== 'admin')
                                <form method="POST" action="{{ route('roles.destroy', $role) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('confirm_delete') }}')">
                                        {{ __('delete') }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection
