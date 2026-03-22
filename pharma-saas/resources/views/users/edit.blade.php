@extends('layouts.app')

@section('title', __('edit') . ' ' . __('user'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('edit') }} {{ __('user') }}</h3>
    </div>
    
    <div class="p-6">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('name') }}</label>
                    <input type="text" name="name" value="{{ $user->name }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('email') }}</label>
                    <input type="email" name="email" value="{{ $user->email }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('phone') }}</label>
                    <input type="text" name="phone" value="{{ $user->phone }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('password') }} ({{ __('optional') }})</label>
                    <input type="password" name="password" minlength="8"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('roles') }}</label>
                    <select name="role" required class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="">{{ __('select') }} {{ __('role') }}</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('status') }}</label>
                    <select name="status" required class="w-full px-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>{{ __('active') }}</option>
                        <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>{{ __('inactive') }}</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
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
