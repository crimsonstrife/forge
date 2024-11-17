@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="mb-4 text-2xl font-bold">Report Templates</h1>

        <!-- Create New Template Button -->
        <div class="mb-4">
            <a href="{{ route('templates.reports.create') }}" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Create New Template
            </a>
        </div>

        <!-- List of Report Templates -->
        @if ($templates->isEmpty())
            <p class="text-gray-600">No templates found. Create a new template to get started.</p>
        @else
            <table class="w-full bg-white rounded-lg shadow table-auto">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Created At</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($templates as $template)
                        <tr>
                            <td class="px-4 py-2 border">{{ $template->name }}</td>
                            <td class="px-4 py-2 border">{{ $template->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 space-x-2 border">
                                <a href="{{ route('templates.reports.show', $template->id) }}" class="text-blue-500 hover:underline">View</a>
                                <a href="{{ route('templates.reports.edit', $template->id) }}" class="text-green-500 hover:underline">Edit</a>
                                <form action="{{ route('templates.reports.destroy', $template->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
