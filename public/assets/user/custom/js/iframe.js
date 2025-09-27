/* Flettons v2 — Child → Parent auto-height with modal/steps support */
(function () {
  var raf = window.requestAnimationFrame || function (fn) { return setTimeout(fn, 16); };
  var lastH = 0, ticking = false, modalMode = false;

  function viewportH() {
    // Prefer visualViewport for mobile address bar changes
    var vv = window.visualViewport;
    return Math.ceil(vv ? vv.height : window.innerHeight || document.documentElement.clientHeight || 0);
  }

  function docH() {
    return Math.max(
      document.body.scrollHeight,
      document.documentElement.scrollHeight,
      document.body.offsetHeight,
      document.documentElement.offsetHeight,
      document.body.clientHeight,
      document.documentElement.clientHeight
    );
  }

  function computeHeight() {
    // when modal open -> use at least viewport height so popup never clips
    var base = docH();
    var vph = viewportH();
    return modalMode ? Math.max(base, vph) : base;
  }

  function send() {
    var h = computeHeight();
    if (h !== lastH && h > 0) {
      lastH = h;
      try { window.parent.postMessage({ frameHeight: h }, '*'); } catch (_) {}
    }
    ticking = false;
  }

  function ping() {
    if (!ticking) { ticking = true; (raf || setTimeout)(send, 0); }
  }

  // detect modal open/close via body class changes
  function updateModalMode() {
    var now = document.body.classList.contains('modal-open');
    if (now !== modalMode) {
      modalMode = now;
      // burst pings when modal toggles (for smooth resize)
      ping(); setTimeout(ping, 50); setTimeout(ping, 200); setTimeout(ping, 600);
    }
  }

  // parent → requestHeight
  window.addEventListener('message', function (e) {
    var d = e.data;
    if (d && typeof d === 'object' && d.requestHeight) ping();
  });

  // init / paints
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ping); else ping();
  window.addEventListener('load', function(){ ping(); setTimeout(ping, 250); });

  // viewport changes
  window.addEventListener('resize', function(){ (raf||setTimeout)(ping,0); });
  window.addEventListener('orientationchange', function(){ setTimeout(ping, 120); });

  // DOM/layout mutations (steps, accordions, toasts, validation)
  if ('MutationObserver' in window) {
    var mo = new MutationObserver(function(muts){
      // also watch for class changes to catch modal-open
      updateModalMode();
      (raf||setTimeout)(ping,0);
    });
    mo.observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['class','style'] });
  }

  // visualViewport changes (mobile URL bar show/hide)
  if (window.visualViewport) {
    visualViewport.addEventListener('resize', ping);
    visualViewport.addEventListener('scroll', ping);
  }

  // gentle keep-alive
  setInterval(ping, 1200);

  // expose helpers for your code (optional)
  window.FlettonsAutoHeight = {
    ping: ping,
    modalOn: function(){ document.body.classList.add('modal-open'); updateModalMode(); },
    modalOff:function(){ document.body.classList.remove('modal-open'); updateModalMode(); }
  };
})();
