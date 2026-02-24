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
                        <span><i class="fa fa-arrow-left fa-lg mr-2"></i><span class="d-none d-sm-inline">Previous</span></span>
                    </li>
                @else
                    <li class="d-inline">
                        <a href="{{ $prevUrl }}" 
                           style="cursor: pointer;"
                           rel="prev">
                            <i class="fa fa-arrow-left fa-lg mr-2"></i><span class="d-none d-sm-inline">Previous</span>
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
                            <span class="d-none d-sm-inline">Next</span><i class="fa fa-arrow-right fa-lg ml-2"></i>
                        </a>
                    </li>
                @else
                    <li class="inactive d-inline">
                        <span><span class="d-none d-sm-inline">Next</span><i class="fa fa-arrow-right fa-lg ml-2"></i></span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>