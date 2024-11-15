@extends('layouts.app')

@section('contextual-navigation')
    <div class="bg-gray-100">
        <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <nav class="space-x-4">
                <a href="{{ route('dashboards.manage') }}" class="text-blue-500">Manage Dashboards</a>
                <a href="{{ route('dashboards.create') }}" class="text-blue-500">Create Dashboard</a>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="container mx-auto">
        <x-slot name="header">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Manage Dashboards') }}
            </h2>
        </x-slot>
        <!-- Manage Dashboards Content -->
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold">{{ __('Your Dashboards') }}</h3>

                        <ul class="mt-4 list-disc list-inside">
                            @forelse ($dashboards as $dashboard)
                                <li class="flex items-center justify-between">
                                    <span>{{ $dashboard->name }}</span>
                                    <a href="{{ route('dashboards.show', $dashboard->id) }}"
                                        class="text-blue-500 hover:underline">
                                        {{ __('View') }}
                                    </a>
                                </li>
                            @empty
                                <p>{{ __('You have no dashboards yet. Create one below!') }}</p>
                            @endforelse
                        </ul>

                        <div class="mt-6">
                            <a href="{{ route('dashboards.create') }}"
                                class="px-4 py-2 text-sm bg-blue-500 rounded hover:bg-blue-700">
                                {{ __('Create New Dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
