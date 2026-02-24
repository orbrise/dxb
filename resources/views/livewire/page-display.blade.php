@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid"> 
        <a class="back-link" href="{{url('/')}}" wire:navigate>
            <i class="fa fa-angle-left fa-fw"></i><span class="hidden-xs">Back</span></a>
        <div class="title">
            <h1><a href="/register"></a></h1>
        </div>
    </div>
</div>
@endsection

<div class="container-fluid">
    <div class="row">
        <div class="col-12" style="padding: 0px 15px 15px 15px;">
            <h1>{{ $page->title }}</h1>
            
            {{-- Render processed content (with shortcodes removed) --}}
            @if($processedContent)
                <div class="page-content">
                    {!! $processedContent !!}
                </div>
            @endif
            
            {{-- Render contact form if shortcode was detected --}}
            @if($hasContactForm)
                <div class="contact-form-section mt-4">
                    <livewire:contact-form />
                </div>
            @endif
        </div>
    </div>
</div> 