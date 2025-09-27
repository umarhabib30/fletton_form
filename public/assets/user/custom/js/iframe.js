(function () {
  function getVisibleHeight() {
    // Sirf visible elements count karo
    function isVisible(el) {
      return el && el.offsetParent !== null;
    }

    let maxH = 0;
    document.querySelectorAll('body *').forEach(el => {
      if (isVisible(el)) {
        const rect = el.getBoundingClientRect();
        maxH = Math.max(maxH, rect.bottom + window.scrollY);
      }
    });

    return Math.ceil(maxH);
  }

  function sendHeight() {
    var h = getVisibleHeight();
    try { window.parent.postMessage({ frameHeight: h }, '*'); } catch (_) {}
  }

  // parent se request aayi
  window.addEventListener('message', e => {
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) sendHeight();
  });

  // triggers
  ['DOMContentLoaded', 'load', 'resize'].forEach(evt =>
    window.addEventListener(evt, () => requestAnimationFrame(sendHeight))
  );

  if ('MutationObserver' in window) {
    new MutationObserver(() => requestAnimationFrame(sendHeight))
      .observe(document.body, { childList: true, subtree: true, attributes: true });
  }

  if ('ResizeObserver' in window) {
    new ResizeObserver(() => requestAnimationFrame(sendHeight))
      .observe(document.body);
  }

  setInterval(sendHeight, 1500); // fallback
})();
