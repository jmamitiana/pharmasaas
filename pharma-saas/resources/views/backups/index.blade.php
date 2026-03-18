@extends('layouts.app')

@section('title', __('backups'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">{{ __('backups') }}</h3>
        <form action="{{ route('backups.create') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                {{ __('add') }} {{ __('backups') }}
            </button>
        </form>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Filename</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('size') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('date') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('status') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($backups as $backup)
                    <tr>
                        <td class="px-4 py-3">{{ $backup->filename }}</td>
                        <td class="px-4 py-3">{{ number_format($backup->size / 1024, 2) }} KB</td>
                        <td class="px-4 py-3">{{ $backup->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            @if($backup->status === 'completed')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('completed') }}</span>
                            @elseif($backup->status === 'failed')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ __('failed') }}</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('pending') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($backup->status === 'completed')
                            <a href="{{ route('backups.download', $backup) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('download') }}</a>
                            <form action="{{ route('backups.restore', $backup) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 mr-2" onclick="return confirm('Are you sure?')">Restore</button>
                            </form>
                            @endif
                            <form action="{{ route('backups.destroy', $backup) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">{{ __('delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $backups->links() }}
        </div>
    </div>
</div>
@endsection
