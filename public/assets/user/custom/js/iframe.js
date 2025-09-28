
(function () {
  let lastH = 0;

  function getVisibleHeight() {
    // Sirf visible elements ki height
    const steps = Array.from(document.querySelectorAll('.step, .confirm-popup-conteiner, body > *'));
    let maxH = 0;

    steps.forEach(el => {
      if (el && el.offsetParent !== null) { // visible check
        const rect = el.getBoundingClientRect();
        maxH = Math.max(maxH, rect.bottom + window.scrollY);
      }
    });

    return Math.ceil(maxH || document.body.scrollHeight);
  }

  function sendHeight(force = false) {
    const h = getVisibleHeight();
    if (force || h !== lastH) {
      lastH = h;
      try {
        window.parent.postMessage({ frameHeight: h }, '*');
      } catch (_) {}
    }
  }

  // Listen parent ping
  window.addEventListener('message', e => {
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) {
      sendHeight(true);
    }
  });

  // Lifecycle triggers
  document.addEventListener('DOMContentLoaded', () => sendHeight(true));
  window.addEventListener('load', () => sendHeight(true));
  window.addEventListener('resize', () => requestAnimationFrame(() => sendHeight(true)));

  // Mutation observer (steps / popup change)
  if ('MutationObserver' in window) {
    new MutationObserver(() => requestAnimationFrame(() => sendHeight(true)))
      .observe(document.body, { childList: true, subtree: true, attributes: true });
  }

  // Resize observer (layout resize)
  if ('ResizeObserver' in window) {
    const ro = new ResizeObserver(() => requestAnimationFrame(() => sendHeight(true)));
    ro.observe(document.body);
    ro.observe(document.documentElement);
  }

  // Keep alive ping
  setInterval(() => sendHeight(true), 1200);

  // Expose manual trigger
  window.FlettonsAutoHeight = { ping: () => sendHeight(true) };
})();
