@extends('layouts.app')

@section('title', 'User: ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Page Header --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-gray-600 mt-1">User Details & Management</p>
        </div>
        <div class="flex gap-2">
            @can('update', $user)
                <a href="{{ route('users.edit', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Edit
                </a>
            @endcan
            @can('delete', $user)
                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Main User Info --}}
        <div class="md:col-span-2 space-y-6">
            {{-- User Information Card --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">User Information</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Name</p>
                            <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Email</p>
                            <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Role</p>
                            <span class="inline-block mt-1 px-3 py-1 {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }} text-xs font-semibold rounded-full">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Status</p>
                            <span class="inline-block mt-1 px-3 py-1 {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-semibold rounded-full">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Created At</p>
                            <p class="text-gray-900 font-medium">{{ $user->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Last Updated</p>
                            <p class="text-gray-900 font-medium">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @if($user->deleted_at)
                        <div>
                            <p class="text-gray-600 text-sm">Deleted At</p>
                            <p class="text-red-600 font-medium">{{ $user->deleted_at->format('M d, Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="flex flex-wrap gap-2">
                    @if($user->isActive())
                        @can('deactivate', $user)
                            <form method="POST" action="{{ route('users.deactivate', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                    Deactivate
                                </button>
                            </form>
                        @endcan
                    @else
                        @can('deactivate', $user)
                            <form method="POST" action="{{ route('users.activate', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                    Activate
                                </button>
                            </form>
                        @endcan
                    @endif
                    
                    @can('view', App\Models\AuditLog::class)
                        <a href="{{ route('users.audit-logs', $user) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            View Audit Logs
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Overview</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="text-gray-600">Email Verified</span>
                        <span class="text-sm font-semibold {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->email_verified_at ? '✓ Yes' : '✗ No' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="text-gray-600">Active</span>
                        <span class="text-sm font-semibold {{ $user->isActive() ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->isActive() ? '✓ Yes' : '✗ No' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="text-gray-600">Administrator</span>
                        <span class="text-sm font-semibold {{ $user->isAdmin() ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->isAdmin() ? '✓ Yes' : '✗ No' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Help Card --}}
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Need help?</span> Contact an administrator for additional user management options.
                </p>
            </div>
        </div>
    </div>

    {{-- Back Link --}}
    <div class="mt-6">
        <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800 font-medium transition">
            ← Back to Users
        </a>
    </div>
</div>
@endsection
