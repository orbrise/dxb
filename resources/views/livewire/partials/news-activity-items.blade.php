{{-- Activity stream items partial.
     Pre-rendered and cached as a string by NewsPage::render() so the heavy
     foreach loops over $items run once per (gender, city, type, perPage) and
     reuse the resulting HTML for the cache TTL. The surrounding <ul>, the
     load-more trigger, and the page chrome stay in news-page.blade so wire:
     directives still get a fresh component fingerprint per request. --}}
@if($type === 'all')
    @foreach($items as $item)
    @if(isset($item->item_type) && $item->item_type === 'escort')
        @php $profile = $item; @endphp
        <li wire:key="news-escort-{{ $profile->id }}" wire:ignore>
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
                                <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                    <i class="fa fa-check"></i>
                                    <span>Verified photos</span>
                                </span>
                                @endif
                                <div class="image-wrapper" wire:ignore>
                                    @if(!empty($profile->coverimg))
                                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image) }}">
                                    @elseif(!empty($profile->singleimg))
                                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image) }}">
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
                                <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                    <i class="fa fa-check"></i>
                                    <span>Verified photos</span>
                                </span>
                                @endif
                                <div class="image-wrapper" wire:ignore>
                                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
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
        <li wire:key="news-question-{{ $question->id }}" wire:ignore>
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
                                <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                    <i class="fa fa-check"></i>
                                    <span>Verified photos</span>
                                </span>
                                @endif
                                <div class="image-wrapper" wire:ignore>
                                    @if(!empty($question->profile->coverimg))
                                    <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->coverimg->image) }}">
                                    @elseif(!empty($question->profile->singleimg))
                                    <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->singleimg->image) }}">
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
                                <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                    <i class="fa fa-check"></i>
                                    <span>Verified photos</span>
                                </span>
                                @endif
                                <div class="image-wrapper" wire:ignore>
                                    <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
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
    <li wire:key="news-escort-{{ $profile->id }}" wire:ignore>
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
                                <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                    <i class="fa fa-check"></i>
                                    <span>Verified photos</span>
                                </span>
                                @endif
                                <div class="image-wrapper" wire:ignore>
                                    @if(!empty($profile->coverimg))
                                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image) }}">
                                    @elseif(!empty($profile->singleimg))
                                    <img alt="{{ $profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image) }}">
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
                                    <div class="image-wrapper" wire:ignore>
                                        <img alt="{{ $profile->name }} - escort in {{ $cityname }}"
                                             class="img-responsive" decoding="async"
                                             height="208"
                                             width="200"
                                            
                                             src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
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
    <li wire:key="news-review-{{ $review->id }}" wire:ignore>
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
                                <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                    <i class="fa fa-check"></i>
                                    <span>Verified photos</span>
                                </span>
                                @endif
                                <div class="image-wrapper" wire:ignore>
                                    @if(!empty($review->profile->coverimg))
                                    <img alt="{{ $review->profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$review->profile->user_id.'/'.$review->profile->id.'/'.$review->profile->coverimg->image) }}">
                                    @elseif(!empty($review->profile->singleimg))
                                    <img alt="{{ $review->profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$review->profile->user_id.'/'.$review->profile->id.'/'.$review->profile->singleimg->image) }}">
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
                                <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                    <i class="fa fa-check"></i>
                                    <span>Verified photos</span>
                                </span>
                                @endif
                                <div class="image-wrapper" wire:ignore>
                                    <img alt="{{ $review->profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
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
    <li wire:key="news-question-{{ $question->id }}" wire:ignore>
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
                                <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                    <i class="fa fa-check"></i>
                                    <span>Verified photos</span>
                                </span>
                                @endif
                                <div class="image-wrapper" wire:ignore>
                                    @if(!empty($question->profile->coverimg))
                                    <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->coverimg->image) }}">
                                    @elseif(!empty($question->profile->singleimg))
                                    <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->singleimg->image) }}">
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
                                <span class="verified-image text-left small" title="Photos Verified by Evoory">
                                    <i class="fa fa-check"></i>
                                    <span>Verified photos</span>
                                </span>
                                @endif
                                <div class="image-wrapper" wire:ignore>
                                    <img alt="{{ $question->profile->name }} - escort in {{ $cityname }}"
                                         class="img-responsive" decoding="async"
                                         height="208"
                                         width="200"
                                        
                                         src="{{ webp_asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}">
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
