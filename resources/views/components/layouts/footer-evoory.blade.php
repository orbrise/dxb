{{-- Evoory Theme Footer - Optimized --}}
<footer class="ev-footer">
    <div class="ev-container">
        {{-- Top Links --}}
        <div class="ev-footer-links-row">
            <a href="/about" class="ev-footer-link">About</a>
            <span class="ev-footer-sep">|</span>
            <a href="/action/listings/new" class="ev-footer-link">Advertise Escort Services</a>
            <span class="ev-footer-sep">|</span>
            <a href="/help-for-advertisers" class="ev-footer-link">Help for Advertisers</a>
            <span class="ev-footer-sep">|</span>
            <a href="/guide-to-seeing-an-escort" class="ev-footer-link">Guide to seeing an escort</a>
            <span class="ev-footer-sep">|</span>
            <a href="https://trafficking.help/" target="_blank" rel="noopener" class="ev-footer-link">Stop Human Trafficking</a>
        </div>
        
        {{-- Disclaimer --}}
        <p class="ev-disclaimer">
            This website only allows adult individuals to advertise their time and companionship to other adult individuals. We do not provide a booking service nor arrange meetings. Any price indicated relates to time only and nothing else. Any service offered or whatever else that may occur is the choice of consenting adults and a private matter between them. In some countries, individuals do not legally have the choice to decide this; it is your responsibility to comply with local laws.
        </p>
        
        {{-- Bottom Section with Dynamic Pages --}}
        <div class="ev-footer-bottom">
            <span class="ev-copyright">&copy; {{ date('Y') }} Evoory</span>
            <span class="ev-footer-sep">|</span>
            <span class="ev-footer-pages">
                @livewire('page-list')
            </span>
        </div>
    </div>
</footer>
