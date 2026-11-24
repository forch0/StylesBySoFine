@extends('layouts.storefront')

@section('content')
    @foreach($sections as $section)
        @includeIf('storefront.sections.'.$section->key, ['section' => $section, 'preset' => $preset])
    @endforeach
@endsection
