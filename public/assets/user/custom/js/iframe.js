/* Flettons: Child → Parent auto-height (drop in EVERY form/app) */
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
    if (h !== lastH && h > 0) {
      lastH = h;
      try { window.parent.postMessage({ frameHeight: h }, '*'); } catch (_) {}
    }
    ticking = false;
  }

  function ping() { if (!ticking) { ticking = true; (raf || setTimeout)(send, 0); } }

  // Parent → requestHeight
  window.addEventListener('message', function (e) {
    var d = e.data;
    if (d && typeof d === 'object' && d.requestHeight) ping();
  });

  // first paints
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ping); else ping();
  window.addEventListener('load', ping);
  window.addEventListener('resize', function(){ (raf||setTimeout)(ping,0); });

  // DOM/layout changes (wizard steps, accordions, toasts)
  if ('MutationObserver' in window) {
    var mo = new MutationObserver(function(){ (raf||setTimeout)(ping,0); });
    mo.observe(document.body, { childList: true, subtree: true, attributes: true, characterData: true });
  }
  if ('ResizeObserver' in window) {
    var ro = new ResizeObserver(function(){ (raf||setTimeout)(ping,0); });
    ro.observe(document.body);
    ro.observe(document.documentElement);
  }

  // light keep-alive
  setInterval(ping, 1200);

  // expose manual call if needed
  window.FlettonsAutoHeight = { ping: ping };
})();
