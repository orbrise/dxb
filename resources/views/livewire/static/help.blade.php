<x-layouts.app-evoory>
@push('css')
<style>
/* Sub-header bar */
.help-subheader {
    background: #131616;
    padding: 12px 0;
}
.help-subheader .ev-container {
    display: flex;
    align-items: center;
    position: relative;
}
.help-subheader .back-link {
    color: #C1F11D;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.help-subheader .back-link:hover {
    color: #d4f84d;
}
.help-subheader .page-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    white-space: nowrap;
}

/* Help page */
.help-page {
    background: #0a0a0a;
    min-height: 100vh;
    padding: 40px 0 60px;
}
.help-page .ev-container {
    max-width: 900px;
    margin-left: 2rem;
}
.help-page h1.help-hero {
    color: #fff;
    font-size: 28px;
    font-weight: 700;
    font-style: italic;
    line-height: 1.3;
    margin: 0 0 36px 0;
    max-width: 600px;
}
.help-page h2 {
    color: #fff;
    font-size: 20px;
    font-weight: 600;
    margin: 32px 0 12px 0;
}
.help-page p {
    color: #ccc;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 16px;
}
.help-page a {
    color: #C1F11D;
    text-decoration: none;
}
.help-page a:hover {
    color: #d4f84d;
    text-decoration: underline;
}
.help-page blockquote {
    border-left: 3px solid #C1F11D;
    padding: 12px 20px;
    margin: 20px 0;
    background: #1a1a1a;
    border-radius: 0 8px 8px 0;
}
.help-page blockquote p {
    color: #ddd;
    margin-bottom: 8px;
}
.help-page blockquote footer {
    color: #999;
    font-size: 14px;
}
.help-page ul {
    list-style: disc;
    padding-left: 24px;
    margin-bottom: 16px;
}
.help-page ul li {
    color: #ccc;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 6px;
}
.help-page ul li::marker {
    color: #C1F11D;
}
.help-page b {
    color: #fff;
}
.help-page .card {
    background: #1a1a1a;
    border: 1px solid #222;
    border-radius: 8px;
    margin: 24px 0;
    overflow: hidden;
}
.help-page .card-header {
    padding: 16px 20px 0;
}
.help-page .card-header h2 {
    margin: 0 0 8px 0;
}
.help-page .card-block {
    padding: 16px 20px;
}
.help-page video {
    border-radius: 6px;
    max-width: 100%;
}
</style>
@endpush

{{-- Sub-header --}}
<div class="help-subheader">
    <div class="ev-container">
        <a class="back-link" href="{{ url('female-escorts-in-Dubai') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Escort in Dubai
        </a>
        <h1 class="page-title">Help for Advertisers - evoory</h1>
    </div>
</div>

