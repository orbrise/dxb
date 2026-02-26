/* Evoory Theme JS - Optimized & Minimal */
(function(){
'use strict';

// Wait for DOM ready
function ready(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn)}

ready(function(){
  // City Search Functionality
  var searchForm=document.querySelector('.ev-search-form');
  var searchInput=searchForm?searchForm.querySelector('.ev-city-search'):null;
  var dropdown=searchForm?searchForm.querySelector('.ev-dropdown'):null;
  var cityIdInput=document.querySelector('#city_id');
  var searchTimeout;
  var csrfToken=document.querySelector('meta[name="csrf-token"]');
  
  if(searchInput&&dropdown){
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
    
    // Close dropdown on outside click
    document.addEventListener('click',function(e){
      if(!e.target.closest('.ev-relative')){
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
  if(searchForm){
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
  
  // Mobile menu toggle
  var mobileMenuBtn=document.querySelector('.ev-mobile-menu-btn');
  var mobileMenu=document.querySelector('.ev-mobile-menu');
  
  if(mobileMenuBtn&&mobileMenu){
    mobileMenuBtn.addEventListener('click',function(){
      mobileMenu.classList.toggle('show');
      this.setAttribute('aria-expanded',mobileMenu.classList.contains('show'));
    });
  }
  
  // Language selector
  var langBtn=document.querySelector('.ev-lang-btn');
  var langDropdown=document.querySelector('.ev-lang-dropdown');
  
  if(langBtn&&langDropdown){
    langBtn.addEventListener('click',function(e){
      e.stopPropagation();
      langDropdown.classList.toggle('show');
    });
    document.addEventListener('click',function(){
      langDropdown.classList.remove('show');
    });
  }
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(function(a){
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
    lazyImages.forEach(function(img){imageObserver.observe(img)});
  }
  
  console.log('Evoory theme initialized');
});
})();
