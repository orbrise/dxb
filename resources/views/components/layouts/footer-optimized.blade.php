<footer id="footer">
    <div class="container-fluid">
        <div class="footer-nav-links">
            <ul class="list-inline mb-3 d-flex flex-wrap align-items-start">
                <li>
                    <a wire:navigate href="{{ url('about') }}">About</a>
                </li>
                <li>
                    <a href="action/listings/new">Advertise Escort Services</a>
                </li>
                <li>
                    <a wire:navigate href="{{ url('help-for-advertisers') }}">Help for Advertisers</a>
                </li>
                <li>
                    <a wire:navigate href="{{ url('guide-to-seeing-an-escort') }}">Guide to seeing an escort</a>
                </li>
                <li class="mb-0" style="vertical-align:middle">
                    <a class="d-inline-flex align-items-center py-0" href="https://trafficking.help/" target="_blank" rel="noopener">
                        <svg class="mr-1" id="a" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 35 47" loading="lazy">
                            <path d="M2.89,24.48c.62,1.79,1.17,6.49,1.64,8.54.95,4.05,1.63,5.81,2.07,8.15.66,3.47,3.06,5.5,7.23,5.77,4.71.3,8.42-.53,11.79-4.51,3.66-4.34,4.61-6.45,5.35-7.22,1.22-1.27,2.02-2.65,2.04-4.21.02-1.04.23-1.9.96-2.56.82-.74.91-1.64-.1-2.15-1.58-.8-3.07-.1-3.79,1.87-1.18,3.24-1.07,4.77-4.16,4.78-1.93,0-1.89-1.28-2.34-3.96-.56-3.33-.68-7.43.25-12.49.7-3.83.32-7.51.35-11.01.01-1.95-.65-2.89-1.88-2.79-1.3.1-1.53,1.32-1.6,3.67-.07,2.37-.33,3.81-.77,5.83-.44,2.04-.36,4.48-.55,7.34-.08,1.13-.51,1.65-1.3,1.63-.64,0-.98-.66-.98-2.04-.01-5.59-.76-10.47-.77-14.65,0-2.74-.38-3.98-1.65-3.95-1.35.03-1.8,1.22-1.73,3.83.1,3.95.39,6.36.13,10.34-.08,1.19.35,3.17.32,5.69,0,.77-.21.92-.75,1.06-.65.17-1.02-.94-1.24-3.17-.42-4.2-1.56-8.46-2.02-12.86-.18-1.69-.75-2.46-1.84-2.33-1.16.14-1.34,1.21-1.12,3.32.64,6.13.89,11.68,2.08,15.83.25.86.04,1.36-.61,1.58-.99.33-1.43-.13-2.16-1.76-1.33-2.95-1.96-5.67-2.37-7.85-.3-1.59-.51-2.93-1.38-3.31C.04,10.08-.13,11.36.06,12.87c.43,3.49,1.34,7.28,2.83,11.61Z" fill="#373435" fill-rule="evenodd" stroke-width="0" />
                            <path d="M3.28,23.96c.62,1.79,1.17,6.49,1.65,8.54.95,4.05,1.63,5.81,2.07,8.15.66,3.47,3.06,5.5,7.23,5.77,4.71.31,8.42-.53,11.79-4.51,3.66-4.34,4.61-6.45,5.35-7.22,1.22-1.27,2.02-2.65,2.04-4.21.02-1.04.24-1.9.96-2.56.82-.74.91-1.64-.1-2.15-1.58-.8-3.07-.1-3.79,1.87-1.18,3.24-1.07,4.77-4.16,4.78-1.93,0-1.89-1.28-2.34-3.96-.56-3.33-.68-7.43.25-12.49.7-3.83.32-7.51.35-11.01.01-1.95-.65-2.89-1.88-2.79-1.3.1-1.53,1.32-1.6,3.67-.07,2.37-.33,3.81-.77,5.83-.44,2.04-.36,4.48-.55,7.34-.08,1.13-.51,1.65-1.3,1.63-.64,0-.98-.66-.98-2.04-.01-5.59-.76-10.47-.77-14.65,0-2.74-.38-3.98-1.65-3.95-1.35.03-1.8,1.22-1.73,3.83.1,3.95.39,6.36.13,10.34-.08,1.19.35,3.17.32,5.69,0,.77-.21.92-.75,1.06-.65.17-1.02-.94-1.24-3.16-.42-4.2-1.56-8.46-2.02-12.86-.18-1.69-.75-2.46-1.84-2.33-1.16.14-1.34,1.21-1.12,3.32.64,6.13.89,11.68,2.08,15.83.25.86.04,1.36-.61,1.58-.99.33-1.43-.13-2.16-1.76-1.33-2.95-1.96-5.67-2.37-7.85-.3-1.59-.51-2.93-1.38-3.31-1.93-.83-2.1.45-1.92,1.96.43,3.49,1.34,7.28,2.83,11.61Z" fill="red" fill-rule="evenodd" stroke-width="0" />
                        </svg>
                        <span>Stop Human Trafficking</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <p class="footer-smallprint">
            This website only allows adult individuals to advertise their time and companionship to other adult individuals. 
            We do not provide a booking service nor arrange meetings. Any price indicated relates to time only and nothing else. 
            Any service offered or whatever else that may occur is the choice of consenting adults and a private matter between them. 
            In some countries, individuals do not legally have the choice to decide this; it is your responsibility to comply with local laws.
        </p>
        
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center">
            <p class="footer-smallprint mb-0">&copy; 2025 Massage Republic</p>
            <span class="footer-pages">
                @livewire('page-list')
            </span>
        </div>
    </div>
</footer>
