@section('headerform')
@include('components.layouts.headerform')
@endsection

@push('css')
<style>
#header {
    margin-bottom: 0px !important;
}

.nav-bar {
    padding: 5px 0 !important  ;
}


.activity-stream .photo img,
.activity-stream .right-thumbs img {
    width: 143.857143px !important;
    height: 148.57142857px !important;
    object-fit: cover;
}

/* Fix Font Awesome icons in news navigation and content */
.activity-stream-nav .fas,
.activity-stream-nav .fa,
.activity-record .fas,
.activity-record .fa {
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
    display: inline-block !important;
    margin-right: 5px;
}

/* Specific icon fixes */
.fa-certificate:before {
    content: "\f0a3";
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
}

.fa-heart:before {
    content: "\f004";
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
}

.fa-question-circle:before {
    content: "\f059";
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
}

/* Show country flags using flag CDN */
.tt-suggestion .fs,
.tt-dropdown-menu .tt-dataset-location .fs {
    display: inline-block !important;
    width: 20px !important;
    height: 15px !important;
    margin-right: 8px !important;
    vertical-align: middle !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    border-radius: 2px !important;
    visibility: visible !important;
    overflow: hidden !important;
}

/* Flag images from flagcdn.com */
.tt-suggestion .fs.ae { background-image: url(https://flagcdn.com/16x12/ae.png) !important; }
.tt-suggestion .fs.ie { background-image: url(https://flagcdn.com/16x12/ie.png) !important; }
.tt-suggestion .fs.gb,
.tt-suggestion .fs.uk { background-image: url(https://flagcdn.com/16x12/gb.png) !important; }
.tt-suggestion .fs.us { background-image: url(https://flagcdn.com/16x12/us.png) !important; }
.tt-suggestion .fs.ca { background-image: url(https://flagcdn.com/16x12/ca.png) !important; }
.tt-suggestion .fs.au { background-image: url(https://flagcdn.com/16x12/au.png) !important; }
.tt-suggestion .fs.de { background-image: url(https://flagcdn.com/16x12/de.png) !important; }
.tt-suggestion .fs.fr { background-image: url(https://flagcdn.com/16x12/fr.png) !important; }
.tt-suggestion .fs.es { background-image: url(https://flagcdn.com/16x12/es.png) !important; }
.tt-suggestion .fs.it { background-image: url(https://flagcdn.com/16x12/it.png) !important; }
.tt-suggestion .fs.nl { background-image: url(https://flagcdn.com/16x12/nl.png) !important; }
.tt-suggestion .fs.be { background-image: url(https://flagcdn.com/16x12/be.png) !important; }
.tt-suggestion .fs.ch { background-image: url(https://flagcdn.com/16x12/ch.png) !important; }
.tt-suggestion .fs.at { background-image: url(https://flagcdn.com/16x12/at.png) !important; }
.tt-suggestion .fs.se { background-image: url(https://flagcdn.com/16x12/se.png) !important; }
.tt-suggestion .fs.no { background-image: url(https://flagcdn.com/16x12/no.png) !important; }
.tt-suggestion .fs.dk { background-image: url(https://flagcdn.com/16x12/dk.png) !important; }
.tt-suggestion .fs.fi { background-image: url(https://flagcdn.com/16x12/fi.png) !important; }
.tt-suggestion .fs.pl { background-image: url(https://flagcdn.com/16x12/pl.png) !important; }
.tt-suggestion .fs.cz { background-image: url(https://flagcdn.com/16x12/cz.png) !important; }
.tt-suggestion .fs.gr { background-image: url(https://flagcdn.com/16x12/gr.png) !important; }
.tt-suggestion .fs.pt { background-image: url(https://flagcdn.com/16x12/pt.png) !important; }
.tt-suggestion .fs.tr { background-image: url(https://flagcdn.com/16x12/tr.png) !important; }
.tt-suggestion .fs.ru { background-image: url(https://flagcdn.com/16x12/ru.png) !important; }
.tt-suggestion .fs.jp { background-image: url(https://flagcdn.com/16x12/jp.png) !important; }
.tt-suggestion .fs.cn { background-image: url(https://flagcdn.com/16x12/cn.png) !important; }
.tt-suggestion .fs.in { background-image: url(https://flagcdn.com/16x12/in.png) !important; }
.tt-suggestion .fs.sg { background-image: url(https://flagcdn.com/16x12/sg.png) !important; }
.tt-suggestion .fs.hk { background-image: url(https://flagcdn.com/16x12/hk.png) !important; }
.tt-suggestion .fs.th { background-image: url(https://flagcdn.com/16x12/th.png) !important; }
.tt-suggestion .fs.my { background-image: url(https://flagcdn.com/16x12/my.png) !important; }
.tt-suggestion .fs.ph { background-image: url(https://flagcdn.com/16x12/ph.png) !important; }
.tt-suggestion .fs.id { background-image: url(https://flagcdn.com/16x12/id.png) !important; }
.tt-suggestion .fs.nz { background-image: url(https://flagcdn.com/16x12/nz.png) !important; }
.tt-suggestion .fs.za { background-image: url(https://flagcdn.com/16x12/za.png) !important; }
.tt-suggestion .fs.br { background-image: url(https://flagcdn.com/16x12/br.png) !important; }
.tt-suggestion .fs.mx { background-image: url(https://flagcdn.com/16x12/mx.png) !important; }
.tt-suggestion .fs.ar { background-image: url(https://flagcdn.com/16x12/ar.png) !important; }

/* Hide the green circle that typeahead adds */
.tt-suggestion .tt-highlight,
.tt-suggestion strong {
    font-weight: normal;
}

.tt-suggestion::before,
.tt-dataset-location::before {
    display: none !important;
    content: none !important;
}

/* Disable autocomplete hint */
.tt-hint,
input.tt-hint,
.twitter-typeahead .tt-hint,
.typeahead-city-wrapper .tt-hint,
.typeahead-city-wrapper input.tt-hint {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    position: absolute !important;
    left: -9999px !important;
    pointer-events: none !important;
}

/* Ensure main input stays visible */
.typeahead-city-wrapper .tt-input {
    display: block !important;
}

.typeahead-city-wrapper .tt-hint {
    display: none !important;
}

/* Show country flags in dropdown */
.tt-suggestion .flag-icon {
    display: inline-block !important;
    width: 20px;
    height: 15px;
    margin-right: 8px;
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;
}

/* Reply box arrow style */
.listing-reply > div:before {
    font-family: FontAwesome !important;
    content: "";
    color: rgba(0, 0, 0, .2);
    font-size: 1.5em;
    position: absolute;
    left: -7px;
    top: 0;
}

</style>
@endpush
<div>
{{-- ESCORTS / WHAT'S NEW Navigation Tabs - Mobile Only --}}
<div class="visible-xs" style="padding: 8px 15px 0 15px; margin: 0; width: 100%;">
    <div class="btn-group" role="group" style="display: flex !important; width: 100%; margin: 0; border-radius: 4px; overflow: visible; position: relative;">
        <a class="btn" href="/{{ $gender ?? 'female' }}-escorts-in-{{ strtolower($selectedcity ?? 'dubai') }}" 
           style="flex: 1; background-color: #4a4a4a !important; color: #fff !important; font-weight: normal; font-size: 13px; text-transform: uppercase; padding: 8px 15px; border: none; border-radius: 4px 0 0 4px; text-align: center;">
            ESCORTS
        </a>
        <a class="btn" href="/{{ $gender ?? 'female' }}-escort-news-in-{{ strtolower($selectedcity ?? 'dubai') }}" 
           style="flex: 1; background-color: #d4a017 !important; color: #000 !important; font-weight: normal; font-size: 13px; text-transform: uppercase; padding: 8px 15px; border: none; border-radius: 0 4px 4px 0; text-align: center; position: relative;">
            WHAT'S NEW
            <span style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid #d4a017;"></span>
        </a>
    </div>
</div>

<header id="header" style="margin-bottom:0px">
    <div class="nav-bar">
        <div class="container-fluid">
            <form class="simple_form activity-nav-form dark-form search-form form-inline" 
                  id="new_q" 
                  activity-nav-form="true"
                  novalidate="novalidate" 
                  action="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}" 
                  accept-charset="UTF-8" 
                  method="get">
                
                <!-- Gender Dropdown -->
                <div class="form-group dropdown activity-search-gender">
                    <button class="btn btn-dark search-bar--gender" data-toggle="dropdown" tabindex="3" type="button">
                        {{ ucfirst($gender) }} escorts <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu nav nav-pills nav-stacked nav-dark dropdown-gender-menu">
                        <li class="{{ $gender === 'female' ? 'active' : '' }}">
                            <a href="/female-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}" title="Escorts in {{ $cityname }}">Female escorts</a>
                        </li>
                        <li class="{{ $gender === 'male' ? 'active' : '' }}">
                            <a href="/male-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}" title="Gay escorts in {{ $cityname }}">Male escorts</a>
                        </li>
                        <li class="{{ $gender === 'shemale' ? 'active' : '' }}">
                            <a href="/shemale-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}" title="Escort shemales in {{ $cityname }}">Shemale escorts</a>
                        </li>
                    </ul>
                </div>

                <!-- City Search -->
                <div class="form-group" style="position: relative;">
                    <div class='typeahead-city-wrapper' style="position: relative;">
                        <span style="position: absolute; left: 12px; top: 58%; transform: translateY(-50%); z-index: 1; pointer-events: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="white">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </span>
                        <input tabindex="1" 
                               id="news_citysearch" 
                               class="city search-bar--city form-control" 
                               placeholder="Type city..." 
                               type="text" 
                               value="{{ $cityname }}" 
                               autocomplete="off"
                               style="background-color: #333; color: white; border: 1px solid #555; padding-left: 38px;">
                        <div id="news_cityappend" class="citys" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #2c2c2c; border: 1px solid #555; border-top: none; max-height: 300px; overflow-y: auto; z-index: 9999;"></div>
                    </div>
                </div>
            </form>
            
            <!-- Activity Stream Navigation -->
            <div class="activity-stream-nav btn-group">
                <a href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}" 
                   class="btn btn-dark {{ !isset($type) || $type === 'all' ? 'active' : '' }}" 
                   style="color:{{ !isset($type) || $type === 'all' ? '#fff' : '#f4b827' }}">All<span class="hidden-xs hidden-sm"> news</span></a>
                <a href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/new-escorts" 
                   class="btn btn-dark {{ $type === 'new-escorts' ? 'active' : '' }}" 
                   style="color:{{ $type === 'new-escorts' ? '#fff' : '#f4b827' }}"><i class="fas fa-certificate"></i> Escorts</a>
                <a href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/new-reviews" 
                   class="btn btn-dark {{ $type === 'new-reviews' ? 'active' : '' }}" 
                   style="color:{{ $type === 'new-reviews' ? '#fff' : '#f4b827' }}"><i class="fas fa-heart"></i> Reviews</a>
                <a href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/new-questions" 
                   class="btn btn-dark {{ $type === 'new-questions' ? 'active' : '' }}" 
                   style="color:{{ $type === 'new-questions' ? '#fff' : '#f4b827' }}"><i class="fas fa-question-circle"></i> Questions</a>
            </div>
        </div>
    </div>
</header>

      <div class="container-fluid mt-2">
    <div class="subscribe-btn-wrapper subscribe-btn-wrapper--small-right">
        <a class="btn btn-primary btn-lg" data-btn-link="" href="/register">
            <i class="fa fa-newspaper"></i> Subscribe
        </a>
    </div>
    
    <a class="page-title" href="/{{ $gender }}-escort-news-in-{{ $selectedcity }}/{{ $type ?? 'all' }}">
        <h1>{{ $title }}</h1>
    </a>
  
    <ul class="activity-stream activity-stream-full ">
        @if($type === 'all')
            @foreach($items as $item)
            @if(isset($item->item_type) && $item->item_type === 'escort')
                @php $profile = $item; @endphp
                <li>
                @if($loop->first || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->created_at->format('Y-m-d') != $profile->created_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $profile->created_at->format('d') }}</span>
                        <span class="month">{{ $profile->created_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-listing {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-certificate"></i> New escort 
                                <a title="{{ $profile->name }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">{{ $profile->name }}</a>
                            </div>
                            
                            <div class="photo">
                                <a class="pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">
                                    <span class="img-wrapper {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($profile->photoverify) && $profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper">
                                            @if(!empty($profile->coverimg))
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 loading="lazy"
                                                 src="{{ smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image) }}">
                                            @elseif(!empty($profile->singleimg))
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 loading="lazy"
                                                 src="{{ smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            @if($profile->multipleimgs && $profile->multipleimgs->count() > 0)
                            <div class="right-thumbs">
                                @foreach($profile->multipleimgs->take(2) as $img)
                                <a class="{{ $loop->index == 1 ? 'hidden-md' : '' }} pb-photo-link" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">
                                    <span class="img-wrapper {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($profile->photoverify) && $profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper">
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 loading="lazy"
                                                 src="{{ smart_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                        </div>
                                    </span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                            
                            <div class="activity-content">
                                {{ Str::limit($profile->about, 400) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @elseif(isset($item->item_type) && $item->item_type === 'question')
                @php $question = $item; @endphp
                <li>
                @if($loop->first || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->updated_at->format('Y-m-d') != $question->updated_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $question->updated_at->format('d') }}</span>
                        <span class="month">{{ $question->updated_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-question-answered {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-question-circle"></i>
                                <a title="{{ $question->profile->name }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">{{ $question->profile->name }}</a>
                                answered a question
                            </div>
                            
                            <div class="photo">
                                <a class=" pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">
                                    <span class="img-wrapper {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($question->profile->photoverify) && $question->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper">
                                            @if(!empty($question->profile->coverimg))
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->coverimg->image) }}">
                                            @elseif(!empty($question->profile->singleimg))
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            @if($question->profile->multipleimgs && $question->profile->multipleimgs->count() > 0)
                            <div class="right-thumbs">
                                @foreach($question->profile->multipleimgs->take(2) as $img)
                                <a class="{{ $loop->index == 1 ? 'hidden-md' : '' }} pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">
                                    <span class="img-wrapper {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($question->profile->photoverify) && $question->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper">
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                        </div>
                                    </span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                            
                            <div class="activity-content">
                                <div class="listing-question">
                                    <ul class="list-unstyled list-separated">
                                        <li>
                                            <div class="question-block">
                                                <p class="question">{!! nl2br(e($question->question)) !!}</p>
                                            </div>
                                            <span class="questioner">
                                                by <a href="/u/{{ $question->askedBy->name ?? 'anonymous' }}">{{ $question->askedBy->name ?? 'Anonymous' }}</a>
                                            </span>
                                            <span class="question-date">&nbsp;– {{ $question->created_at->format('d M Y') }}</span>
                                            <div class="answer-wrapper">
                                                <div class="answer-block">
                                                    <p class="answer">{!! nl2br(e($question->answer)) !!}</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endif
            @endforeach
            
        @elseif($type === 'new-escorts')
            @foreach($items as $profile)
            <li>
                @if($loop->first || $loop->iteration == 1 || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->created_at->format('Y-m-d') != $profile->created_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $profile->created_at->format('d') }}</span>
                        <span class="month">{{ $profile->created_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-listing {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-certificate"></i> New escort 
                                <a title="{{ $profile->name }}, {{ $profile->gnat->nicename ?? 'Unknown' }} escort in {{ $profile->gcity->name ?? $cityname }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">{{ $profile->name }}</a>
                            </div>
                            
                            <div class="photo">
                                <a class=" pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">
                                    <span class="img-wrapper {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($profile->photoverify) && $profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper">
                                            @if(!empty($profile->coverimg))
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image) }}">
                                            @elseif(!empty($profile->singleimg))
                                            <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            <div class="right-thumbs">
                                @if($profile->multipleimgs && $profile->multipleimgs->count() > 0)
                                    @foreach($profile->multipleimgs->take(2) as $img)
                                    <a class="{{ $loop->index == 1 ? 'hidden-md' : '' }} pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $profile->id }}/{{ $profile->slug }}">
                                        <span class="img-wrapper {{ $profile->package_id == 21 || $profile->package_id == 20 ? 'premium' : '' }}">
                                            <div class="image-wrapper">
                                                <img alt="{{ $profile->name }} - escort in {{ $cityname }}" 
                                                     class="img-responsive" 
                                                     height="208" 
                                                     width="200"
                                                     src="{{ smart_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                            </div>
                                        </span>
                                    </a>
                                    @endforeach
                                @endif
                            </div>
                            
                            <div class="activity-content">
                                {{ Str::limit($profile->about, 400) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
            
        @elseif($type === 'new-reviews')
            @foreach($items as $review)
            <li>
                @if($loop->first || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->created_at->format('Y-m-d') != $review->created_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $review->created_at->format('d') }}</span>
                        <span class="month">{{ $review->created_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-review {{ $review->profile->package_id == 21 || $review->profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-heart"></i> New review for 
                                <a title="{{ $review->profile->name }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $review->profile->id }}/{{ $review->profile->slug }}">{{ $review->profile->name }}</a>
                            </div>
                            
                            <div class="photo">
                                <a class=" pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $review->profile->id }}/{{ $review->profile->slug }}">
                                    <span class="img-wrapper {{ $review->profile->package_id == 21 || $review->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($review->profile->photoverify) && $review->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper">
                                            @if(!empty($review->profile->coverimg))
                                            <img alt="{{ $review->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$review->profile->user_id.'/'.$review->profile->id.'/'.$review->profile->coverimg->image) }}">
                                            @elseif(!empty($review->profile->singleimg))
                                            <img alt="{{ $review->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$review->profile->user_id.'/'.$review->profile->id.'/'.$review->profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            @if($review->profile->multipleimgs && $review->profile->multipleimgs->count() > 0)
                            <div class="right-thumbs">
                                @foreach($review->profile->multipleimgs->take(2) as $img)
                                <a class="{{ $loop->index == 1 ? 'hidden-md' : '' }} pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $review->profile->id }}/{{ $review->profile->slug }}">
                                    <span class="img-wrapper {{ $review->profile->package_id == 21 || $review->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($review->profile->photoverify) && $review->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper">
                                            <img alt="{{ $review->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                        </div>
                                    </span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                            
                            <div class="activity-content">
                                <div class="review">
                                    <span class="star-rating" data-val="{{ $review->star }}" title="Rating: {{ $review->star }} / 5">
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $review->star ? 'selected' : '' }}" data-val="{{ $i }}"></span>
                                            @endfor
                                        </div>
                                    </span>
                                    <span class="reviewer">
                                        by <a href="/u/{{ $review->user->name ?? 'anonymous' }}">{{ $review->user->name ?? 'Anonymous' }}</a>
                                    </span>
                                    <span class="review-date">&nbsp;– {{ $review->created_at->format('d M Y') }}</span>
                                    <div class="review-description">
                                        <p class="review-text">{{ Str::limit($review->review, 500) }}</p>
                                        @if($review->reply)
                                    <div class="listing-reply" style="margin-top: 15px; position: relative;">
                                        <div style="display: inline-block;
    max-width: 100%;
    background: #2a2a2a;
    padding: 10px 15px;
    border-radius: 9px;
    position: relative;">
                                            <!-- Arrow effect -->
                                            <div style="position: absolute;
    top: 10px;
    left: -10px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid #2a2a2a;
    transform: rotate(269deg);"></div>
                                            
                                           
                                            <span style="color: #aaa; font-size: 13px;">
                                                {{ $review->reply }}
                                            </span>
                                        </div>
                                        
                                    </div>
                                    @endif
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
            
        @elseif($type === 'new-questions')
            @foreach($items as $question)
            <li>
                @if($loop->first || (isset($items[$loop->index - 1]) && $items[$loop->index - 1]->updated_at->format('Y-m-d') != $question->updated_at->format('Y-m-d')))
                <div class="date-wrapper">
                    <div class="date {{ $loop->first ? 'first' : '' }}">
                        <span class="day">{{ $question->updated_at->format('d') }}</span>
                        <span class="month">{{ $question->updated_at->format('M') }}</span>
                    </div>
                </div>
                @else
                <div class="date-wrapper"></div>
                @endif
                
                <div class="activity-record-wrapper">
                    <div class="activity-record new-question-answered {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                        <div class="activity-row">
                            <div class="headline h3">
                                <i class="fas fa-question-circle"></i>
                                <a title="{{ $question->profile->name }}" 
                                   href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">{{ $question->profile->name }}</a>
                                answered a question
                            </div>
                            
                            <div class="photo">
                                <a class=" pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">
                                    <span class="img-wrapper {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($question->profile->photoverify) && $question->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper">
                                            @if(!empty($question->profile->coverimg))
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->coverimg->image) }}">
                                            @elseif(!empty($question->profile->singleimg))
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->singleimg->image) }}">
                                            @endif
                                        </div>
                                    </span>
                                </a>
                            </div>
                            
                            @if($question->profile->multipleimgs && $question->profile->multipleimgs->count() > 0)
                            <div class="right-thumbs">
                                @foreach($question->profile->multipleimgs->take(2) as $img)
                                <a class="{{ $loop->index == 1 ? 'hidden-md' : '' }} pb-photo-link" href="/{{ $gender }}-escorts-in-{{ $selectedcity }}/{{ $question->profile->id }}/{{ $question->profile->slug }}">
                                    <span class="img-wrapper {{ $question->profile->package_id == 21 || $question->profile->package_id == 20 ? 'premium' : '' }}">
                                        @if(!empty($question->profile->photoverify) && $question->profile->photoverify->status == 'approved')
                                        <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                                            <i class="fa fa-check"></i>
                                            <span>Verified photos</span>
                                        </span>
                                        @endif
                                        <div class="image-wrapper">
                                            <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}" 
                                                 class="img-responsive" 
                                                 height="208" 
                                                 width="200"
                                                 src="{{ smart_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
                                        </div>
                                    </span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                            
                            <div class="activity-content">
                                <div class="listing-question">
                                    <ul class="list-unstyled list-separated">
                                        <li>
                                            <div class="question-block">
                                                <p class="question">{!! nl2br(e($question->question)) !!}</p>
                                            </div>
                                            <span class="questioner">
                                                by <a href="/u/{{ $question->askedBy->name ?? 'anonymous' }}">{{ $question->askedBy->name ?? 'Anonymous' }}</a>
                                            </span>
                                            <span class="question-date">&nbsp;– {{ $question->created_at->format('d M Y') }}</span>
                                            @if($question->answer)
                                            <div class="answer-wrapper">
                                                <div class="answer-block">
                                                    <p class="answer">{!! nl2br(e($question->answer)) !!}</p>
                                                </div>
                                            </div>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        @endif
        
        @if($items->hasMorePages() || $items->count() >= 3)
        <li class="activity-footer" id="load-more-trigger" x-data="{ 
            observe() {
                let observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            console.log('Trigger visible - loading more');
                            $wire.loadMore();
                        }
                    });
                }, { rootMargin: '500px' });
                observer.observe(this.$el);
            }
        }" x-init="observe()">
            <div class="text-center" style="padding: 20px;">
                <div wire:loading wire:target="loadMore">
                    <i class="fa fa-spinner fa-spin" style="font-size: 24px; color: #f4b827;"></i>
                    <p style="color: #999; margin-top: 10px;">Loading more...</p>
                </div>
                <div wire:loading.remove wire:target="loadMore">
                    <p style="color: #666;">Scroll for more...</p>
                </div>
            </div>
        </li>
        @endif
    </ul>
    </div>
    {{-- <div class="subscribe-rss">
        <a href="https://massagerepublic.com/{{ $gender }}-escort-news-in-{{ $selectedcity }}/{{ $type }}.rss" type="application/rss+xml">
            <i class="fa fa-rss"></i> Subscribe by RSS
        </a>
    </div> --}}
</div>

@push('js')
<script>
// City Search Functionality for News Page
(function() {
    'use strict';
    
    function initNewsCitySearch() {
        console.log('🏙️ Initializing news page city search...');
        
        const cityInput = document.getElementById('news_citysearch');
        const cityAppend = document.getElementById('news_cityappend');
        
        if (!cityInput) {
            console.log('❌ News city input not found');
            return false;
        }
        
        console.log('✅ News city input found');
        
        let searchTimeout = null;
        
        // Helper function to get country code from country name
        function getCountryCode(countryName) {
            if (!countryName) return null;
            
            const countryMap = {
                'United Arab Emirates': 'AE', 'Pakistan': 'PK', 'India': 'IN',
                'United Kingdom': 'GB', 'United States': 'US', 'Brazil': 'BR',
                'Philippines': 'PH', 'Thailand': 'TH', 'Singapore': 'SG',
                'China': 'CN', 'Japan': 'JP', 'France': 'FR', 'Germany': 'DE',
                'Italy': 'IT', 'Spain': 'ES', 'Canada': 'CA', 'Australia': 'AU',
                'Netherlands': 'NL', 'Belgium': 'BE', 'Switzerland': 'CH',
                'Austria': 'AT', 'Sweden': 'SE', 'Norway': 'NO', 'Denmark': 'DK',
                'Finland': 'FI', 'Poland': 'PL', 'Czech Republic': 'CZ',
                'Turkey': 'TR', 'Egypt': 'EG', 'South Africa': 'ZA',
                'Saudi Arabia': 'SA', 'Qatar': 'QA', 'Kuwait': 'KW',
                'Bahrain': 'BH', 'Oman': 'OM', 'Lebanon': 'LB', 'Jordan': 'JO',
                'Ireland': 'IE', 'Portugal': 'PT', 'Greece': 'GR', 'Russia': 'RU',
                'Malaysia': 'MY', 'Indonesia': 'ID', 'Vietnam': 'VN',
                'South Korea': 'KR', 'Hong Kong': 'HK', 'New Zealand': 'NZ',
                'Argentina': 'AR', 'Mexico': 'MX', 'Colombia': 'CO'
            };
            
            return countryMap[countryName] || null;
        }
        
        // Function to search cities from database
        function searchCities(query) {
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                cityAppend.style.display = 'none';
                cityAppend.innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(function() {
                console.log('🔍 Searching for city:', query);
                
                // Use SessionRecovery.fetch for automatic token refresh on session expiry
                var fetchFn = window.SessionRecovery ? window.SessionRecovery.fetch.bind(window.SessionRecovery) : fetch;
                
                fetchFn('/cities/search?_=' + Date.now(), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.SessionRecovery ? window.SessionRecovery.getToken() : '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache'
                    },
                    body: JSON.stringify({ query: query })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('✅ City search results:', data);
                    cityAppend.innerHTML = '';
                    
                    if (data.length === 0) {
                        cityAppend.innerHTML = '<div style="padding: 10px; color: #999;">No cities found</div>';
                        cityAppend.style.display = 'block';
                    } else {
                        data.forEach(function(city) {
                            const citySlug = city.name.toLowerCase().replace(/\s+/g, '-');
                            const countryCode = getCountryCode(city.country);
                            
                            const opt = document.createElement('div');
                            opt.style.cssText = 'padding: 10px 15px; cursor: pointer; color: #fff; display: flex; align-items: center; transition: background 0.2s;';
                            
                            // Add flag
                            if (countryCode) {
                                const flagSpan = document.createElement('span');
                                flagSpan.style.cssText = 'margin-right: 10px; width: 20px; height: 14px; background-size: cover; background-position: center; display: inline-block;';
                                flagSpan.style.backgroundImage = `url(https://flagcdn.com/w40/${countryCode.toLowerCase()}.png)`;
                                opt.appendChild(flagSpan);
                            }
                            
                            // City name
                            const nameSpan = document.createElement('span');
                            nameSpan.textContent = city.name;
                            nameSpan.style.flex = '1';
                            opt.appendChild(nameSpan);
                            
                            // Profile count
                            if (city.profile_count !== undefined) {
                                const countSpan = document.createElement('span');
                                countSpan.textContent = city.profile_count;
                                countSpan.style.cssText = 'color: #999; font-size: 12px; margin-left: auto;';
                                opt.appendChild(countSpan);
                            }
                            
                            opt.addEventListener('mouseenter', function() {
                                this.style.backgroundColor = '#444';
                            });
                            opt.addEventListener('mouseleave', function() {
                                this.style.backgroundColor = 'transparent';
                            });
                            
                            opt.addEventListener('click', function() {
                                console.log('🎯 City selected:', city.name);
                                cityInput.value = city.name;
                                cityAppend.style.display = 'none';
                                
                                // Redirect to the news page for selected city
                                const currentGender = '{{ $gender ?? "female" }}';
                                const currentType = '{{ $type ?? "all" }}';
                                let urlPath = `/${currentGender}-escort-news-in-${citySlug}`;
                                if (currentType && currentType !== 'all') {
                                    urlPath += `/${currentType}`;
                                }
                                console.log('🚀 Redirecting to:', urlPath);
                                window.location.href = urlPath;
                            });
                            
                            cityAppend.appendChild(opt);
                        });
                        cityAppend.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('❌ City search error:', error);
                    // Show user-friendly error with retry hint
                    cityAppend.innerHTML = '<div style="padding: 10px; color: #dc3545;">Connection error. Please try again.</div>';
                    cityAppend.style.display = 'block';
                    
                    // Auto-retry after a short delay
                    setTimeout(function() {
                        if (cityInput.value.trim().length >= 2) {
                            console.log('🔄 Auto-retrying city search...');
                            searchCities(cityInput.value.trim());
                        }
                    }, 2000);
                });
            }, 300);
        }
        
        // Input event listener
        cityInput.addEventListener('input', function(e) {
            searchCities(e.target.value.trim());
        });
        
        // Click outside to close dropdown
        document.addEventListener('click', function(e) {
            if (!cityInput.contains(e.target) && !cityAppend.contains(e.target)) {
                cityAppend.style.display = 'none';
            }
        });
        
        // Focus event
        cityInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                searchCities(this.value.trim());
            }
        });
        
        console.log('🏙️ News page city search initialized successfully');
        return true;
    }
    
    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNewsCitySearch);
    } else {
        initNewsCitySearch();
    }
    
    // Handle Livewire navigation
    document.addEventListener('livewire:navigated', initNewsCitySearch);
})();
</script>
@endpush
</div>
