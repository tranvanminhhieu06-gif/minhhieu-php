/**
 * HIEU CEO - Core Master Interactive Application Script
 * Features: Toast Suite, 3D Tilt Engine, Animated Counters, Modals, Theme Switching AJAX
 */

document.addEventListener('DOMContentLoaded', () => {
  initToasts();
  init3DTilt();
  initCounters();
  initModals();
  initThemeToggles();
  initQuickActions();
});

// 1. Toast Notification Suite
function initToasts() {
  if (!document.querySelector('.toast-container')) {
    const container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
}

function showToast(message, type = 'info', duration = 3500) {
  const container = document.querySelector('.toast-container');
  if (!container) return;

  const icons = {
    success: 'fa-circle-check',
    error: 'fa-circle-exclamation',
    warning: 'fa-triangle-exclamation',
    info: 'fa-circle-info'
  };

  const toast = document.createElement('div');
  toast.className = `toast-card ${type}`;
  toast.innerHTML = `
    <i class="fa-solid ${icons[type] || icons.info}" style="font-size: 1.2rem; color: ${type === 'success' ? '#10b981' : type === 'error' ? '#f43f5e' : type === 'warning' ? '#f59e0b' : '#89f5ff'};"></i>
    <div style="flex: 1; font-size: 0.9rem; font-weight: 500;">${message}</div>
    <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #737373; cursor: pointer; padding: 4px;">
      <i class="fa-solid fa-xmark"></i>
    </button>
  `;

  container.appendChild(toast);
  setTimeout(() => toast.classList.add('show'), 50);

  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 400);
  }, duration);
}

// 2. 3D Tilt Engine on Hover
function init3DTilt() {
  const cards = document.querySelectorAll('.ceo-card-tilt');
  cards.forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      const rotateX = ((y - centerY) / centerY) * -10;
      const rotateY = ((x - centerX) / centerX) * 10;

      card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px) scale(1.02)`;
    });

    card.addEventListener('mouseleave', () => {
      card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0) scale(1)';
    });
  });
}

// 3. Animated Number Counters
function initCounters() {
  const counters = document.querySelectorAll('.counter-val');
  const speed = 200;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const target = +entry.target.getAttribute('data-target');
        const count = +entry.target.innerText.replace(/[^0-9.]/g, '') || 0;
        const inc = target / speed;

        const updateCount = () => {
          const current = +entry.target.getAttribute('data-current') || 0;
          if (current < target) {
            const next = Math.ceil(current + inc);
            entry.target.setAttribute('data-current', next);
            entry.target.innerText = next.toLocaleString();
            setTimeout(updateCount, 15);
          } else {
            entry.target.innerText = target.toLocaleString();
          }
        };
        updateCount();
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(c => observer.observe(c));
}

// 4. Modal Suite
function initModals() {
  document.querySelectorAll('[data-modal-target]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = btn.getAttribute('data-modal-target');
      openModal(targetId);
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        overlay.classList.remove('active');
      }
    });
  });
}

function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.add('active');
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.remove('active');
}

// 5. AJAX Theme Activation Trigger
function initThemeToggles() {
  document.querySelectorAll('.btn-activate-theme').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const themeId = btn.getAttribute('data-theme-id');
      const themeName = btn.getAttribute('data-theme-name') || 'Giao diện';

      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang kích hoạt...';

      try {
        const response = await fetch('api/themes.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'activate', theme_id: themeId })
        });
        const result = await response.json();

        if (result.success) {
          showToast(`Đã kích hoạt thành công: ${themeName}!`, 'success');
          setTimeout(() => window.location.reload(), 1000);
        } else {
          showToast(result.message || 'Lỗi khi kích hoạt giao diện.', 'error');
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-power-off mr-2"></i> Kích Hoạt';
        }
      } catch (err) {
        showToast('Không thể kết nối đến máy chủ API.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-power-off mr-2"></i> Kích Hoạt';
      }
    });
  });
}

// 6. Quick Actions (Clear Cache, Dark/Light Mode)
function initQuickActions() {
  const cacheBtn = document.getElementById('btn-clear-cache');
  if (cacheBtn) {
    cacheBtn.addEventListener('click', async () => {
      cacheBtn.classList.add('animate-rotate');
      try {
        const res = await fetch('api/system.php?action=clear_cache');
        const data = await res.json();
        if (data.success) {
          showToast('Đã dọn dẹp bộ nhớ đệm thành công!', 'success');
        } else {
          showToast(data.message, 'error');
        }
      } catch (e) {
        showToast('Lỗi khi gọi API xóa Cache.', 'error');
      } finally {
        setTimeout(() => cacheBtn.classList.remove('animate-rotate'), 600);
      }
    });
  }

  // Dark / Light Mode Switcher
  const themeModeBtn = document.getElementById('btn-theme-mode');
  if (themeModeBtn) {
    themeModeBtn.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-theme') || 'dark';
      const next = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('hieu_ceo_mode', next);
      showToast(`Đã chuyển sang chế độ ${next === 'dark' ? 'Dark Mode' : 'Light Mode'}`, 'info');
    });

    // Check saved mode
    const saved = localStorage.getItem('hieu_ceo_mode');
    if (saved) {
      document.documentElement.setAttribute('data-theme', saved);
    }
  }
}