{{-- Main content --}}
<div class="help-page">
    <div class="ev-container">
        <h1 class="help-hero">evoory was created to serve the needs of both the providers and users of adult services</h1>

        <blockquote>
            <p>MDir was a real help when I started out as an independent last year. The enquiries you sent plus my old clients meant my life <b>got a lot better!</b> Thanks for the help.</p>
            <footer>Ella</footer>
        </blockquote>

        <h2>Is it free?</h2>
        <p>Yes, it is always free to have a profile. There is a (generous) limit on the number of enquiries (messages and clicks on your phone number) you can get each month in 'free mode'. If you reach this limit we will send you an email and you will not appear for 14 days when people make a search, although you profile will continue to be live and people will still be able contact you. We do this to provide the fairest distribution of exposure on the site between our free members.</p>

        <h2>How do I make a great profile?</h2>
        <p>If you provide a complete profile with accurate information, you will increase the number and quality of the results you get from it.</p>
        <ul>
            <li>Put real photos, preferably more than one</li>
            <li>Take time to provide a good description</li>
            <li>List only the services you provide</li>
            <li>Make sure your phone number is correct</li>
        </ul>
        <p>Please don't!</p>
        <ul>
            <li>Make more than one profile per individual (in the same city), they may be deleted. If you list as an agency and do make a profile for each girl this is permitted if you have their permission.</li>
            <li>Post fake listings or fake photos</li>
            <li>Use ALL CAPS, it looks CHEAP</li>
        </ul>

        <h2>Can one individual have more than one profile?</h2>
        <p>No. If you create duplicate profiles they may be deleted or your account blocked. As an individual you can list in more than one city if you really are there often (please pause your listing when not there and put available dates on your different profiles). You will save time on responding to enquiries which don't produce business.</p>

        <h2>Can I make profiles for different individuals?</h2>
        <p>Yes, as long as you have their permission for you to list them and they are real people.</p>

        <h2 id="google-analytics">Can I see how much business you are generating for me?</h2>
        <p>We hope so! In your account area on evoory you can see graphs which detail: the number of messages sent to you, clicks on your phone number and clicks through to your website. If you use Google Analytics you can also find the number of website visitors by looking in <b>Acquisition > Campaigns > All Campaigns</b>. We know you won't always know about all the customers we send, but we have tried our best to give you an idea! If you have questions about measurement, get in touch, as we really appreciate hearing about your results.</p>

        <span id="upgrade-options"></span>
        <h2>What are the different options for my profile?</h2>
        <p>A few cities have higher pricing than the base prices below because of higher demand and better results for advertisers. You will see the price for your city from your listing page. Our main goal is to deliver value-for-money to everyone, based on results, while maintaining a free option for everyone.</p>
        <ul>
            <li><b>VIP:</b> You get top priority in the search results and city main pages. Unlimited enquiries. <b>From €29/month</b></li>
            <li><b>Featured:</b> You appear ahead of Basic and Free profiles. Unlimited enquiries. <b>From €18/month</b></li>
            <li><b>Basic:</b> You get unlimited monthly enquires and priority over free members in the search results. <b>From €7/month</b></li>
            <li><b>Free:</b> You will appear after paid listings in the search results and if you receive over a certain amount of enquiries during a 30 day period your profile will be limited until the end of the period. We do this to provide for the fairest distribution of leads between our free members.</li>
        </ul>

        <span id="payment-problems"></span>
        <h2>I have problems paying?</h2>
        <p>If your card is not accepted, you will see an error message. There are several things you can try:</p>
        <ul>
            <li>Ensure your card has sufficient free funds on it</li>
            <li>Check with your bank that your card can make international payments online</li>
            <li>Check your card is enrolled with a secure online payment scheme such as 3D (Verified by Visa) or Mastercard SecureCode - you should be able to do this online with your bank</li>
            <li>Call your bank to ask why the payment for the amount you tried to pay was refused</li>
        </ul>
        <p>If you have completed these steps and still have a problem <a href="/contact-us">contact us</a> with details of what the problem is, including the error message.</p>

        <h2 id="order">How are profiles ordered?</h2>
        <p>Profiles within the different categories (VIP, Featured, Basic and Free) are randomised daily and customised to the individual user and quality factors for each search (make sure you answer or ignore questions, add video and get reviews for the best results). We do this to provide the best possible experience for users and ensure that all advertisers in a single category receive a similar amount of exposure to users.</p>

        <h2 id="how-do-you-set-prices">How do you set prices?</h2>
        <p>Our main objective is to provide value-for-money. Our pricing is defined on a per city basis. We only raise prices (and not often) when the number of enquiries and advertisers goes over a certain level. This is to maintain the number of VIP and Featured listings at a level where each paid listing will continue to receive the number of enquiries they expect from us. We introduced variable pricing after talking to many advertisers who were asking how they could get more exposure and indicated they were willing to pay more if they could stand out.</p>

        <h2 id="fake">Fake photos are not OK</h2>
        <p>If you post fake photos, your listings will be marked as Fake. This will display a warning to the visitors and move your listing to the bottom of the results.</p>

        <h2 id="why-are-my-photos-marked-as-fake">Why are my photos marked as fake?</h2>
        <p>This is the most common complaint from users about listings. Fake photos are not fair to the visitors and other advertisers. Your photos may be marked as fake because of a user report - you will receive a warning mail when this happens. If you do not agree with the decision you can apply for verification. It is free and you can <a href="/my-profile">verify here</a>.</p>

        <h2>What is not OK?</h2>
        <p>If you are an advertiser and do not follow the rules, your account and any current account associated with you and any future account will be blocked.</p>
        <ul>
            <li>Underage photos or photos of children in any listing.</li>
            <li>Trafficking, enslavement or anything similar.</li>
            <li>Abuse, violence or oppression towards colleagues.</li>
            <li>Online trolling or other defamation towards other users.</li>
        </ul>

        <h2>Can I transfer credits?</h2>
        <p>Yes, go to your <a href="/my-account">Account</a> and click on <b>Transfer credits</b>.</p>

        <div class="card" id="viewing_messages">
            <div class="card-header">
                <h2>Viewing Messages</h2>
            </div>
            <div class="card-block">
                <video width="600" controls class="col-sm-12 col-xs-12 col-md-6" src="//d18fr84zq3fgpm.cloudfront.net/comfy/cms/files/files/000/000/067/original/viewing_messages.mp4"></video>
            </div>
        </div>

        <div class="card" id="viewing_questions">
            <div class="card-header">
                <h2>Viewing Questions</h2>
            </div>
            <div class="card-block">
                <video width="600" controls class="col-sm-12 col-xs-12 col-md-6" src="//d18fr84zq3fgpm.cloudfront.net/comfy/cms/files/files/000/000/061/original/viewing_questions.mp4"></video>
            </div>
        </div>

        <div class="card" id="bookmarking">
            <div class="card-header">
                <h2>Bookmarking</h2>
            </div>
            <div class="card-block">
                <video width="600" controls class="col-sm-12 col-xs-12 col-md-6" src="//d18fr84zq3fgpm.cloudfront.net/comfy/cms/files/files/000/000/060/original/bookmarking.mp4"></video>
            </div>
        </div>

        <div class="card" id="editing_listings">
            <div class="card-header">
                <h2>Editing Listings</h2>
            </div>
            <div class="card-block">
                <video width="600" controls class="col-sm-12 col-xs-12 col-md-6" src="//d18fr84zq3fgpm.cloudfront.net/comfy/cms/files/files/000/000/066/original/edit_listing.mp4"></video>
            </div>
        </div>

        <div class="card" id="deleting_listings">
            <div class="card-header">
                <h2>Deleting Listings</h2>
            </div>
            <div class="card-block">
                <video width="600" controls class="col-sm-12 col-xs-12 col-md-6" src="//d18fr84zq3fgpm.cloudfront.net/comfy/cms/files/files/000/000/062/original/deleting_listings.mp4"></video>
            </div>
        </div>

        <div class="card" id="managing_profiles">
            <div class="card-header">
                <h2>Managing Profiles</h2>
            </div>
            <div class="card-block">
                <video width="600" controls class="col-sm-12 col-xs-12 col-md-6" src="//d18fr84zq3fgpm.cloudfront.net/comfy/cms/files/files/000/000/065/original/managing_profiles.mp4"></video>
            </div>
        </div>

        <div class="card" id="transfering_credits">
            <div class="card-header">
                <h2>Transferring Credits</h2>
            </div>
            <div class="card-block">
                <video width="600" controls class="col-sm-12 col-xs-12 col-md-6" src="//d18fr84zq3fgpm.cloudfront.net/comfy/cms/files/files/000/000/064/original/transfering_credits.mp4"></video>
            </div>
        </div>

        <div class="card" id="editing_account">
            <div class="card-header">
                <h2>Editing my Email and Account details</h2>
            </div>
            <div class="card-block">
                <video width="600" controls class="col-sm-12 col-xs-12 col-md-6" src="//d18fr84zq3fgpm.cloudfront.net/comfy/cms/files/files/000/000/063/original/editing_account.mp4"></video>
            </div>
        </div>

        <h2>More questions?</h2>
        <p>If you have a question, find something that doesn't work or have a suggestion to make the site work better, <a href="/forums/feedback">click here for the Feedback forum</a>.</p>

        <h2>Enquiries</h2>
        <p>Contact us by using our: <a href="/contact-us">contact form</a>.</p>
    </div>
</div>
</x-layouts.app-evoory>
