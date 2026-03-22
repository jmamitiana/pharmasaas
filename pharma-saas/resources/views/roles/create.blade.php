@extends('layouts.app')

@section('title', __('add') . ' ' . __('role'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('add') }} {{ __('role') }}</h3>
    </div>
    
    <div class="p-6">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('name') }}</label>
                <input type="text" name="name" required
                       class="w-full max-w-md px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('permissions') }}</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-w-3xl">
                    @foreach($permissions as $permission)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm">{{ $permission->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('roles.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    {{ __('cancel') }}
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                    {{ __('save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
