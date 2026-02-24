<div>
    @if($pages->isEmpty())
        <p>No pages available.</p>
    @else
    <ul class="list-inline mb-3 d-flex flex-wrap align-items-start">
        @foreach($pages as $page)
          <li>  <a href="/page/{{ $page->slug }}">{{ $page->title }}</a>  </li>
        @endforeach
      </ul>
        
    @endif
</div>