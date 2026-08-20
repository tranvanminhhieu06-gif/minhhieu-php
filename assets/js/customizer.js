/**
 * HIEU CEO - Live Visual Theme Customizer Engine
 * Real-time CSS Variable manipulation and instant iframe preview sync
 */

document.addEventListener('DOMContentLoaded', () => {
  const iframe = document.getElementById('customizer-preview-frame');
  const themeId = document.getElementById('customizer-theme-id')?.value;

  // 1. Color Pickers
  const primaryColorInput = document.getElementById('cust-primary-color');
  const secondaryColorInput = document.getElementById('cust-secondary-color');
  const accentColorInput = document.getElementById('cust-accent-color');
  const bgColorInput = document.getElementById('cust-bg-color');
  const radiusInput = document.getElementById('cust-border-radius');
  const fontSelect = document.getElementById('cust-font-family');

  function updateLiveStyles() {
    if (!iframe || !iframe.contentDocument) return;
    const doc = iframe.contentDocument;

    const primary = primaryColorInput?.value || '#6366f1';
    const secondary = secondaryColorInput?.value || '#ec4899';
    const accent = accentColorInput?.value || '#06b6d4';
    const bg = bgColorInput?.value || '#0f172a';
    const radius = (radiusInput?.value || 14) + 'px';
    const font = fontSelect?.value || 'Outfit';

    // Inject/Update style tag inside iframe
    let styleTag = doc.getElementById('hieu-live-custom-css');
    if (!styleTag) {
      styleTag = doc.createElement('style');
      styleTag.id = 'hieu-live-custom-css';
      doc.head.appendChild(styleTag);
    }

    styleTag.innerHTML = `
      :root {
        --ceo-primary: ${primary} !important;
        --ceo-secondary: ${secondary} !important;
        --ceo-accent: ${accent} !important;
        --bg-deep: ${bg} !important;
        --radius-md: ${radius} !important;
        --font-main: '${font}', sans-serif !important;
      }
      .btn-primary, .btn-ceo-primary {
        background: ${primary} !important;
        border-color: ${primary} !important;
      }
      .text-primary { color: ${primary} !important; }
    `;
  }

  // Bind Events
  [primaryColorInput, secondaryColorInput, accentColorInput, bgColorInput, radiusInput, fontSelect].forEach(elem => {
    if (elem) {
      elem.addEventListener('input', updateLiveStyles);
      elem.addEventListener('change', updateLiveStyles);
    }
  });

  if (iframe) {
    iframe.addEventListener('load', () => {
      setTimeout(updateLiveStyles, 300);
    });
  }

  // 2. Section Toggle Checkboxes
  document.querySelectorAll('.section-toggle-cb').forEach(cb => {
    cb.addEventListener('change', (e) => {
      const sectionKey = cb.getAttribute('data-section-key');
      const isChecked = cb.checked;
      if (iframe && iframe.contentDocument) {
        const secElem = iframe.contentDocument.querySelector(`[data-section="${sectionKey}"], #${sectionKey}, .section-${sectionKey}`);
        if (secElem) {
          secElem.style.display = isChecked ? 'block' : 'none';
        }
      }
    });
  });

  // 3. Save Customizer Form via AJAX
  const saveBtn = document.getElementById('btn-save-customizer');
  if (saveBtn) {
    saveBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      saveBtn.disabled = true;
      saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang Lưu Cấu Hình...';

      const payload = {
        theme_id: themeId,
        primary_color: primaryColorInput?.value,
        secondary_color: secondaryColorInput?.value,
        accent_color: accentColorInput?.value,
        bg_color: bgColorInput?.value,
        border_radius: radiusInput?.value,
        font_family: fontSelect?.value,
        custom_css: document.getElementById('cust-custom-css')?.value || '',
        sections: {}
      };

      document.querySelectorAll('.section-toggle-cb').forEach(cb => {
        payload.sections[cb.getAttribute('data-section-key')] = cb.checked ? 1 : 0;
      });

      try {
        const res = await fetch('api/customize.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (result.success) {
          if (typeof showToast === 'function') {
            showToast('Đã lưu toàn bộ cấu hình tùy biến thành công!', 'success');
          } else {
            alert('Đã lưu thành công!');
          }
        } else {
          showToast(result.message || 'Lỗi khi lưu.', 'error');
        }
      } catch (err) {
        showToast('Lỗi máy chủ khi lưu tùy biến.', 'error');
      } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-2"></i> Lưu & Áp Dụng';
      }
    });
  }
});
