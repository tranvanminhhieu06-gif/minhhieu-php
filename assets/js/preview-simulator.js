/**
 * HIEU CEO - Device Viewport Simulator Controller
 * Switch between Desktop (iMac), Tablet (iPad Pro), Mobile (iPhone 15 Pro)
 */

document.addEventListener('DOMContentLoaded', () => {
  const frame = document.getElementById('simulator-device-frame');
  const iframe = document.getElementById('simulator-iframe');
  const deviceButtons = document.querySelectorAll('.device-btn');
  const rotateBtn = document.getElementById('btn-rotate-device');
  const zoomSelect = document.getElementById('select-preview-zoom');
  const reloadBtn = document.getElementById('btn-reload-preview');

  if (!frame) return;

  // 1. Switch Devices
  deviceButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      deviceButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const mode = btn.getAttribute('data-device'); // 'desktop', 'tablet', 'mobile'
      frame.className = `device-frame ${mode} animate-fade-scale`;
    });
  });

  // 2. Rotate Orientation
  let isLandscape = false;
  if (rotateBtn) {
    rotateBtn.addEventListener('click', () => {
      isLandscape = !isLandscape;
      if (isLandscape) {
        frame.style.transform = 'rotate(90deg) scale(0.85)';
      } else {
        frame.style.transform = 'none';
      }
    });
  }

  // 3. Zoom Scaling
  if (zoomSelect) {
    zoomSelect.addEventListener('change', (e) => {
      const zoom = e.target.value;
      frame.style.transform = `scale(${zoom})`;
      frame.style.transformOrigin = 'center center';
    });
  }

  // 4. Reload Iframe
  if (reloadBtn && iframe) {
    reloadBtn.addEventListener('click', () => {
      reloadBtn.classList.add('animate-rotate');
      iframe.src = iframe.src;
      setTimeout(() => reloadBtn.classList.remove('animate-rotate'), 800);
    });
  }
});
