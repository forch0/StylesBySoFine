@extends('layouts.storefront')

@section('title', ($page->meta_title ?: $page->title).' — '.($settings['store_name'] ?? ''))
@section('meta_description', $page->meta_description ?? '')

@section('content')
<div class="section-pad py-12 lg:py-16">
    <div class="max-w-3xl mx-auto">
        <h1 class="heading-display mb-8">{{ $page->title }}</h1>
        <div class="text-neutral-700 leading-relaxed space-y-4 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:mt-8 [&_h2]:mb-3 [&_h3]:font-semibold [&_h3]:mt-6 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_a]:text-wine-700 [&_a]:underline">
            {!! nl2br(e($page->content)) !!}
        </div>
        @includeIf('storefront.partials.'.$page->slug)
    </div>
</div>
@endsection
