(function () {
  /* ------------ HEIGHT PINGS (as you had) ------------ */
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
  window.addEventListener('message', function (e) {
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) ping();
  });
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ping);
  } else { ping(); }
  window.addEventListener('load', ping);
  window.addEventListener('resize', ping);
  if ('MutationObserver' in window) {
    new MutationObserver(ping).observe(document.body, { childList: true, subtree: true, attributes: true });
  }
  if ('ResizeObserver' in window) {
    var ro = new ResizeObserver(ping);
    ro.observe(document.body);
    ro.observe(document.documentElement);
  }
  setInterval(ping, 1500);

  /* ------------ STEP/NEXT -> parent ko top pe lao ------------ */
  function notifyStep() {
    try { window.parent.postMessage({ type: 'SCROLL_TOP' }, '*'); } catch (_) {}
  }
  document.addEventListener('click', function (e) {
    if (e.target.closest('#nextBtn') || e.target.closest('#proceedFromSummaryBtn') || e.target.closest('#proceedBtn')) {
      notifyStep();
    }
  }, { passive: true });

  /* ============================================================
     POPUP LOCK + CENTER
     - detect #confirm-popup-conteiner open/close
     - child ko lock (no scroll) + 100dvh
     - parent ko POPUP_OPEN / POPUP_CLOSE bhejna
     ============================================================ */
  var POPUP_SEL = '#confirm-popup-conteiner';   // tumhara popup wrapper
  var popup = null;
  function havePopup() {
    if (!popup) popup = document.querySelector(POPUP_SEL);
    return !!popup;
  }
  // child side body/html lock class
  var LOCK_CLASS = 'ewm-popup-lock';
  // add CSS just once
  (function injectPopupCSS(){
    var id = 'ewm-popup-lock-css';
    if (document.getElementById(id)) return;
    var st = document.createElement('style'); st.id = id;
    st.textContent = `
      html.${LOCK_CLASS}, body.${LOCK_CLASS}{ overflow:hidden !important; }
      @supports (height: 100dvh){
        html.${LOCK_CLASS}, body.${LOCK_CLASS}{ height:100dvh !important; max-height:100dvh !important; }
      }
      /* safety: make sure popup is truly viewport-fixed */
      ${POPUP_SEL}{
        position: fixed !important;
        inset: 0 !important;
        z-index: 2147483000 !important;
        display: none; /* tumhari JS kholti band karti hogi; yahan force nahi */
      }
    `;
    document.head.appendChild(st);
  })();

  function isPopupVisible() {
    if (!havePopup()) return false;
    var cs = getComputedStyle(popup);
    return cs.display !== 'none' && cs.visibility !== 'hidden' && popup.offsetParent !== null || cs.position === 'fixed';
  }

  function setChildLock(on){
    [document.documentElement, document.body].forEach(function(n){
      if (!n) return;
      if (on) n.classList.add(LOCK_CLASS);
      else n.classList.remove(LOCK_CLASS);
    });
  }

  var lastOpen = false;
  function syncPopupState(force){
    var open = isPopupVisible();
    if (force || open !== lastOpen){
      lastOpen = open;
      setChildLock(open);
      try {
        window.parent.postMessage({ type: open ? 'POPUP_OPEN' : 'POPUP_CLOSE' }, '*');
      } catch(_){}
      if (open){
        // ensure immediately visible
        try { window.parent.postMessage({ type: 'SCROLL_IFRAME_CENTER' }, '*'); } catch(_){}
      }
    }
  }

  // hooks: buttons that open/close
  document.addEventListener('click', function(e){
    // open buttons (tumhare buttons)
    if (e.target.closest('.buy-now-btn, .confirm-yes, .proceed')) {
      setTimeout(function(){ syncPopupState(true); }, 0);
    }
    // close buttons (X/back/dismiss)
    if (e.target.closest('.confirm-popup-close, .confirm-popup-back')) {
      setTimeout(function(){ syncPopupState(true); }, 0);
    }
  }, { passive:true });

  // observe popup node for class/style changes
  var watchStarted = false;
  function startWatching(){
    if (watchStarted || !havePopup()) return;
    watchStarted = true;
    new MutationObserver(function(){ syncPopupState(false); })
      .observe(popup, { attributes:true, attributeFilter:['style','class'], subtree:true, childList:true });
  }
  if (document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', function(){ startWatching(); syncPopupState(true); });
  } else { startWatching(); syncPopupState(true); }

})();
