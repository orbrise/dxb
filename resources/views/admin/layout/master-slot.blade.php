{{-- Slot-based wrapper around the classic admin.layout.master so that
     Livewire 3 full-page components (which render into {{ $slot }}) can
     be rendered inside the existing admin chrome without touching the
     hundreds of legacy views that still @extends('admin.layout.master'). --}}
@extends('admin.layout.master')

@section('content')
    {{ $slot }}
@endsection
