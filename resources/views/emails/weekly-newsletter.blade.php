<div style="background-color:#232323;font:14px/1.5 'Helvetica Neue',Helvetica,Arial,sans-serif;color:#fff">
    <div style="padding:20px;max-width:670px;background-color:#232323;font:14px/1.5 'Helvetica Neue',Helvetica,Arial,sans-serif;color:#fff">
        
        {{-- Header --}}
        <div style="border-bottom:2px solid #333;padding-bottom:5px;margin-bottom:20px">
            <a href="{{ config('app.url') }}?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" title="Massage Republic" style="color:#f4b827;text-decoration:none;outline:0">
                <img alt="Massage Republic" src="https://assets.massagerepublic.com.co/assets/images/web/maillogo.gif" width="309">
            </a>
        </div>

        {{-- Greeting --}}
        <p style="font:14px/1.5 'Helvetica Neue',Helvetica,Arial,sans-serif;color:#fff">Hi {{ $user->name ?? $user->email }},</p>

        <p style="font:14px/1.5 'Helvetica Neue',Helvetica,Arial,sans-serif;color:#fff">
            Here's what's new this week in {{ implode(', ', $cityNames) }}!
        </p>

        {{-- New Reviews Section --}}
        @if($reviews && $reviews->count() > 0)
        <h3 style="color:#fff;font-size:24px">
            <a href="{{ config('app.url') }}/{{ $genders[0] }}-escort-news-in-{{ strtolower(str_replace(' ', '-', $cityNames[0])) }}/new-escort-reviews?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">
                New Reviews in {{ $cityNames[0] }}
            </a>
        </h3>

        @foreach($reviews->take(7) as $review)
        <div style="font-style:italic;margin-bottom:20px;clear:both">
            @if($review->profile)
            <a href="{{ config('app.url') }}/{{ $review->profile->gender }}-escorts-in-{{ strtolower(str_replace(' ', '-', $review->profile->getcity->name ?? 'dubai')) }}/{{ $review->profile->slug }}?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">
                <span style="display:block;margin:0 auto;margin-right:10px;float:left">
                    <div>
                        @if($review->profile->coverimg && $review->profile->coverimg->image)
                        <img alt="{{ $review->profile->name }}" height="95" 
                             src="{{ smart_asset('userimages/'.$review->profile->user_id.'/'.$review->profile->id.'/'.$review->profile->coverimg->image) }}" 
                             width="89" style="border:1px solid #fff;object-fit:contain">
                        @elseif($review->profile->singleimg && $review->profile->singleimg->image)
                        <img alt="{{ $review->profile->name }}" height="95" 
                             src="{{ smart_asset('userimages/'.$review->profile->user_id.'/'.$review->profile->id.'/'.$review->profile->singleimg->image) }}" 
                             width="89" style="border:1px solid #fff;object-fit:contain">
                        @else
                        <img alt="{{ $review->profile->name }}" height="95" 
                             src="{{ config('app.url') }}/assets/images/default-avatar.png" 
                             width="89" style="border:1px solid #fff;object-fit:contain">
                        @endif
                    </div>
                </span>
            </a>
            <p style="font:14px/1.5 'Helvetica Neue',Helvetica,Arial,sans-serif;color:#fff;margin-top:0">
                {{ Str::limit($review->review, 200) }}
            </p>
            <a href="{{ config('app.url') }}/{{ $review->profile->gender }}-escorts-in-{{ strtolower(str_replace(' ', '-', $review->profile->getcity->name ?? 'dubai')) }}/{{ $review->profile->slug }}?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">More</a>
            <p style="font:14px/1.5 'Helvetica Neue',Helvetica,Arial,sans-serif;color:#fff;margin-top:0">Rating: {{ $review->star }} / 5</p>
            @endif
            <div style="clear:both"></div>
        </div>
        @endforeach
        @endif

        {{-- New Listings Section --}}
        @if($listings && $listings->count() > 0)
        <h3 style="color:#fff;font-size:24px">
            <a href="{{ config('app.url') }}/{{ $genders[0] }}-escort-news-in-{{ strtolower(str_replace(' ', '-', $cityNames[0])) }}/new-escorts?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">
                New Listings in {{ $cityNames[0] }}
            </a>
        </h3>
        
        <ul>
        @foreach($listings->take(15) as $listing)
            <li style="color:#fff;display:list-item;list-style-type:disc;line-height:21px;float:left;list-style:none;display:block;padding-left:0;width:157.5px;margin-left:10px">
                <a href="{{ config('app.url') }}/{{ $listing->gender }}-escorts-in-{{ strtolower(str_replace(' ', '-', $listing->getcity->name ?? 'dubai')) }}/{{ $listing->slug }}?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">
                    <span style="display:block;margin:0 auto">
                        <div>
                            @if($listing->coverimg && $listing->coverimg->image)
                            <img alt="{{ $listing->name }}" height="135" 
                                 src="{{ smart_asset('userimages/'.$listing->user_id.'/'.$listing->id.'/'.$listing->coverimg->image) }}" 
                                 width="115" style="border:1px solid #fff;object-fit:contain">
                            @elseif($listing->singleimg && $listing->singleimg->image)
                            <img alt="{{ $listing->name }}" height="135" 
                                 src="{{ smart_asset('userimages/'.$listing->user_id.'/'.$listing->id.'/'.$listing->singleimg->image) }}" 
                                 width="115" style="border:1px solid #fff;object-fit:contain">
                            @else
                            <img alt="{{ $listing->name }}" height="135" 
                                 src="{{ config('app.url') }}/assets/images/default-avatar.png" 
                                 width="115" style="border:1px solid #fff;object-fit:contain">
                            @endif
                        </div>
                    </span>
                </a>
                <div style="font-weight:700;margin-bottom:6px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis">
                    <a href="{{ config('app.url') }}/{{ $listing->gender }}-escorts-in-{{ strtolower(str_replace(' ', '-', $listing->getcity->name ?? 'dubai')) }}/{{ $listing->slug }}?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">
                        {{ $listing->name }}
                    </a>
                </div>
            </li>
        @endforeach
        </ul>
        <div style="clear:both"></div>
        @endif

        {{-- New Answered Questions Section --}}
        @if($questions && $questions->count() > 0)
        <h3 style="color:#fff;font-size:24px">
            <a href="{{ config('app.url') }}/{{ $genders[0] }}-escort-news-in-{{ strtolower(str_replace(' ', '-', $cityNames[0])) }}/new-questions?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">
                New Answered Questions in {{ $cityNames[0] }}
            </a>
        </h3>

        @foreach($questions->take(6) as $question)
        <div style="margin-bottom:20px;clear:both">
            @if($question->profile)
            <a href="{{ config('app.url') }}/{{ $question->profile->gender }}-escorts-in-{{ strtolower(str_replace(' ', '-', $question->profile->getcity->name ?? 'dubai')) }}/{{ $question->profile->slug }}?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">
                <span style="display:block;margin:0 auto;margin-right:10px;float:left">
                    <div>
                        @if($question->profile->coverimg && $question->profile->coverimg->image)
                        <img alt="{{ $question->profile->name }}" height="95" 
                             src="{{ smart_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->coverimg->image) }}" 
                             width="89" style="border:1px solid #fff;object-fit:contain">
                        @elseif($question->profile->singleimg && $question->profile->singleimg->image)
                        <img alt="{{ $question->profile->name }}" height="95" 
                             src="{{ smart_asset('userimages/'.$question->profile->user_id.'/'.$question->profile->id.'/'.$question->profile->singleimg->image) }}" 
                             width="89" style="border:1px solid #fff;object-fit:contain">
                        @else
                        <img alt="{{ $question->profile->name }}" height="95" 
                             src="{{ config('app.url') }}/assets/images/default-avatar.png" 
                             width="89" style="border:1px solid #fff;object-fit:contain">
                        @endif
                    </div>
                </span>
            </a>
            <div style="font-style:italic">
                <strong>Q:</strong> {{ Str::limit($question->question, 150) }}
            </div>
            <div>
                <strong>A:</strong> {{ Str::limit($question->answer, 200) }}— 
                <a href="{{ config('app.url') }}/{{ $question->profile->gender }}-escorts-in-{{ strtolower(str_replace(' ', '-', $question->profile->getcity->name ?? 'dubai')) }}/{{ $question->profile->slug }}?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">
                    {{ $question->profile->name }}
                </a>
            </div>
            @endif
            <div style="clear:both"></div>
        </div>
        @endforeach
        @endif

        {{-- Footer --}}
        <p style="font:14px/1.5 'Helvetica Neue',Helvetica,Arial,sans-serif;color:#fff">Thanks, Claire</p>

        <p style="font:14px/1.5 'Helvetica Neue',Helvetica,Arial,sans-serif;color:#fff;color:#bebebe;font-size:10pt;border-top:2px solid #333;margin-top:20px;text-align:justify;padding-top:5px">
            To change newsletter settings or unsubscribe:
            <a href="{{ config('app.url') }}/my-account/newsletter/edit?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0">click here</a>
        </p>

        <div style="text-align:center;padding-top:5px;margin-top:5px;border-top:2px solid #333;font-size:9pt">
            <a href="{{ config('app.url') }}?utm_campaign=Newsletter&utm_content=Weekly%20Update&utm_medium=email&utm_source=AppMail" style="color:#f4b827;text-decoration:none;outline:0;text-decoration:underline">
                Go to MassageRepublic.com
            </a>
        </div>
    </div>
</div>
