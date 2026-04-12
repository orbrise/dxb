<div>
    @if ($paginator->hasPages())
        @php
            $gender = request()->route('gender') ?: 'female';
            $city = request()->route('city') ?: 'dubai';
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            
            // Generate URLs
            $prevPage = $currentPage - 1;
            $nextPage = $currentPage + 1;
            
            $prevUrl = $prevPage <= 1 
                ? route('home', ['gender' => $gender, 'city' => $city])
                : route('home.paginated', ['gender' => $gender, 'city' => $city, 'page' => $prevPage]);
            
            $nextUrl = route('home.paginated', ['gender' => $gender, 'city' => $city, 'page' => $nextPage]);
        @endphp 
        <nav aria-label="Listing pages navigation" style="width: fit-content">
            <ul class="pagination pagination-lg mt-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="inactive d-inline">
                        <span><svg class="pag-arrow pag-arrow-left" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg><span class="d-none d-sm-inline">Previous</span></span>
                    </li>
                @else
                    <li class="d-inline">
                        <a href="{{ $prevUrl }}" 
                           style="cursor: pointer;"
                           rel="prev">
                            <svg class="pag-arrow pag-arrow-left" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg><span class="d-none d-sm-inline">Previous</span>
                        </a>
                    </li>
                @endif

                {{-- Current Page / Total Pages --}}
                <li class="active d-inline">
                    <span class="px-4">{{ $currentPage }} of {{ $lastPage }}</span>
                </li>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="d-inline">
                        <a href="{{ $nextUrl }}" 
                           style="cursor: pointer;"
                           rel="next">
                            <span class="d-none d-sm-inline">Next</span><svg class="pag-arrow pag-arrow-right" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    </li>
                @else
                    <li class="inactive d-inline">
                        <span><span class="d-none d-sm-inline">Next</span><svg class="pag-arrow pag-arrow-right" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>