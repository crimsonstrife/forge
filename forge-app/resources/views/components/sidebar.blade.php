<!-- resources/views/components/sidebar.blade.php -->
<div class="flex flex-col w-64 h-full text-white bg-gray-800">
    <div class="px-4 py-2">
        <!-- Logo -->
        <div class="flex items-center shrink-0">
            <a href="{{ route('dashboard') }}">
                <x-application-mark class="block w-auto h-9" />
            </a>
        </div>
    </div>
    <nav class="mt-4">
        <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">Dashboard</x-sidebar-link>
        <x-sidebar-link href="{{ route('projects.index') }}" :active="request()->routeIs('projects.index')">Projects</x-sidebar-link>
        <x-sidebar-link href="{{ route('issues.index') }}" :active="request()->routeIs('issues.index')">Issues</x-sidebar-link>
    </nav>
    <div class="px-4 py-2 mt-auto">
        <x-sidebar-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">Profile</x-sidebar-link>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full px-4 py-2 text-left">Logout</button>
        </form>
    </div>
</div>
