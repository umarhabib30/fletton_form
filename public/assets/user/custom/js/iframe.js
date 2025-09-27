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

  function sendHeight() {
    var h = docHeight();
    if (h !== lastH) {
      lastH = h;
      try { window.parent.postMessage({ frameHeight: h }, '*'); } catch (_) {}
    }
    ticking = false;
  }

  function ping() {
    if (!ticking) {
      ticking = true;
      (raf || setTimeout)(sendHeight, 0);
    }
  }

  // Parent se ping aaya
  window.addEventListener('message', function (e) {
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) ping();
  });

  // Initial
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ping);
  } else {
    ping();
  }
  window.addEventListener('load', ping);
  window.addEventListener('resize', ping);

  // DOM changes observe
  if ('MutationObserver' in window) {
    var mo = new MutationObserver(ping);
    mo.observe(document.body, { childList: true, subtree: true, attributes: true });
  }
  if ('ResizeObserver' in window) {
    var ro = new ResizeObserver(ping);
    ro.observe(document.body);
    ro.observe(document.documentElement);
  }

  // Periodic keep-alive
  setInterval(ping, 1500);

  // 🔹 Step change hone par parent ko upar scroll karna
  function notifyStepChange() {
    try { window.parent.postMessage({ scrollTop: true }, '*'); } catch (_) {}
  }
  document.addEventListener('click', function (e) {
    if (e.target.closest('#nextBtn') || e.target.closest('#proceedBtn')) {
      notifyStepChange();
    }
  });

  // 🔹 Popup Observer: agar popup visible hai → parent ko centerMe bhejna
  var popup = document.getElementById('confirm-popup-conteiner');
  if (popup && 'MutationObserver' in window) {
    var wasVisible = false;
    var popObs = new MutationObserver(function () {
      var nowVisible = window.getComputedStyle(popup).display !== 'none';
      if (nowVisible && !wasVisible) {
        // Popup just opened
        try { window.parent.postMessage({ centerMe: true }, '*'); } catch (_) {}
      }
      wasVisible = nowVisible;
    });
    popObs.observe(popup, { attributes: true, attributeFilter: ['style','class'] });
  }

})();
