// Smooth scroll for in-page links
document.addEventListener('click', function(e){
  const target = e.target.closest('a[href^="#"]');
  if(!target) return;
  const id = target.getAttribute('href');
  if(id.length > 1){
    const el = document.querySelector(id);
    if(el){
      e.preventDefault();
      el.scrollIntoView({behavior:'smooth', block:'start'});
    }
  }
});

// Sticky header shrink on scroll
const header = document.querySelector('[data-header]');
function onScroll(){
  if(window.scrollY > 10){
    document.body.classList.add('is-scrolled');
  } else {
    document.body.classList.remove('is-scrolled');
  }
}
onScroll();
window.addEventListener('scroll', onScroll, {passive:true});

// Mobile nav toggle
const nav = document.querySelector('.nav');
const toggle = document.querySelector('.nav-toggle');
if(toggle && nav){
  toggle.addEventListener('click', () => {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!expanded));
    nav.setAttribute('aria-expanded', String(!expanded));
  });
}

// Dynamic year in footer
const yearEl = document.querySelector('[data-year]');
if(yearEl){ yearEl.textContent = String(new Date().getFullYear()); }

// Modal: Book demo
function openModal(id){
  const m = document.getElementById('modal-' + id);
  if(!m) return;
  m.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}
function closeModals(){
  document.querySelectorAll('.modal').forEach(m => m.setAttribute('aria-hidden','true'));
  document.body.style.overflow = '';
}
document.addEventListener('click', function(e){
  const openBtn = e.target.closest('[data-open-modal]');
  if(openBtn){ e.preventDefault(); openModal(openBtn.getAttribute('data-open-modal')); return; }
  const closeBtn = e.target.closest('[data-close-modal]');
  if(closeBtn){ e.preventDefault(); closeModals(); return; }
});
document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeModals(); });

// Dropdown: Login
document.addEventListener('click', function(e){
  const toggle = e.target.closest('[data-dropdown-toggle]');
  const dropdown = e.target.closest('[data-dropdown]');
  document.querySelectorAll('[data-dropdown]').forEach(d => {
    if(toggle && d.contains(toggle)){
      const expanded = (d.getAttribute('aria-expanded') === 'true');
      d.setAttribute('aria-expanded', String(!expanded));
    } else if(!dropdown || !d.contains(dropdown)){
      d.setAttribute('aria-expanded','false');
    }
  });
});

// (Removed audience tabs and pet lookup JS per new design)

