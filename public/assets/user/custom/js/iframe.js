/* Flettons - Child Iframe Auto Height (drop this in EVERY form/app) */
(function () {
  var raf = window.requestAnimationFrame || function (fn) { return setTimeout(fn, 16); };
  var lastH = 0, ticking = false;

  function docHeight() {
    return Math.max(
      document.body.scrollHeight,
      document.documentElement.scrollHeight,
      document.body.offsetHeight,
      document.documentElement.offsetHeight,
      document.body.clientHeight,
      document.documentElement.clientHeight
    );
  }

  function send() {
    var h = docHeight();
    if (h !== lastH) {
      lastH = h;
      try { window.parent.postMessage({ frameHeight: h }, '*'); } catch (_) {}
    }
    ticking = false;
  }

  function ping() { if (!ticking) { ticking = true; (raf || setTimeout)(send, 0); } }

  // Parent ping -> reply height
  window.addEventListener('message', function (e) {
    var d = e.data;
    if (d && typeof d === 'object' && d.requestHeight) ping();
  });

  // Initial/paint events
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ping); else ping();
  window.addEventListener('load', ping);
  window.addEventListener('resize', ping);

  // DOM/layout changes (steps, accordions, toasts…)
  if ('MutationObserver' in window) {
    var mo = new MutationObserver(ping);
    mo.observe(document.body, { childList: true, subtree: true, attributes: true, characterData: true });
  }
  if ('ResizeObserver' in window) {
    var ro = new ResizeObserver(ping);
    ro.observe(document.body);
    ro.observe(document.documentElement);
  }

  // Keep-alive (very light)
  setInterval(ping, 1200);

  // Manual access if needed inside your code:
  window.FlettonsAutoHeight = { ping: ping };
})();
