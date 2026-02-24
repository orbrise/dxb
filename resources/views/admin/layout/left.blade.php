<nav class="sidebar-nav">
                <ul class="nav in side-menu">
                    

                    <li class="{{ Route::currentRouteName() == 'admin.dashboard' ? 'current-page' : '' }}">
                        <a class="ripple" href="{{route('admin.dashboard')}}">
                           <i class="list-icon material-icons">show_chart</i>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li><!--end -->
                    
                    @php
                        $profileRoutes = ['admin.profiles.index','admin.verifications', 'admin.genders', 'admin.busts', 'admin.haircolors', 'admin.ethnicities', 'admin.languages', 'admin.orientations'];
                        $isProfileSectionOpen = in_array(Route::currentRouteName(), $profileRoutes) || str_contains(Route::currentRouteName(), 'admin.profiles');
                    @endphp
                    <li class="menu-item-has-children {{ $isProfileSectionOpen ? 'current-page' : '' }}">
                        <a href="javascript:void(0);" class="ripple" aria-expanded="{{ $isProfileSectionOpen ? 'true' : 'false' }}">
                            <span class="color-color-scheme">
                                <i class="list-icon material-icons">face</i>
                                <span class="hide-menu">Profiles</span>
                            </span>
                        </a>
                        <ul class="list-unstyled sub-menu collapse {{ $isProfileSectionOpen ? 'in' : '' }}" aria-expanded="{{ $isProfileSectionOpen ? 'true' : 'false' }}" style="{{ $isProfileSectionOpen ? '' : 'height: 0px;' }}">
                            <li class="{{ Route::currentRouteName() == 'admin.profiles.index' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.profiles.index')}}">
                                    <span class="hide-menu">All Profiles</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.verifications' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.verifications')}}">
                                    <span class="hide-menu">Photo Verifications</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.genders' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.genders')}}">
                                    <span class="hide-menu">Gender</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.busts' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.busts')}}">
                                    <span class="hide-menu">Busts</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.haircolors' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.haircolors')}}">
                                    <span class="hide-menu">Hair Colors</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.ethnicities' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.ethnicities')}}">
                                    <span class="hide-menu">Ethnicities</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.languages' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.languages')}}">
                                    <span class="hide-menu">Languages</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.orientations' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.orientations')}}">
                                    <span class="hide-menu">Orientation</span>
                                </a>
                            </li>
                        </ul>
                    </li> 
                    
                    
                      <li class="{{ Route::currentRouteName() == 'admin.users' ? 'current-page' : '' }}">
                        <a class="ripple" href="{{route('admin.users')}}">
                           <i class="list-icon material-icons">account_circle</i>
                            <span class="hide-menu"> Users</span>
                        </a>
                    </li> 
                    
                    @php
                        $supportRoutes = ['admin.whatsapp.index', 'admin.whatsapp.quick-chat', 'admin.whatsapp.settings', 'admin.whatsapp.rotation.index', 'admin.telegram.quick-chat'];
                        $isSupportSectionOpen = in_array(Route::currentRouteName(), $supportRoutes) || str_contains(Route::currentRouteName(), 'admin.whatsapp') || str_contains(Route::currentRouteName(), 'admin.telegram');
                    @endphp
                    <li class="menu-item-has-children {{ $isSupportSectionOpen ? 'current-page' : '' }}">
                        <a href="javascript:void(0);" class="ripple" aria-expanded="{{ $isSupportSectionOpen ? 'true' : 'false' }}">
                            <span class="color-color-scheme">
                                <i class="list-icon material-icons">headset_mic</i>
                                <span class="hide-menu">Support</span>
                            </span>
                        </a>
                        <ul class="list-unstyled sub-menu collapse {{ $isSupportSectionOpen ? 'in' : '' }}" aria-expanded="{{ $isSupportSectionOpen ? 'true' : 'false' }}" style="{{ $isSupportSectionOpen ? '' : 'height: 0px;' }}">
                            <li class="{{ Route::currentRouteName() == 'admin.whatsapp.quick-chat' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.whatsapp.quick-chat')}}">
                                    <span class="hide-menu">WhatsApp</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.telegram.quick-chat' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.telegram.quick-chat')}}">
                                    <span class="hide-menu">Telegram</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.whatsapp.index' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.whatsapp.index')}}">
                                    <span class="hide-menu">WhatsApp API</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.whatsapp.rotation.index' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.whatsapp.rotation.index')}}">
                                    <span class="hide-menu">Rotation Messages</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    @php
                        $blogRoutes = ['admin.blog.posts.index', 'admin.blog.posts.create', 'admin.blog.posts.edit', 'admin.blog.posts.show', 'admin.blog.categories.index', 'admin.blog.categories.create', 'admin.blog.categories.edit', 'admin.blog.tags.index', 'admin.blog.tags.create', 'admin.blog.tags.edit', 'admin.blog.settings.index'];
                        $isBlogSectionOpen = in_array(Route::currentRouteName(), $blogRoutes) || str_contains(Route::currentRouteName(), 'admin.blog');
                    @endphp
                    <li class="menu-item-has-children {{ $isBlogSectionOpen ? 'current-page' : '' }}">
                        <a href="javascript:void(0);" class="ripple" aria-expanded="{{ $isBlogSectionOpen ? 'true' : 'false' }}">
                            <span class="color-color-scheme">
                                <i class="list-icon material-icons">description</i>
                                <span class="hide-menu">Blog</span>
                            </span>
                        </a>
                        <ul class="list-unstyled sub-menu collapse {{ $isBlogSectionOpen ? 'in' : '' }}" aria-expanded="{{ $isBlogSectionOpen ? 'true' : 'false' }}" style="{{ $isBlogSectionOpen ? '' : 'height: 0px;' }}">
                            <li class="{{ str_contains(Route::currentRouteName(), 'admin.blog.posts') ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.blog.posts.index')}}">
                                    <span class="hide-menu">Posts</span>
                                </a>
                            </li>
                            <li class="{{ str_contains(Route::currentRouteName(), 'admin.blog.categories') ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.blog.categories.index')}}">
                                    <span class="hide-menu">Categories</span>
                                </a>
                            </li>
                            <li class="{{ str_contains(Route::currentRouteName(), 'admin.blog.tags') ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.blog.tags.index')}}">
                                    <span class="hide-menu">Tags</span>
                                </a>
                            </li>
                            <li class="{{ str_contains(Route::currentRouteName(), 'admin.blog.settings') ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.blog.settings.index')}}">
                                    <span class="hide-menu">SEO Settings</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                             
                    <li class="{{ str_contains(Route::currentRouteName(), 'admin.auctions') ? 'current-page' : '' }}">
                        <a class="ripple" href="{{route('admin.auctions.index')}}">
                            <i class="list-icon material-icons">account_balance</i>
                            <span class="hide-menu">Auctions</span>
                        </a>
                    </li><!--end -->

                    @php
                        $internationalRoutes = ['admin.cities', 'admin.countries', 'admin.currencies'];
                        $isInternationalSectionOpen = in_array(Route::currentRouteName(), $internationalRoutes);
                    @endphp
                    <li class="menu-item-has-children {{ $isInternationalSectionOpen ? 'current-page' : '' }}">
                        <a href="javascript:void(0);" class="ripple" aria-expanded="{{ $isInternationalSectionOpen ? 'true' : 'false' }}">
                            <span class="color-color-scheme">
                                <i class="list-icon material-icons">public</i>
                                <span class="hide-menu">International</span>
                            </span>
                        </a>
                        <ul class="list-unstyled sub-menu collapse {{ $isInternationalSectionOpen ? 'in' : '' }}" aria-expanded="{{ $isInternationalSectionOpen ? 'true' : 'false' }}" style="{{ $isInternationalSectionOpen ? '' : 'height: 0px;' }}">
                            <li class="{{ Route::currentRouteName() == 'admin.cities' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.cities')}}">
                                    
                                    <span class="hide-menu">Cities</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.countries' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.countries')}}">
                                    
                                    <span class="hide-menu">Countries</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.currencies' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.currencies')}}">
                                    
                                    <span class="hide-menu">Currencies</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    


                     @php
                        $settingsRoutes = ['admin.appsetting', 'admin.url-redirects.index', 'admin.url-aliases.index', 'admin.mail-settings.index', 'seo.index', 'default-seo.index'];
                        $isSettingsSectionOpen = in_array(Route::currentRouteName(), $settingsRoutes) || 
                                                 str_contains(Route::currentRouteName(), 'admin.appsetting') ||
                                                 str_contains(Route::currentRouteName(), 'admin.url-redirects') ||
                                                 str_contains(Route::currentRouteName(), 'admin.url-aliases') ||
                                                 str_contains(Route::currentRouteName(), 'admin.mail-settings') ||
                                                 str_contains(Route::currentRouteName(), 'seo') ||
                                                 str_contains(Route::currentRouteName(), 'default-seo');
                    @endphp
                    <li class="menu-item-has-children {{ $isSettingsSectionOpen ? 'current-page' : '' }}">
                        <a href="javascript:void(0);" class="ripple" aria-expanded="{{ $isSettingsSectionOpen ? 'true' : 'false' }}">
                            <span class="color-color-scheme">
                                <i class="list-icon material-icons">settings</i>
                                <span class="hide-menu">Settings</span>
                            </span>
                        </a>
                        <ul class="list-unstyled sub-menu collapse {{ $isSettingsSectionOpen ? 'in' : '' }}" aria-expanded="{{ $isSettingsSectionOpen ? 'true' : 'false' }}" style="{{ $isSettingsSectionOpen ? '' : 'height: 0px;' }}">
                            <li class="{{ Route::currentRouteName() == 'admin.appsetting' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.appsetting')}}">
                                    <span class="hide-menu">App Setup</span>
                                </a>
                            </li>
                            <li class="{{ str_contains(Route::currentRouteName(), 'admin.url-redirects') ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.url-redirects.index')}}">
                                    <span class="hide-menu">URL Redirects</span>
                                </a>
                            </li>
                            <li class="{{ str_contains(Route::currentRouteName(), 'admin.url-aliases') ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.url-aliases.index')}}">
                                    <span class="hide-menu">URL Aliases</span>
                                </a>
                            </li>
                            <li class="{{ str_contains(Route::currentRouteName(), 'admin.mail-settings') ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.mail-settings.index')}}">
                                    <span class="hide-menu">Mail Settings</span>
                                </a>
                            </li>
                            <li class="{{ str_contains(Route::currentRouteName(), 'seo') && !str_contains(Route::currentRouteName(), 'default-seo') ? 'active' : '' }}">
                                <a class="ripple" href="{{route('seo.index')}}">
                                    <span class="hide-menu">SEO Keywords</span>
                                </a>
                            </li>
                            <li class="{{ str_contains(Route::currentRouteName(), 'default-seo') ? 'active' : '' }}">
                                <a class="ripple" href="{{route('default-seo.index')}}">
                                    <span class="hide-menu">Default SEO Settings</span>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="{{ str_contains(Route::currentRouteName(), 'admin.listings') ? 'current-page' : '' }}">
                        <a class="ripple" href="{{route('admin.listings.index')}}">
                           <i class="list-icon material-icons">dashboard</i>
                            <span class="hide-menu">Categories</span>
                        </a>
                    </li>


                   

                    


                  



                    

                    <li class="{{ str_contains(request()->url(), '/admin/pages') || str_contains(Route::currentRouteName(), 'pages') ? 'current-page' : '' }}">
                        <a class="ripple" href="{{url('admin/pages')}}">
                           <i class="list-icon material-icons">assistant_photo</i>
                            <span class="hide-menu">Pages</span>
                        </a>
                    </li>

                    <li class="{{ str_contains(Route::currentRouteName(), 'questions') ? 'current-page' : '' }}">
                        <a class="ripple" href="{{route('questions.index')}}">
                            <i class="list-icon material-icons">question_answer</i>
                            <span class="hide-menu">Questions</span>
                        </a>
                    </li>

                    <li class="{{ str_contains(Route::currentRouteName(), 'reviews') ? 'current-page' : '' }}">
                        <a class="ripple" href="{{route('reviews.index')}}">
                           <i class="list-icon material-icons">recent_actors</i>
                            <span class="hide-menu">Reviews</span>
                        </a>
                    </li>

                    <li class="{{ str_contains(Route::currentRouteName(), 'admin.profile-reports') ? 'current-page' : '' }}">
                        <a class="ripple" href="{{route('admin.profile-reports.index')}}">
                           <i class="list-icon material-icons">flag</i>
                            <span class="hide-menu">Profile Reports</span>
                            @php
                                $pendingReports = \App\Models\Report::where('status', 'pending')->count();
                            @endphp
                            @if($pendingReports > 0)
                                <span class="badge badge-pill badge-danger ml-2">{{ $pendingReports }}</span>
                            @endif
                        </a>
                    </li>

                    @php
                        $packageRoutes = ['admin.packages', 'admin.global.packages'];
                        $isPackageSectionOpen = in_array(Route::currentRouteName(), $packageRoutes);
                    @endphp
                    <li class="menu-item-has-children {{ $isPackageSectionOpen ? 'current-page' : '' }}">
                        <a href="javascript:void(0);" class="ripple" aria-expanded="{{ $isPackageSectionOpen ? 'true' : 'false' }}">
                            <i class="list-icon material-icons">card_giftcard</i>
                            <span class="hide-menu">Packages</span>
                        </a>
                        <ul class="list-unstyled sub-menu collapse {{ $isPackageSectionOpen ? 'in' : '' }}" aria-expanded="{{ $isPackageSectionOpen ? 'true' : 'false' }}" style="{{ $isPackageSectionOpen ? '' : 'height: 0px;' }}">
                            <li class="{{ Route::currentRouteName() == 'admin.packages' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.packages')}}">
                                    <span class="hide-menu">Country Specific</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'admin.global.packages' ? 'active' : '' }}">
                                <a class="ripple" href="{{route('admin.global.packages')}}">
                                    <span class="hide-menu">Global Packages</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ Route::currentRouteName() == 'admin.wallet' ? 'current-page' : '' }}">
                        <a class="ripple" href="{{route('admin.wallet')}}">
                         <i class="list-icon material-icons">account_balance_wallet</i>
                            <span class="hide-menu">Wallet</span>
                        </a>
                    </li> 

                    <li>
                        <a class="ripple" href="javascript:void(0);" onclick="event.preventDefault(); if(confirm('Are you sure you want to clear all caches?')) { document.getElementById('clear-cache-form').submit(); }">
                           <i class="list-icon material-icons">cached</i>
                            <span class="hide-menu">Clear Cache</span>
                        </a>
                        <form id="clear-cache-form" action="{{ route('admin.clear-cache') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>

                    
                    
                </ul><!--end navbar-nav--->
               
          </nav>



