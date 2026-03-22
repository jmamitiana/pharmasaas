@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ $user->name }}</h3>
        <div class="flex space-x-2">
            <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                {{ __('edit') }}
            </a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">
                {{ __('back') }}
            </a>
        </div>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">{{ __('information') }}</h4>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('name') }}:</dt>
                        <dd class="font-medium">{{ $user->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('email') }}:</dt>
                        <dd class="font-medium">{{ $user->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('phone') }}:</dt>
                        <dd class="font-medium">{{ $user->phone ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('status') }}:</dt>
                        <dd>
                            @if($user->status === 'active')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('active') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('inactive') }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">{{ __('roles') }}</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($user->roles as $role)
                    <span class="px-3 py-1 bg-primary-100 text-primary-800 rounded-full">{{ $role->name }}</span>
                    @endforeach
                </div>
                
                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2 mt-6">{{ __('permissions') }}</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($user->getAllPermissions() as $permission)
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">{{ $permission->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-gray-800">{{ $user->sales->count() }}</p>
                <p class="text-sm text-gray-500">{{ __('sales') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-gray-800">{{ $user->purchases->count() }}</p>
                <p class="text-sm text-gray-500">{{ __('purchases') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-gray-800">{{ $user->transfers->count() }}</p>
                <p class="text-sm text-gray-500">{{ __('transfers') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
