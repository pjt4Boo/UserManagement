@extends('layouts.app')

@section('title', 'Audit Logs: ' . $user->name)

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Audit Logs</h1>
        <p class="text-gray-600 mt-1">Activity log for <span class="font-semibold">{{ $user->name }}</span></p>
    </div>

    {{-- Audit Logs Table --}}
    @if($auditLogs->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Action
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Actor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Target User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Changes
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($auditLogs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $log->created_at->format('M d, Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 
                                        @if($log->action === 'created')
                                            bg-green-100 text-green-800
                                        @elseif($log->action === 'updated')
                                            bg-blue-100 text-blue-800
                                        @elseif($log->action === 'deleted')
                                            bg-red-100 text-red-800
                                        @elseif($log->action === 'force_deleted')
                                            bg-red-200 text-red-900
                                        @elseif($log->action === 'deactivated')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                        text-xs font-semibold rounded-full">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($log->actor)
                                        <a href="{{ route('users.show', $log->actor) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ $log->actor->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-500 italic">System</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($log->user)
                                        <a href="{{ route('users.show', $log->user) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ $log->user->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-500 italic">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($log->changes && is_array($log->changes))
                                        <details class="cursor-pointer">
                                            <summary class="text-blue-600 hover:text-blue-800 font-medium">
                                                View Changes
                                            </summary>
                                            <div class="mt-2 p-3 bg-gray-50 rounded text-xs font-mono whitespace-pre-wrap">
                                                @forelse($log->changes as $field => $change)
                                                    @if(is_array($change) && isset($change['old']) && isset($change['new']))
                                                        <div class="mb-2">
                                                            <span class="font-semibold">{{ $field }}:</span>
                                                            <div class="text-red-600">- {{ $change['old'] ?? 'null' }}</div>
                                                            <div class="text-green-600">+ {{ $change['new'] ?? 'null' }}</div>
                                                        </div>
                                                    @endif
                                                @empty
                                                    <span class="text-gray-500">No changes recorded</span>
                                                @endforelse
                                            </div>
                                        </details>
                                    @else
                                        <span class="text-gray-500 italic">No changes</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $auditLogs->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <div class="text-gray-500 mb-4">
                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-lg font-medium text-gray-700 mb-2">No audit logs found</p>
            <p class="text-gray-500">This user hasn't had any recorded activities yet.</p>
        </div>
    @endif

    {{-- Back Link --}}
    <div class="mt-6">
        <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800 font-medium transition">
            ← Back to {{ $user->name }}
        </a>
    </div>
</div>
@endsection
