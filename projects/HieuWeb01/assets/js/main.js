/**
 * HieuMini - Client Javascript Interactions (UI/UX Pro Max Edition)
 */

document.addEventListener('DOMContentLoaded', () => {
  // ==========================================
  // 1. Top Announcement Bar Carousel Ticker
  // ==========================================
  const topbarItems = document.querySelectorAll('.topbar-item');
  if (topbarItems.length > 1) {
    let activeTopbarIdx = 0;
    setInterval(() => {
      topbarItems[activeTopbarIdx].classList.remove('active');
      activeTopbarIdx = (activeTopbarIdx + 1) % topbarItems.length;
      topbarItems[activeTopbarIdx].classList.add('active');
    }, 4500);
  }

  // ==========================================
  // 2. Sticky Header Elevation on Scroll
  // ==========================================
  const siteHeader = document.getElementById('site-header');
  if (siteHeader) {
    const handleScroll = () => {
      if (window.scrollY > 20) {
        siteHeader.classList.add('scrolled');
      } else {
        siteHeader.classList.remove('scrolled');
      }
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
  }

  // ==========================================
  // 3. Mobile Offcanvas Navigation Drawer
  // ==========================================
  const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
  const mobileDrawer = document.getElementById('mobile-drawer');
  const mobileDrawerBackdrop = document.getElementById('mobile-drawer-backdrop');
  const drawerCloseBtn = document.getElementById('drawer-close-btn');

  function openDrawer() {
    if (mobileDrawer && mobileDrawerBackdrop) {
      mobileDrawer.classList.add('active');
      mobileDrawerBackdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
      if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'true');
    }
  }

  function closeDrawer() {
    if (mobileDrawer && mobileDrawerBackdrop) {
      mobileDrawer.classList.remove('active');
      mobileDrawerBackdrop.classList.remove('active');
      document.body.style.overflow = '';
      if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'false');
    }
  }

  if (mobileMenuToggle) mobileMenuToggle.addEventListener('click', openDrawer);
  if (drawerCloseBtn) drawerCloseBtn.addEventListener('click', closeDrawer);
  if (mobileDrawerBackdrop) mobileDrawerBackdrop.addEventListener('click', closeDrawer);

  // ==========================================
  // 4. Mobile Fullscreen Search Overlay
  // ==========================================
  const mobileSearchBtn = document.getElementById('mobile-search-btn');
  const mobileSearchOverlay = document.getElementById('mobile-search-overlay');
  const mobileSearchClose = document.getElementById('mobile-search-close');
  const mobileSearchInput = document.getElementById('mobile-search-input');

  function openMobileSearch() {
    if (mobileSearchOverlay) {
      mobileSearchOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      setTimeout(() => {
        if (mobileSearchInput) mobileSearchInput.focus();
      }, 100);
    }
  }

  function closeMobileSearch() {
    if (mobileSearchOverlay) {
      mobileSearchOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  if (mobileSearchBtn) mobileSearchBtn.addEventListener('click', openMobileSearch);
  if (mobileSearchClose) mobileSearchClose.addEventListener('click', closeMobileSearch);
  if (mobileSearchOverlay) {
    mobileSearchOverlay.addEventListener('click', (e) => {
      if (e.target === mobileSearchOverlay) closeMobileSearch();
    });
  }

  // ==========================================
  // 5. Desktop Search Shortcuts & Clear Button
  // ==========================================
  const headerSearchInput = document.getElementById('header-search-input');
  const searchClearBtn = document.getElementById('search-clear-btn');

  if (headerSearchInput && searchClearBtn) {
    const updateClearBtn = () => {
      searchClearBtn.style.display = headerSearchInput.value.trim().length > 0 ? 'flex' : 'none';
    };

    headerSearchInput.addEventListener('input', updateClearBtn);
    searchClearBtn.addEventListener('click', () => {
      headerSearchInput.value = '';
      updateClearBtn();
      headerSearchInput.focus();
    });
    updateClearBtn();
  }

  // Global Keyboard Shortcut: '/' to focus search
  document.addEventListener('keydown', (e) => {
    // Focus search on '/' when not in input/textarea
    if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
      e.preventDefault();
      if (window.innerWidth <= 868) {
        openMobileSearch();
      } else if (headerSearchInput) {
        headerSearchInput.focus();
        headerSearchInput.select();
      }
    }

    // Escape key closes modals and drawers
    if (e.key === 'Escape') {
      closeDrawer();
      closeMobileSearch();
      const userCard = document.getElementById('user-dropdown-card');
      if (userCard) userCard.classList.remove('show');
    }
  });

  // ==========================================
  // 6. User Dropdown Toggle on Mobile/Touch
  // ==========================================
  const userDropdownBtn = document.getElementById('user-dropdown-btn');
  const userDropdownCard = document.getElementById('user-dropdown-card');

  if (userDropdownBtn && userDropdownCard) {
    userDropdownBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdownCard.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
      if (!userDropdownCard.contains(e.target) && !userDropdownBtn.contains(e.target)) {
        userDropdownCard.classList.remove('show');
      }
    });
  }

  // ==========================================
  // 7. Hero Slider
  // ==========================================
  const slides = document.querySelectorAll('.hero-slide');
  const prevBtn = document.querySelector('.slider-btn.prev');
  const nextBtn = document.querySelector('.slider-btn.next');
  let currentSlide = 0;

  function showSlide(index) {
    if (!slides.length) return;
    slides.forEach((s, i) => {
      s.classList.toggle('active', i === index);
    });
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
  }

  if (nextBtn) nextBtn.addEventListener('click', nextSlide);
  if (prevBtn) prevBtn.addEventListener('click', prevSlide);

  if (slides.length > 1) {
    setInterval(nextSlide, 5000);
  }

  // ==========================================
  // 8. Flash Sale Countdown Timer
  // ==========================================
  const countdownEl = document.getElementById('flash-countdown');
  if (countdownEl) {
    let targetTime = new Date().getTime() + (24 * 3600 * 1000) + (14 * 60 * 1000);
    setInterval(() => {
      const now = new Date().getTime();
      const diff = targetTime - now;
      if (diff > 0) {
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        const hEl = document.getElementById('cd-hours');
        const mEl = document.getElementById('cd-mins');
        const sEl = document.getElementById('cd-secs');
        if (hEl) hEl.textContent = String(hours).padStart(2, '0');
        if (mEl) mEl.textContent = String(minutes).padStart(2, '0');
        if (sEl) sEl.textContent = String(seconds).padStart(2, '0');
      }
    }, 1000);
  }

  // ==========================================
  // 9. Tab Switcher
  // ==========================================
  const tabBtns = document.querySelectorAll('.tab-btn');
  const tabPanes = document.querySelectorAll('.tab-pane');
  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.tab;
      tabBtns.forEach(b => b.classList.remove('active'));
      tabPanes.forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      const pane = document.getElementById(targetId);
      if (pane) pane.classList.add('active');
    });
  });

  // ==========================================
  // 10. Modal Size Guide
  // ==========================================
  const openModalBtns = document.querySelectorAll('[data-open-modal]');
  const closeModalBtns = document.querySelectorAll('[data-close-modal]');
  
  openModalBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modalId = btn.dataset.openModal;
      const modal = document.getElementById(modalId);
      if (modal) modal.classList.add('active');
    });
  });

  closeModalBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const modal = btn.closest('.modal-overlay');
      if (modal) modal.classList.remove('active');
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) overlay.classList.remove('active');
    });
  });

  // ==========================================
  // 11. Quantity Stepper
  // ==========================================
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.parentElement.querySelector('.qty-input');
      if (!input) return;
      let val = parseInt(input.value) || 1;
      if (btn.classList.contains('plus')) {
        val++;
      } else if (btn.classList.contains('minus')) {
        if (val > 1) val--;
      }
      input.value = val;
    });
  });
});

// Toast notification helper
function showToast(message, type = 'info') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}
