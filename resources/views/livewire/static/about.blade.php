<x-layouts.app-evoory>
@push('css')
<style>
/* Sub-header bar */
.about-subheader {
    background: #131616;
    padding: 12px 0;
}
.about-subheader .ev-container {
    display: flex;
    align-items: center;
    position: relative;
}
.about-subheader .back-link {
    color: #C1F11D;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.about-subheader .back-link:hover {
    color: #d4f84d;
}
.about-subheader .page-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    white-space: nowrap;
}

/* About page */
.about-page {
    background: #0a0a0a;
    min-height: 100vh;
    padding: 40px 0 60px;
}
.about-page .ev-container {
    max-width: 900px;
    margin-left: 2rem;
}
.about-page h1.about-hero {
    color: #fff;
    font-size: 28px;
    font-weight: 700;
    font-style: italic;
    line-height: 1.3;
    margin: 0 0 36px 0;
    max-width: 600px;
}
.about-page h2 {
    color: #fff;
    font-size: 20px;
    font-weight: 600;
    margin: 32px 0 12px 0;
}
.about-page p {
    color: #ccc;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 16px;
}
.about-page a {
    color: #C1F11D;
    text-decoration: none;
}
.about-page a:hover {
    color: #d4f84d;
    text-decoration: underline;
}
.about-page blockquote {
    border-left: 3px solid #C1F11D;
    padding: 12px 20px;
    margin: 20px 0;
    background: #1a1a1a;
    border-radius: 0 8px 8px 0;
}
.about-page blockquote p {
    color: #ddd;
    margin-bottom: 8px;
}
.about-page blockquote footer,
.about-page blockquote ul {
    color: #999;
    font-size: 14px;
    list-style: disc;
    padding-left: 18px;
}
.about-page blockquote li {
    color: #999;
}
</style>
@endpush

{{-- Sub-header --}}
<div class="about-subheader">
    <div class="ev-container">
        <a class="back-link" href="{{ url('female-escorts-in-Dubai') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Escort in Dubai
        </a>
        <h1 class="page-title">About - evoory</h1>
    </div>
</div>

{{-- Main content --}}
<div class="about-page">
    <div class="ev-container">
        <h1 class="about-hero">evoory was created to serve the needs of users<br>and providers of adult services</h1>

        <h2>What is it for?</h2>
        <p>Providers can list a detailed and informational profile where they can propose their time and companionship to others. Users of the site can then make contact directly. We do not act as an intermediary or play any role in any transaction which may eventually take place. We recommend you follow the local laws of the country in which you reside.</p>

        <h2>Why are we different?</h2>
        <p>We have spent a lot of time communicating with users and advertisers to produce a website which is as useful as possible. Features such as verified photos, user reviews, questions and SSL security all help this to happen. We want users and providers to find the website useful and are working hard to make that happen.</p>
        <p>We also have a social mission to <a href="/human-trafficking">help victims of human trafficking</a>.</p>

        <h2>What are people saying?</h2>
        <blockquote>
            <p>If I am honest I thought when you said <q>We will remove all the fake listings</q>, in your last mail, that you were pulling my leg (so to speak..!!). But hey you have done pretty much what you said, and the site is greatly improved. You cannot find a forum here now which does not talk about Mdir and the verified profiles being real. What I want is value for my money and a site that goes to the extent that MDir does to ensure that I at least get the girl in the picture is in my opinion worth a few extra durham.</p>
            <ul><li>Kabul Guy</li></ul>
        </blockquote>
        <blockquote>
            <p>This website has quickly been classed as a reputable site with verified girls and an option to review the lady you visit. You will not find the cheap and nasty (and I don't mean ugly) ladies who find their business in a bar to be advertising. The ladies here are verified by the site and the punters are able to leave reviews, ask questions and communicate easily with the Escort. NOT ALL MEN wish to have a wham Bam Fuck Fest and in actual fact there are many guys out there who are very keen to learn a little about the girl before he commits to a booking.</p>
            <ul><li>Milly</li></ul>
        </blockquote>
        <blockquote>
            <p>Compliments to good website. It is one of the best!</p>
            <ul><li>Jane Twain</li></ul>
        </blockquote>
        <blockquote>
            <p>Thank you for all your help and personal care you put into this site.</p>
            <ul><li>DeLa Luna</li></ul>
        </blockquote>
    </div>
</div>
</x-layouts.app-evoory>
