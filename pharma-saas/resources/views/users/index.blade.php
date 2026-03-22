@extends('layouts.app')

@section('title', __('users'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('users') }}</h3>
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
            {{ __('add') }} {{ __('user') }}
        </a>
    </div>
    
    <div class="p-6">
        <form method="GET" class="mb-4 flex gap-4">
            <input type="text" name="search" placeholder="{{ __('search') }}..." 
                   class="px-4 py-2 border rounded-lg w-full max-w-md"
                   value="{{ request('search') }}">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                {{ __('search') }}
            </button>
        </form>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('name') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('email') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('phone') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('roles') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->phone ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @foreach($user->roles as $role)
                                <span class="px-2 py-1 text-xs rounded bg-gray-100">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3">
                            @if($user->status === 'active')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('active') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <a href="{{ route('users.show', $user) }}" class="text-primary-600 hover:text-primary-900">{{ __('view') }}</a>
                                <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-900">{{ __('edit') }}</a>
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('confirm_delete') }}')">
                                        {{ __('delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
