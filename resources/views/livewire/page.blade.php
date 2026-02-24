@extends('components.layouts.app')

@section('content')
<h1>{{ $page->title }}</h1>
<div>
    {!! $page->content !!} <!-- Render the WYSIWYG content -->
</div>
@endsection