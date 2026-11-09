/* Evoory Theme JS - Optimized & Minimal */
(function(){
'use strict';

// wire:navigate swaps the page body without a reload, so bindings made once on
// DOM ready are lost on the way back to a page (the homepage city search went
// dead after visiting chat). init() therefore runs again on every
// livewire:navigated, binding each element only once; document-level listeners
// are added once per tab. The guard stops a second copy of this file (Livewire
// re-runs body scripts on navigation) from adding them again.
if(window.__evooryThemeLoaded)return;
window.__evooryThemeLoaded=true;

// Wait for DOM ready
function ready(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn)}

// True the first time it's called for an element (then marks it bound).
function once(el,key){
  if(!el||el.dataset[key])return false;
  el.dataset[key]='1';
  return true;
}

// Set by init() for the country modal on the current page (used by Escape).
var closeCurrentCountryModal=null;

function init(){
  // City Search Functionality
  var searchForm=document.querySelector('.ev-search-form');
  var searchInput=searchForm?searchForm.querySelector('.ev-city-search'):null;
  var dropdown=searchForm?searchForm.querySelector('.ev-dropdown'):null;
  var cityIdInput=document.querySelector('#city_id');
  var searchTimeout;
  var csrfToken=document.querySelector('meta[name="csrf-token"]');

  if(searchInput&&dropdown&&once(searchInput,'evCitySearch')){
    // Search cities on input
    searchInput.addEventListener('input',function(){
      var query=this.value.trim();
      clearTimeout(searchTimeout);

      if(query.length<2){
        dropdown.classList.remove('show');
        dropdown.innerHTML='';
        return;
      }

      searchTimeout=setTimeout(function(){
        console.log('Searching for:', query);
        fetch('/cities/search',{
          method:'POST',
          headers:{'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':csrfToken?csrfToken.content:''},
          body:'query='+encodeURIComponent(query)+'&_token='+(csrfToken?csrfToken.content:'')
        })
        .then(function(r){
          console.log('Response status:', r.status);
          return r.json();
        })
        .then(function(data){
          console.log('Cities data:', data);
          if(data&&data.length>0){
            var html='';
            data.forEach(function(city){
              html+='<div class="ev-dropdown-item" data-id="'+city.id+'" data-name="'+city.name+'" data-slug="'+(city.slug||'')+'">'+city.name+(city.country?' - '+city.country:'')+'</div>';
            });
            dropdown.innerHTML=html;
            dropdown.classList.add('show');
          }else{
            dropdown.innerHTML='<div class="ev-dropdown-item">No cities found</div>';
            dropdown.classList.add('show');
          }
        })
        .catch(function(err){
          console.error('City search error:', err);
          dropdown.classList.remove('show');
        });
      },300);
    });

    // Select city from dropdown
    dropdown.addEventListener('click',function(e){
      var item=e.target.closest('.ev-dropdown-item');
      if(item&&item.dataset.id){
        searchInput.value=item.dataset.name;
        if(cityIdInput)cityIdInput.value=item.dataset.id;
        searchInput.dataset.slug=item.dataset.slug||'';
        dropdown.classList.remove('show');
      }
    });

    // Focus handling
    searchInput.addEventListener('focus',function(){
      if(this.value.trim().length>=2&&dropdown.innerHTML){
        dropdown.classList.add('show');
      }
    });
  }

  // Form submission
  if(searchForm&&once(searchForm,'evSearchSubmit')){
    searchForm.addEventListener('submit',function(e){
      e.preventDefault();
      var city=searchInput?searchInput.value.trim():'';
      var slug=searchInput?searchInput.dataset.slug:'';
      if(city){
        var url=slug?'female-escorts-in-'+slug:'female-escorts-in-'+city.toLowerCase().replace(/\s+/g,'-').replace(/[^\w-]/g,'');
        window.location.href=url;
      }
    });
  }

  // Alphabet Filter
  var alphaButtons=document.querySelectorAll('.ev-alpha-btn');
  var countryCards=document.querySelectorAll('.ev-country-card');

  alphaButtons.forEach(function(btn){
    if(!once(btn,'evAlpha'))return;
    btn.addEventListener('click',function(){
      var letter=this.dataset.letter;

      // Update active state
      alphaButtons.forEach(function(b){b.classList.remove('active')});
      this.classList.add('active');

      // Filter countries
      countryCards.forEach(function(card){
        var name=card.dataset.country||card.querySelector('.ev-country-name').textContent;
        if(!letter||letter==='all'||name.charAt(0).toUpperCase()===letter.toUpperCase()){
          card.style.display='';
        }else{
          card.style.display='none';
        }
      });
    });
  });

  // Country cities modal
  var countryModal=document.querySelector('#ev-country-modal');
  var countryModalBody=document.querySelector('#ev-country-modal-body');
  var countryModalSearch=document.querySelector('#ev-country-modal-search');
  var countryModalIso=document.querySelector('.ev-country-modal-iso');
  var countryModalName=document.querySelector('.ev-country-modal-name');
  var countryModalCount=document.querySelector('.ev-country-modal-count');
  var countryModalCloseButtons=document.querySelectorAll('[data-close-country-modal]');
  var loadedCities=[];

  function slugifyCityName(value){
    return (value||'').toLowerCase().replace(/\s+/g,'-').replace(/[^\w-]/g,'');
  }

  function buildCityRows(cities){
    var grouped={};
    cities.forEach(function(city){
      var first=(city.name||'').trim().charAt(0).toUpperCase()||'#';
      if(!grouped[first])grouped[first]=[];
      grouped[first].push(city);
    });

    var letters=Object.keys(grouped).sort();
    if(!letters.length){
      return '<div class="ev-country-empty">No cities found</div>';
    }

    var html='';
    letters.forEach(function(letter){
      html+='<div class="ev-city-group">';
      html+='<div class="ev-city-group-head"><span class="ev-city-letter">'+letter+'</span><span class="ev-city-line"></span></div>';
      html+='<div class="ev-city-list">';
      grouped[letter].forEach(function(city){
        var slug=city.slug||slugifyCityName(city.name);
        html+='<a href="female-escorts-in-'+slug+'" class="ev-city-chip">'+city.name+'</a>';
      });
      html+='</div></div>';
    });
    return html;
  }

  function renderCountryCities(list){
    if(!countryModalBody)return;
    countryModalBody.innerHTML=buildCityRows(list||[]);
  }

  function openCountryModal(){
    if(!countryModal)return;
    countryModal.classList.add('show');
    countryModal.setAttribute('aria-hidden','false');
    document.body.style.overflow='hidden';
    if(countryModalSearch)countryModalSearch.focus();
  }

  function closeCountryModal(){
    if(!countryModal)return;
    countryModal.classList.remove('show');
    countryModal.setAttribute('aria-hidden','true');
    document.body.style.overflow='';
    if(countryModalSearch)countryModalSearch.value='';
  }
  closeCurrentCountryModal=countryModal?function(){
    if(countryModal.classList.contains('show'))closeCountryModal();
  }:null;

  function loadCountryCities(code, fallbackName, fallbackCount){
    if(!countryModalBody)return;
    countryModalBody.innerHTML='<div class="ev-country-empty">Loading cities...</div>';
    fetch('/countries/'+encodeURIComponent(code)+'/cities',{headers:{'Accept':'application/json'}})
      .then(function(r){
        if(!r.ok)throw new Error('Request failed');
        return r.json();
      })
      .then(function(data){
        var country=data&&data.country?data.country:{};
        loadedCities=Array.isArray(data&&data.cities)?data.cities:[];
        if(countryModalIso)countryModalIso.textContent=(country.iso||String(code||'').toUpperCase());
        if(countryModalName)countryModalName.textContent=(country.name||fallbackName||'Country');
        if(countryModalCount)countryModalCount.textContent=((country.count!=null?country.count:fallbackCount)||loadedCities.length)+' Cities';
        renderCountryCities(loadedCities);
      })
      .catch(function(){
        loadedCities=[];
        if(countryModalIso)countryModalIso.textContent=String(code||'').toUpperCase();
        if(countryModalName)countryModalName.textContent=fallbackName||'Country';
        if(countryModalCount)countryModalCount.textContent=(fallbackCount||0)+' Cities';
        countryModalBody.innerHTML='<div class="ev-country-empty">Unable to load cities</div>';
      });
  }

  countryCards.forEach(function(card){
    if(!once(card,'evCountryCard'))return;
    card.addEventListener('click',function(e){
      e.preventDefault();
      var code=card.dataset.countryCode||'';
      var name=card.dataset.country||'';
      var count=card.dataset.countryCount||'';
      if(countryModalSearch)countryModalSearch.value='';
      if(countryModalIso)countryModalIso.textContent=String(code).toUpperCase();
      if(countryModalName)countryModalName.textContent=name;
      if(countryModalCount)countryModalCount.textContent=(count||0)+' Cities';
      openCountryModal();
      loadCountryCities(code,name,count);
    });
  });

  if(countryModalSearch&&once(countryModalSearch,'evCountrySearch')){
    countryModalSearch.addEventListener('input',function(){
      var query=this.value.trim().toLowerCase();
      if(!query){
        renderCountryCities(loadedCities);
        return;
      }
      var filtered=loadedCities.filter(function(city){
        return (city.name||'').toLowerCase().indexOf(query)!==-1;
      });
      renderCountryCities(filtered);
    });
  }

  countryModalCloseButtons.forEach(function(btn){
    if(once(btn,'evCountryClose'))btn.addEventListener('click',closeCountryModal);
  });

  // Mobile menu toggle
  var mobileMenuBtn=document.querySelector('.ev-mobile-menu-btn');
  var mobileMenu=document.querySelector('.ev-mobile-menu');

  if(mobileMenuBtn&&mobileMenu&&once(mobileMenuBtn,'evMobileMenu')){
    mobileMenuBtn.addEventListener('click',function(){
      mobileMenu.classList.toggle('show');
      this.setAttribute('aria-expanded',mobileMenu.classList.contains('show'));
    });
  }

  // Language selector
  var langBtn=document.querySelector('.ev-lang-btn');
  var langDropdown=document.querySelector('.ev-lang-dropdown');

  if(langBtn&&langDropdown&&once(langBtn,'evLang')){
    langBtn.addEventListener('click',function(e){
      e.stopPropagation();
      langDropdown.classList.toggle('show');
    });
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(function(a){
    if(!once(a,'evSmoothScroll'))return;
    a.addEventListener('click',function(e){
      var target=document.querySelector(this.getAttribute('href'));
      if(target){
        e.preventDefault();
        target.scrollIntoView({behavior:'smooth',block:'start'});
      }
    });
  });

  // Lazy load optimization - defer non-critical images
  if('IntersectionObserver'in window){
    var lazyImages=document.querySelectorAll('img[data-src]');
    var imageObserver=new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          var img=entry.target;
          img.src=img.dataset.src;
          img.removeAttribute('data-src');
          imageObserver.unobserve(img);
        }
      });
    });
    lazyImages.forEach(function(img){if(once(img,'evLazy'))imageObserver.observe(img)});
  }

  console.log('Evoory theme initialized');
}

// Document-level listeners, added once; they look elements up when they fire
// so they keep working on pages swapped in by wire:navigate.
document.addEventListener('click',function(e){
  // Close the city dropdown on outside click
  if(!e.target.closest('.ev-relative')){
    document.querySelectorAll('.ev-search-form .ev-dropdown').forEach(function(d){d.classList.remove('show')});
  }
  // Close the language dropdown (its button stops propagation of its own click)
  var langDropdown=document.querySelector('.ev-lang-dropdown');
  if(langDropdown)langDropdown.classList.remove('show');
});

document.addEventListener('keydown',function(e){
  if(e.key==='Escape'&&closeCurrentCountryModal)closeCurrentCountryModal();
});

ready(init);
document.addEventListener('livewire:navigated',init);
})();
