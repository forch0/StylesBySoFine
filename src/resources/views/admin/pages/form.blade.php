@extends('layouts.admin')

@section('title', $page->exists ? 'Edit: '.$page->title : 'New page')

@section('content')
<form action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" method="POST" class="max-w-3xl space-y-5">
    @csrf
    @if($page->exists) @method('PUT') @endif

    <div class="card p-5 space-y-4">
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">Title *</label><input name="title" value="{{ old('title', $page->title) }}" class="input" required></div>
            <div><label class="label">Slug</label><input name="slug" value="{{ old('slug', $page->slug) }}" class="input" placeholder="auto"></div>
        </div>
        <div>
            <label class="label">Content</label>
            <textarea name="content" rows="14" class="input">{{ old('content', $page->content) }}</textarea>
            <p class="text-xs text-neutral-500 mt-1">Plain text — blank lines become paragraphs.</p>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">Meta title</label><input name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="input"></div>
            <div><label class="label">Meta description</label><input name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" class="input"></div>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published ?? true) ? 'checked' : '' }} class="accent-wine-700 w-4 h-4"> Published
        </label>
    </div>

    <button class="btn-primary">{{ $page->exists ? 'Save page' : 'Create page' }}</button>
</form>
@endsection
