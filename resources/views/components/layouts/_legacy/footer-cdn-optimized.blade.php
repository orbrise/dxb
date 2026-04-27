<footer id="footer">
    <div class="container-fluid">
        <div class="footer-nav-links">
            <!-- Add your footer navigation here -->
        </div>
        
        <p class="footer-smallprint">
            This website only allows adult individuals to advertise their time and companionship to other adult individuals. 
            We do not provide a booking service nor arrange meetings. Any price indicated relates to time only and nothing else. 
            Any service offered or whatever else that may occur is the choice of consenting adults and a private matter between them. 
            In some countries, individuals do not legally have the choice to decide this; it is your responsibility to comply with local laws.
        </p>
        
        <div class="footer-bottom">
            <p class="footer-smallprint copyright">&copy; 2025 Massage Republic</p>
            <div class="footer-pages">
                @livewire('page-list')
            </div>
        </div>
    </div>
</footer>

<style>
.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.copyright {
    margin: 0;
}

.footer-pages {
    display: flex;
    gap: 10px;
}

@media (max-width: 768px) {
    .footer-bottom {
        flex-direction: column;
        text-align: center;
    }
}
</style>
