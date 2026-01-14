@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">AI Summarize Test</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('ai.summarize_test.submit') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Text to summarize</label>
            <textarea name="text" rows="8" class="mt-1 block w-full" placeholder="Paste long text here">{{ old('text', $input_text ?? '') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Max length (words)</label>
                <input type="number" name="max_length" min="10" max="2000" value="{{ old('max_length', $max_length ?? 150) }}" class="mt-1 block w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Focus (optional)</label>
                <input type="text" name="focus" value="{{ old('focus', $focus ?? '') }}" class="mt-1 block w-full" placeholder="e.g., key facts, legal issues" />
            </div>
        </div>

        <div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">Summarize</button>
        </div>
    </form>

    @isset($summary)
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-2">Summary</h2>
            <div class="p-4 bg-gray-100 rounded">
                <pre class="whitespace-pre-wrap">{{ $summary }}</pre>
            </div>
        </div>
    @endisset
</div>
@endsection

