@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="">
        <i class="fa fa-angle-left fa-fw"></i>
        <span class="hidden-xs">My Profile</span>
      </a>
      <div class="title">
        <h1>
          <a href="">Upgrade for Sana Khan in Dubai</a>
        </h1>
      </div>
    </div>
  </div>
@endsection

<div class="container-fluid">
      <div class="content-wrapper no-sidebar">
        <div id="content">
          <form class="simple_form upgrade-listing-form upgrade-listing-form-init  free-visible" id="new_upgrade_process" data-listing-upgrade-form="{&quot;accountBalance&quot;:0,&quot;upgrades&quot;:{&quot;free&quot;:{&quot;name&quot;:&quot;Free profile&quot;,&quot;pricePerMonth&quot;:null},&quot;basic&quot;:{&quot;name&quot;:&quot;Basic profile&quot;,&quot;pricePerMonth&quot;:99},&quot;featured&quot;:{&quot;name&quot;:&quot;Featured profile&quot;,&quot;pricePerMonth&quot;:179},&quot;premium&quot;:{&quot;name&quot;:&quot;VIP profile&quot;,&quot;pricePerMonth&quot;:299}},&quot;periods&quot;:[{&quot;value&quot;:0.34,&quot;label&quot;:&quot;10 days&quot;,&quot;days&quot;:10},{&quot;value&quot;:0.67,&quot;label&quot;:&quot;20 days&quot;,&quot;days&quot;:20},{&quot;value&quot;:1,&quot;label&quot;:&quot;30 days&quot;,&quot;days&quot;:30}],&quot;maxPaymentMethodPrices&quot;:{&quot;pbs&quot;:700,&quot;emp&quot;:1000,&quot;cardi&quot;:500,&quot;bitcoin&quot;:50000,&quot;lightning&quot;:1500,&quot;constant&quot;:100,&quot;momo&quot;:1000,&quot;promptpay&quot;:1000,&quot;viettelpay&quot;:1000},&quot;minPaymentMethodPrices&quot;:{&quot;promptpay&quot;:9},&quot;bitcoinBonus&quot;:10,&quot;lightningBonus&quot;:10}" novalidate="novalidate" action="/my-profile/sana-khan-764ef4fa-698d-43e9-8ea9-90690fe782d2/upgrade" accept-charset="UTF-8" method="post">
            <input name="utf8" type="hidden" value="&#x2713;" />
            <input type="hidden" name="authenticity_token" value="HeMCOhqmq9jr1cViBkGgSd4RURse9TDuCBnxh/uvM648LFDDtjP3QfaMB3FLdfx92ENrrxZv7xhCu67TuJJD1g==" />
            <div class="upgrade-type-selector" data-listing-upgrade-form-upgrade-type-selector="">
              <div class="row">
                <div class="col-lg-offset-1 col-lg-10">
                  <div class="row">
                    <div class="col-sm-3">
                      <div class="free upgrade-type upgrade-type-free current active" data-listing-upgrade-form-select-upgrade-type="free">
                        <div class="form-group radio_buttons optional upgrade_process_upgrade_type">
                          <input type="hidden" name="upgrade_process[upgrade_type]" value="" />
                          <span class="radio-inline">
                            <label for="upgrade_process_upgrade_type_">
                              <input class="radio_buttons optional" type="radio" value="" name="upgrade_process[upgrade_type]" id="upgrade_process_upgrade_type_" />Free </label>
                          </span>
                        </div>
                        <div class='until'>Current</div>
                      </div>
                    </div>
                    
                    
                  
                    <div class="clearfix"></div>
                    <div class="upgrade-types-bottom-text">
                      <p class="text-muted lead">To find out more about the different upgrade options <a href="/help-for-advertisers#upgrade-options" target="_blank">click here</a>. </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="checkout-fields" data-listing-upgrade-form-checkout="">
              <div class="row mx-0">
                <div class="col-sm-9 col-md-7 col-md-offset-1 col-lg-5 col-lg-offset-3 d-flex flex-column">
                  <div class="block pb-0 mb-3 upgrade-duration">
                    <div class="upgrade-title d-flex justify-content-between align-items-center mt-0 mb-2">
                      <h2 class="upgrade-name font-weight-bold my-0 mr-2" data-listing-upgrade-form-selected-upgrade-type-display=""></h2>
                      <a class="btn btn-primary d-flex align-items-center justify-content-center" data-listing-upgrade-form-back-to-upgrade-selection-btn="" href="#">
                        <i class="fa fa-arrow-left"></i>
                        <span class="ml-2">Back to selection</span>
                      </a>
                    </div>
                    <div class="border-top padding-top">
                      <div class="upgrade-duration__radios mb-3" data-listing-upgrade-duration-selector=""></div>
                      <div class="text-right w-100 pb-3">
                        <strong class="lead">Total: € <b data-listing-upgrade-form-total-price-display></b>
                        </strong>
                      </div>
                    </div>
                  </div>
                  <div class="block px-0 mb-3 hidden skip block--payment" id="balance-block">
                    <div class="right warn-text" data-card-payment-max-amount-warning="" data-message="We can only accept payments up to € {{maxAmount}} at a time.
												<br>
