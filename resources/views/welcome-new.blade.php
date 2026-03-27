@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">User Management System</h1>
        <p class="text-gray-600 text-lg mb-8">A comprehensive Laravel application for managing users with roles, authorization, and audit logging.</p>
        
        @auth
            <div class="space-y-4">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition">
                        Manage Users
                    </a>
                @else
                    <p class="text-gray-500">Welcome, {{ auth()->user()->name }}!</p>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                            Logout
                        </button>
                    </form>
                @endif
            </div>
        @else
            <a href="{{ route('login') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition">
                Login to Get Started
            </a>
        @endauth

        <div class="mt-12 grid grid-cols-3 gap-4 text-sm">
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl mb-2">👥</div>
                <p class="text-gray-700 font-semibold">User CRUD</p>
                <p class="text-gray-600 text-xs mt-1">Create, read, update, delete</p>
            </div>
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl mb-2">🔒</div>
                <p class="text-gray-700 font-semibold">Authorization</p>
                <p class="text-gray-600 text-xs mt-1">Policies & gates</p>
            </div>
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl mb-2">📋</div>
                <p class="text-gray-700 font-semibold">Audit Logs</p>
                <p class="text-gray-600 text-xs mt-1">Track all changes</p>
            </div>
        </div>
    </div>
</div>
@endsection