Period has been reduced from {{periodValueWas}} to {{periodValue}} days.
">
                    </div>
                    <ul class="payment-options">
                      <li>
                        <div class="credits-label px-3">
                          <h2 class="inline-block payment-options-box__title">Account Balance</h2>
                        </div>
                        <label class="p-3 d-flex flex-wrap align-items-center mb-0" for="upgrade_process_payment_method_account_balance">
                          <input type="radio" value="account_balance" name="upgrade_process[payment_method]" id="upgrade_process_payment_method_account_balance" />
                          <span>Available balance</span>
                          <span class="account-balance" data-listing-upgrade-form-account-balance-display="">€ 0</span>
                          <span data-listing-upgrade-form-purchase-account-balance-link="" style="margin-left:10px">
                            <a href="/my-profile/credits/purchase">Purchase account balance</a>
                          </span>
                        </label>
                      </li>
                    </ul>
                  </div>
                  <div class="col-sm-12 px-0 block order-2" id="payprocc-payments-block">
                    <h2 class="payment-options-box__title d-flex align-items-center p-3 mb-0">Local payment methods</h2>
                    <ul class="payment-options toggle-li-radio list-unstyled my-0">
                      <li>
                        <label class="p-3 mb-0 d-flex align-items-center" for="upgrade_process_payment_method_promptpay">
                          <input type="radio" value="promptpay" name="upgrade_process[payment_method]" id="upgrade_process_payment_method_promptpay" />
                          <span>Thailand</span>
                          <img alt="Payprocc Payments Promptpay logo" class="ml-3" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_promptpay_logo-6b70d10f79bdff9dc1efde97f390f89f91019db1b14c81389d20be94fd270275.svg" style="filter: brightness(2)" width="100" />
                        </label>
                      </li>
                      <li>
                        <label class="p-3 mb-0 d-flex align-items-center" for="upgrade_process_payment_method_momo">
                          <input type="radio" value="momo" name="upgrade_process[payment_method]" id="upgrade_process_payment_method_momo" />
                          <span>Vietnam</span>
                          <img alt="Payprocc Payments Momo logo" class="ml-3" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_momo_logo-41d3eadebd629ec10dd28d7c13683b8c5cf564e37eae34ae478de6d89a72b26c.svg" width="45" />
                        </label>
                      </li>
                      <li>
                        <label class="p-3 mb-0 d-flex align-items-center" for="upgrade_process_payment_method_viettelpay">
                          <input type="radio" value="viettelpay" name="upgrade_process[payment_method]" id="upgrade_process_payment_method_viettelpay" />
                          <span>Vietnam</span>
                          <img alt="Payprocc Payments Viettelpay logo" class="ml-3" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_viettelpay_logo-f99d7823ad8c66b5da8a2248c1877215a648cb16703f63c641584d684f73019b.svg" width="60" />
                        </label>
                      </li>
                    </ul>
                    <div class="px-4 data-payprocc-redirect-form" data-payprocc-redirect-form="" data-server-pub-key="MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAtuebhxbQpaZGaaEKq38DgjJUFH4Xowi1yK2mtmSmPAQXa9Y5+0kZhWBocCCgG/YaHPNd91UAk9nC/wtrzsimFNkfZ9CG7AHxKPgrUegmbDfZuMjtQXE9Lejfm4NVqXfZsnY66obecu84zjb6MlUC/X5zluU7P2MOB+FwJCufOAbHlDMFEqpNsA6YqhGsziHClgWcF/JsoC/0i3zqWibC4qWhjDbZolXn3q4xA6gz915P2ceFalKE27gdsHr6Q3swKwDudlKSCtu0zgeq9eEPRrfe/lU5vJoRpXV1vKc5BT/aX23XaJW2TKk/wcC5EjawZE6GIcG8HnwJ1WYrDAu2iLDlqHWJPZoOgy4eVjBCkfKTnn2M6A4MeNwgwh0aJuQ2O7bkGleqZyFPxAfxP6lfjAm1N93E2xibrLXf+pPi7NuRbdbLxDRxHJAu7XQx9ncO5eRcDStOu9i0MGwfeX68dxyd0/chLezhUZZkO2Xn/WWDWFlAiyCSi+hsIMcENPJdo98UTnLWdshBNy9DNKHCsOyLI4xvopGMnWYOUFZAqUdRmOlYrWVzAIub8CsIZEPNUbgAXP3eZtnvjsk3jTE7LvNDGXfARbSPbuB704CT8WRLcYq/2qKqWiQhcas1ylvU6paLZ9DDC7v7c8kWhKSfD/76DoN9/wAEl6ZgUULhu7kCAwEAAQ==" data-submit-url="/action/process_credit_card_payment">
                      <div class="payprocc-credit-card-form__first_line pt-1"></div>
                      <div class="payprocc-credit-card-form__submit-error alert alert-danger hidden" data-payprocc-redirect-form-submit-error=""></div>
                      <button class="d-flex align-items-center justify-content-center mt-4 btn btn-block btn-primary btn-lg btn-xs-block payprocc-credit-card-form__submit-button" type="submit">Pay € <span data-payprocc-credit-card-form-price-display></span>
                      </button>
                      <p class="hidden" data-payprocc-redirect-form-submitting-indicator="">Processing...</p>
                    </div>
                  </div>
                  <div class="block p-0 mb-3 hidden skip block--payment" id="crypto-payments-block">
                    <div aria-labelledby="lightningModalLabel" class="modal fade modal__lightning" id="lightningTutorialModal" role="dialog" tabindex="-1">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content p-0">
                          <img class="w-100 top-bleed-illustration" src="https://d257pz9kz95xf4.cloudfront.net/assets/lightning_illustration-27c10e59f4f47cfc7feff624c5aaa017578a4f70c269aad18af405155320ca9d.webp" />
                          <button type="button" class="close px-3 pt-2 position-absolute" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                          <div class="modal-body">
                            <h4 class="modal-title mb-3" id="lightningModalLabel">What is Lightning Network?</h4>
                            <p class="mb-4">It is a way to use Bitcoin to pay for things on the Internet quickly and inexpensively. Think of Lightning Network as a special box where you keep some of your Bitcoins. When you want to pay someone, you simply transfer some Bitcoins from your box to their box. When you're done using Lightning Network, you can close that special box and get your Bitcoins back into your account.</p>
                            <h4 class="mb-4">Lightning Network is great for small purchases. It is:</h4>
                            <ul class="mb-3 custom-marker custom-marker__bolt">
                              <li class="mb-2">
                                <strong>Fast</strong> - it means you can send money to others instantly, like sending a text message.
                              </li>
                              <li class="mb-2">
                                <strong>Cheap</strong> - transaction costs are very low, so you don't have to pay much for sending small amounts of money.
                              </li>
                              <li class="mb-2">
                                <strong>Secure</strong> - it's secure because the whole process is backed by Bitcoin technology, but it works much faster and cheaper than traditional Bitcoin payments.
                              </li>
                              <li class="mb-2">
                                <strong>Perfect for small transactions</strong> - it's a great solution if you want to pay for something small on the Internet, like a game, music, or coffee.
                              </li>
                            </ul>
                            <p>Look for Lightning Network in major cryptocurrency exchanges like <a href="https://www.binance.com/" target="_blank" rel="noopener noreferrer nofollow">binance.com</a> or <a href="https://www.kraken.com/" target="_blank" rel="noopener noreferrer nofollow">kraken.com</a>. There are also wallets offering bitcoin payments through Lightning Network like for example <a href="https://www.walletofsatoshi.com/" target="_blank" rel="noopener noreferrer nofollow">Wallet of Satoshi</a>. </p>
                          </div>
                          <div class="modal-footer pt-0">
                            <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="block__coinvert" id="crypto-payments-block">
                      <ul class="payment-options toggle-li-radio list-unstyled my-0">
                        <div class="crypto-label d-flex align-items-center justify-content-between mb-3 p-3 flex-wrap gap-3">
                          <h2 class="payment-options-box__title mb-0 mr-3">Crypto</h2>
                          <div class="badge badge-outline pl-3 py-2 d-flex align-items-center ml-sm-auto" style="font-size: 1em;">
                            <span>Get 10% bonus!</span>
                            <i class="fa fa-gift shake mx-2" style="font-size: 1.4em;"></i>
                          </div>
                        </div>
                        <li class="mb-1 position-relative">
                          <label class="mb-0 p-3 d-flex align-items-center" for="upgrade_process_payment_method_bitcoin">
                            <input type="radio" value="bitcoin" checked="checked" name="upgrade_process[payment_method]" id="upgrade_process_payment_method_bitcoin" />
                            <span>Bitcoin</span>
                            <img alt="Bitcoin icon" class="ml-2" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/btc-b522654df0046f6af0e8ac9f67078a87d26069445a01866c1c337bde91bbcd5f.svg" width="24" />
                            <div class="alert-crypto-notice alert mb-0 ml-3 p-2 position-relative" data-btc-notice="bitcoin" style="display:none">
                              <strong>Not available for payments below € <span class="ml-0"></span>
                              </strong> due to high transaction fees. We recommend <strong>Lightning Network</strong> for smaller amounts.
                            </div>
                            <p class="bonus-info mb-0 ml-auto small lh-initial" data-bonus-info="bitcoin">Get <strong class="text-primary">extra € <span class="ml-0" data-listing-btc-bonus-display>AMOUNT</span>
                              </strong> added to your balance! </p>
                          </label>
                          <div class="px-4 bitcoin-form bitcoin-form" data-bitcoin-form="" data-submit-url="/accounts/dubai_escorts_12/bitcoin_addresses/assign">
                            <p class="small pt-3">If the address is not accepted by your wallet, select "legacy" below</p>
                            <div id="bitcoin-form__address-type-input" class="form-group radio_buttons required bitcoin_address_type">
                              <input type="hidden" name="bitcoin[address_type]" value="" />
                              <span class="radio-inline">
                                <label class="d-flex align-items-center" for="bitcoin_address_type_bech32">
                                  <input class="radio_buttons required bitcoin-form__address-format-input__field" required="required" aria-required="true" type="radio" value="bech32" checked="checked" name="bitcoin[address_type]" id="bitcoin_address_type_bech32" />default (bc1 <small class="text-muted">xxxx...</small>) </label>
                              </span>
                              <span class="radio-inline">
                                <label class="d-flex align-items-center" for="bitcoin_address_type_p2sh">
                                  <input class="radio_buttons required bitcoin-form__address-format-input__field" required="required" aria-required="true" type="radio" value="p2sh" name="bitcoin[address_type]" id="bitcoin_address_type_p2sh" />legacy (3 <small class="text-muted">xxxx...</small>) </label>
                              </span>
                            </div>
                            <button class="d-flex align-items-center justify-content-center btn btn-block btn-primary btn-lg btn-xs-block mb-4 bitcoin-form__submit-button" data-bitcoin-submit="" type="submit">Generate address for € <span data-bitcoin-form-price-display></span>
                            </button>
                            <p class="text-center">Current rate is <strong>€98816 <i class="fa fa-exchange-alt"></i> 1 BTC </strong>
                            </p>
                            <div class="bitcoin-form__submit-error alert alert-danger hidden" data-bitcoin-form-submit-error=""></div>
                            <p class="hidden" data-bitcoin-form-submitting-indicator="">Processing...</p>
                            <div aria-hidden="true" aria-labelledby="bitcoinAddressModal" class="modal fade" id="bitcoinAddressModal" role="dialog" tabindex="-1">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content m-2 m-sm-0 p-0 p-sm-0">
                                  <div class="modal-body">
                                    <div class="d-flex justify-content-center py-4">
                                      <div class="qr-code"></div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 pb-3 justify-content-center">
                                      <span>Payment status:</span>
                                      <div data-ws-room="btc_cnfrm">
                                        <div class="badge">
                                          <i class="fa fa-clock pr-1"></i>
                                          <span class="text-uppercase">pending</span>
                                        </div>
                                      </div>
                                    </div>
                                    <table class="table table--borderless mb-4 mb-sm-2">
                                      <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1)">
                                        <td class="bitcoin-address text-primary fw-bold" id="bitcoinAddress" style="overflow-wrap:anywhere" title="Open in wallet app"></td>
                                        <td>
                                          <button style="min-width: 130px" type="button" class="mx-auto btn btn-ghost d-flex align-items-center px-2 py-0" data-copy-btn="#bitcoinAddress">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 icon icon-tabler icon-tabler-copy" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                              <rect x="8" y="8" width="12" height="12" rx="2"></rect>
                                              <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
                                            </svg>
                                            <span data-text="Copied">Copy address</span>
                                          </button>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>
                                          <span class="bitcoin-amount text-primary fw-bold" id="bitcoinAmount" title="Open in wallet app"></span>
                                          <span class="text-primary fw-bold">&nbsp;BTC</span>
                                        </td>
                                        <td>
                                          <button style="min-width: 130px" type="button" class="mx-auto btn btn-ghost d-flex align-items-center px-2 py-0" data-copy-btn="#bitcoinAmount">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 icon icon-tabler icon-tabler-copy" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                              <rect x="8" y="8" width="12" height="12" rx="2"></rect>
                                              <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
                                            </svg>
                                            <span data-text="Copied">Copy amount</span>
                                          </button>
                                        </td>
                                      </tr>
                                    </table>
                                    <p>Please transfer BTC to this address only.</p>
                                    <p>Once your transaction appears on blockchain you will receive <span class="upgrade"></span>
                                      <span data-bonus-info="bitcoin">10% of the amount in € will be added to your balance. </span>
                                    </p>
                                    <p>The bitcoin conversion rate is guaranteed for 3 hours and only for the BTC amount shown.</p>
                                    <p>You will receive an e-mail once your purchase is complete.</p>
                                  </div>
                                  <div class="modal-footer pt-0">
                                    <div class="d-flex align-items-center gap-5" data-buttons-swap="btc_cnfrm">
                                      <a class="btn btn-block btn-default" href="/my-profile">Need more time</a>
                                      <a class="btn btn-block btn-primary mt-0" href="/my-profile">I've sent it</a>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <noscript>
                            <p class="alert alert-warning">Please enable JavaScript.</p>
                          </noscript>
                        </li>
                        <li>
                          <label class="mb-0 p-3 d-flex align-items-center" for="upgrade_process_payment_method_lightning">
                            <input type="radio" value="lightning" name="upgrade_process[payment_method]" id="upgrade_process_payment_method_lightning" />
                            <span>Lightning Network</span>
                            <img alt="Lightning network icon" class="ml-2" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/btc_lightning-fa0277781a99cded862007ac4d2e5f5fa8fbafec4d9c2b59dbd3b2fc354e91a0.svg" width="42" />
                            <p class="bonus-info mb-0 ml-auto small lh-initial" data-bonus-info="lightning">Get <strong class="text-primary">extra € <span class="ml-0" data-listing-lightning-bonus-display>AMOUNT</span>
                              </strong> added to your balance! </p>
                          </label>
                          <p class="pl-4 py-2 pr-3">The Lightning Network is a cheaper and faster method for Bitcoin payments. <a class="fw-bold" data-target="#lightningTutorialModal" data-toggle="modal" href="#" style="color:#ffc64d; white-space: nowrap">
                              <span> How&nbsp;does&nbsp;it&nbsp;work</span>&nbsp; <i class="fa fa-question-circle"></i>
                            </a>
                          </p>
                          <div class="px-4 lightning-form" data-lightning-form="" data-submit-url="/accounts/dubai_escorts_12/lightnings/assign">
                            <button class="d-flex align-items-center justify-content-center btn btn-block btn-primary btn-lg btn-xs-block mb-4 bitcoin-form__submit-button" type="submit">Generate invoice for € <span data-lightning-form-price-display></span>
                            </button>
                            <p class="text-center">Current rate is <strong>€98816 <i class="fa fa-exchange-alt"></i> 1 BTC </strong>
                            </p>
                            <div class="lightning-form__submit-error alert alert-danger hidden" data-lightning-form-submit-error=""></div>
                            <p class="hidden" data-lightning-form-submitting-indicator="">Processing...</p>
                            <div aria-hidden="true" aria-labelledby="lightningAddressModal" class="modal" id="lightningAddressModal" role="dialog" tabindex="-1">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content m-2 m-sm-0 p-0 p-sm-0">
                                  <div class="modal-body">
                                    <div class="d-flex justify-content-center py-4">
                                      <div class="lightning-qr-code qr-code"></div>
                                    </div>
                                    <button type="button" class="mx-auto mb-3 btn btn-default d-flex align-items-center p-2" data-copy-btn="#lightningAddress">
                                      <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 icon icon-tabler icon-tabler-copy" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <rect x="8" y="8" width="12" height="12" rx="2"></rect>
                                        <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
                                      </svg>
                                      <span data-text="Copied">Copy invoice</span>
                                    </button>
                                    <div class="d-flex flex-wrap gap-2 pb-3">
                                      <span>Payment status:</span>
                                      <div data-ws-room="ln_cnfrm">
                                        <div class="badge">
                                          <i class="fa fa-clock pr-1"></i>
                                          <span class="text-uppercase">pending</span>
                                        </div>
                                      </div>
                                    </div>
                                    <div id="lightningAddress" class="lightning-address text-center text-primary pb-4" style="word-wrap: anywhere"></div>
                                    <p>By paying this invoice you will transfer <span class="lightning-amount text-primary"></span> satoshis. </p>
                                    <p>Once the invoice is paid you will receive <span class="lightning-upgrade"></span>
                                      <span data-bonus-info="lightning">10% of the amount in € will be added to your balance. </span>
                                    </p>
                                    <p>Lightning invoices have expiry dates. Making payment with expired invoices is not possible.</p>
                                    <p>You will receive an e-mail once your purchase is complete.</p>
                                  </div>
                                  <div class="modal-footer pt-0">
                                    <div class="d-flex align-items-center gap-5" data-buttons-swap="ln_cnfrm">
                                      <a class="btn btn-block btn-default" href="/my-profile">Need more time</a>
                                      <a class="btn btn-block btn-primary mt-0" href="/my-profile">I've sent it</a>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <noscript>
                            <p class="alert alert-warning">Please enable JavaScript.</p>
                          </noscript>
                        </li>
                      </ul>
                      <div class="text-center py-3" style="font-size:80%">Powered by <a href="https://coinvert.org" target="_blank">Coinvert.org</a>
                      </div>
                    </div>
                    <script>
                      var bitcoinMinAmount = "20";
                      var bitcoinLegacyMinAmount = "40";
                      var lightningMinAmount = "1";
                    </script>
                  </div>

                  <div class="block p-0 mb-3 block--payment" id="credit-cards-block">
                    <ul class="payment-options">
                      <div class="cards-label d-flex align-items-center p-3">
                        <h2 class="mb-0 mr-3 inline-block payment-options-box__title">Wallet</h2>
                        <svg width="30" style="fill: #ffff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                          <path d="M168 336C181.3 336 192 346.7 192 360C192 373.3 181.3 384 168 384H120C106.7 384 96 373.3 96 360C96 346.7 106.7 336 120 336H168zM360 336C373.3 336 384 346.7 384 360C384 373.3 373.3 384 360 384H248C234.7 384 224 373.3 224 360C224 346.7 234.7 336 248 336H360zM512 32C547.3 32 576 60.65 576 96V416C576 451.3 547.3 480 512 480H64C28.65 480 0 451.3 0 416V96C0 60.65 28.65 32 64 32H512zM512 80H64C55.16 80 48 87.16 48 96V128H528V96C528 87.16 520.8 80 512 80zM528 224H48V416C48 424.8 55.16 432 64 432H512C520.8 432 528 424.8 528 416V224z" />
                        </svg>
                      </div>
                      <li class="mb-3">
                        <label class="p-3 d-flex align-items-center mb-0 flex-wrap" for="upgrade_process_payment_method_emp_wpf">
                          <input type="radio" value="emp_wpf" name="upgrade_process[payment_method]" id="upgrade_process_payment_method_emp_wpf" />
                          <span class="mr-2">Wallet payment</span>
                          <img class="icon mr-1" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/mc_logo-1fee638879a55506111eef88a8369601147f17d09fa23d940350fee69fb9fc79.svg" />
                          <img class="icon" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/visa_logo-5f6bf07538a0b32cedb6babb58d8c28c7a917c26d4d7df3edd61be4980ddef6c.svg" />
                        </label>
                        <div class="px-4 cc-inputs-wrap data-emp-wpf-redirect-form hidden" data-emp-wpf-redirect-form="" data-server-pub-key="MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAtuebhxbQpaZGaaEKq38DgjJUFH4Xowi1yK2mtmSmPAQXa9Y5+0kZhWBocCCgG/YaHPNd91UAk9nC/wtrzsimFNkfZ9CG7AHxKPgrUegmbDfZuMjtQXE9Lejfm4NVqXfZsnY66obecu84zjb6MlUC/X5zluU7P2MOB+FwJCufOAbHlDMFEqpNsA6YqhGsziHClgWcF/JsoC/0i3zqWibC4qWhjDbZolXn3q4xA6gz915P2ceFalKE27gdsHr6Q3swKwDudlKSCtu0zgeq9eEPRrfe/lU5vJoRpXV1vKc5BT/aX23XaJW2TKk/wcC5EjawZE6GIcG8HnwJ1WYrDAu2iLDlqHWJPZoOgy4eVjBCkfKTnn2M6A4MeNwgwh0aJuQ2O7bkGleqZyFPxAfxP6lfjAm1N93E2xibrLXf+pPi7NuRbdbLxDRxHJAu7XQx9ncO5eRcDStOu9i0MGwfeX68dxyd0/chLezhUZZkO2Xn/WWDWFlAiyCSi+hsIMcENPJdo98UTnLWdshBNy9DNKHCsOyLI4xvopGMnWYOUFZAqUdRmOlYrWVzAIub8CsIZEPNUbgAXP3eZtnvjsk3jTE7LvNDGXfARbSPbuB704CT8WRLcYq/2qKqWiQhcas1ylvU6paLZ9DDC7v7c8kWhKSfD/76DoN9/wAEl6ZgUULhu7kCAwEAAQ==" data-submit-url="/action/process_credit_card_payment">
                          <div class="emp-credit-card-form__first_line pt-1"></div>
                          <div class="emp-credit-card-form__submit-error alert alert-danger hidden" data-emp-wpf-redirect-form-submit-error=""></div>
                          <button class="d-flex align-items-center justify-content-center mt-4 btn btn-block btn-primary btn-lg btn-xs-block emp-wpf-credit-card-form__submit-button" type="submit">Pay € <span data-emp-credit-card-form-price-display></span>
                          </button>
                          <p class="small text-muted pb-2 pt-3 text-center payment-info">Payments operated by B.V. is, Netherlands. "mrl_cc" will appear on your card statement. By proceeding you agree to our <a href="/toc">Terms of Service</a>
                          </p>
                          <p class="hidden" data-emp-wpf-redirect-form-submitting-indicator="">Processing...</p>
                        </div>
                        <noscript>
                          <p class="alert alert-warning">Please enable JavaScript.</p>
                        </noscript>
                      </li>
                    </ul>
                  </div>


                  <div class="block p-0 mb-3 block--payment" id="credit-cards-block">
                    <ul class="payment-options">
                      <div class="cards-label d-flex align-items-center p-3">
                        <h2 class="mb-0 mr-3 inline-block payment-options-box__title">Credit/Debit Card</h2>
                        <svg width="30" style="fill: #ffff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                          <path d="M168 336C181.3 336 192 346.7 192 360C192 373.3 181.3 384 168 384H120C106.7 384 96 373.3 96 360C96 346.7 106.7 336 120 336H168zM360 336C373.3 336 384 346.7 384 360C384 373.3 373.3 384 360 384H248C234.7 384 224 373.3 224 360C224 346.7 234.7 336 248 336H360zM512 32C547.3 32 576 60.65 576 96V416C576 451.3 547.3 480 512 480H64C28.65 480 0 451.3 0 416V96C0 60.65 28.65 32 64 32H512zM512 80H64C55.16 80 48 87.16 48 96V128H528V96C528 87.16 520.8 80 512 80zM528 224H48V416C48 424.8 55.16 432 64 432H512C520.8 432 528 424.8 528 416V224z" />
                        </svg>
                      </div>
                      <li class="mb-3">
                        <label class="p-3 d-flex align-items-center mb-0 flex-wrap" for="upgrade_process_payment_method_emp_wpf">
                          <input type="radio" value="emp_wpf" name="upgrade_process[payment_method]" id="upgrade_process_payment_method_emp_wpf" />
                          <span class="mr-2">Card payment</span>
                          <img class="icon mr-1" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/mc_logo-1fee638879a55506111eef88a8369601147f17d09fa23d940350fee69fb9fc79.svg" />
                          <img class="icon" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/visa_logo-5f6bf07538a0b32cedb6babb58d8c28c7a917c26d4d7df3edd61be4980ddef6c.svg" />
                        </label>
                        <div class="px-4 cc-inputs-wrap data-emp-wpf-redirect-form hidden" data-emp-wpf-redirect-form="" data-server-pub-key="MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAtuebhxbQpaZGaaEKq38DgjJUFH4Xowi1yK2mtmSmPAQXa9Y5+0kZhWBocCCgG/YaHPNd91UAk9nC/wtrzsimFNkfZ9CG7AHxKPgrUegmbDfZuMjtQXE9Lejfm4NVqXfZsnY66obecu84zjb6MlUC/X5zluU7P2MOB+FwJCufOAbHlDMFEqpNsA6YqhGsziHClgWcF/JsoC/0i3zqWibC4qWhjDbZolXn3q4xA6gz915P2ceFalKE27gdsHr6Q3swKwDudlKSCtu0zgeq9eEPRrfe/lU5vJoRpXV1vKc5BT/aX23XaJW2TKk/wcC5EjawZE6GIcG8HnwJ1WYrDAu2iLDlqHWJPZoOgy4eVjBCkfKTnn2M6A4MeNwgwh0aJuQ2O7bkGleqZyFPxAfxP6lfjAm1N93E2xibrLXf+pPi7NuRbdbLxDRxHJAu7XQx9ncO5eRcDStOu9i0MGwfeX68dxyd0/chLezhUZZkO2Xn/WWDWFlAiyCSi+hsIMcENPJdo98UTnLWdshBNy9DNKHCsOyLI4xvopGMnWYOUFZAqUdRmOlYrWVzAIub8CsIZEPNUbgAXP3eZtnvjsk3jTE7LvNDGXfARbSPbuB704CT8WRLcYq/2qKqWiQhcas1ylvU6paLZ9DDC7v7c8kWhKSfD/76DoN9/wAEl6ZgUULhu7kCAwEAAQ==" data-submit-url="/action/process_credit_card_payment">
                          <div class="emp-credit-card-form__first_line pt-1"></div>
                          <div class="emp-credit-card-form__submit-error alert alert-danger hidden" data-emp-wpf-redirect-form-submit-error=""></div>
                          <button class="d-flex align-items-center justify-content-center mt-4 btn btn-block btn-primary btn-lg btn-xs-block emp-wpf-credit-card-form__submit-button" type="submit">Pay € <span data-emp-credit-card-form-price-display></span>
                          </button>
                          <p class="small text-muted pb-2 pt-3 text-center payment-info">Payments operated by B.V. is, Netherlands. "mrl_cc" will appear on your card statement. By proceeding you agree to our <a href="/toc">Terms of Service</a>
                          </p>
                          <p class="hidden" data-emp-wpf-redirect-form-submitting-indicator="">Processing...</p>
                        </div>
                        <noscript>
                          <p class="alert alert-warning">Please enable JavaScript.</p>
                        </noscript>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="col-sm-3 col-md-4">
                  <div class="help-links"></div>
                </div>
              </div>
            </div>
          </form>
          <div class="hidden">
            <iframe width="10" height="10" id="threeDSMethodIframe" name="threeDSMethodIframe">
              <html>
                <body></body>
              </html>
            </iframe>
            <form id="threeDSMethodForm" name="threeDSMethodForm" enctype="application/x-www-form-urlencoded;charset=UTF-8" style="display: none" method="post" action="" target="threeDSMethodIframe">
              <input type="hidden" name="unique_id" id="unique_id" value="" />
              <input type="hidden" name="signature" id="signature" value="" />
            </form>
            <form id="threeDForm" name="ThreeDForm" style="display: none" method="post" action="">
              <input type="hidden" name="creq" id="creq" value="" />
              <input type="hidden" name="threeDSSessionData" id="threeDSSessionData" value="" />
            </form>
          </div>
          <script>
            var bitcoinBonusThreshold = "100";
          </script>
        </div>
      </div>
    </div>
