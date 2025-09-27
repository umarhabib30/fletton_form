(function () {
  var iframe = document.getElementById('flettonsForm');
  var ALLOWED = ['https://quote.flettons.group'];
  var lockForPopup = false;

  function headerOffset() {
    var off = 0;
    var admin  = document.getElementById('wpadminbar');
    var header = document.querySelector('.site-header, header.site-header, .Kadence_Pro_Header');
    if (admin)  off += admin.offsetHeight || 0;
    if (header) off += header.offsetHeight || 0;
    // thoda breathing room
    return off + 8;
  }

  function setIframeHeight(h) {
    if (!lockForPopup && h > 0) iframe.style.height = h + 'px';
  }

  function requestChildHeight() {
    try { iframe.contentWindow.postMessage({ requestHeight: true }, '*'); } catch(_){}
  }

  // --- ROBUST CENTER FOR REAL MOBILE ---
  function centerIframeNow() {
    if (!iframe) return;
    var rect = iframe.getBoundingClientRect();
    var vvH  = (window.visualViewport && window.visualViewport.height) || window.innerHeight;
    var target = window.pageYOffset + rect.top + (rect.height / 2) - (vvH / 2) - headerOffset();
    target = Math.max(0, Math.round(target));
    // smooth + hard set fallback
    window.scrollTo({ top: target, behavior: 'smooth' });
    setTimeout(function(){ window.scrollTo(0, target); }, 180);
    setTimeout(function(){ window.scrollTo(0, target); }, 650);
  }

  // while the popup is on, keep iframe 100dvh tall so center looks perfect
  function lockPopupMode() {
    lockForPopup = true;
    var use100dvh = (window.CSS && CSS.supports('height','100dvh'));
    iframe.style.height = use100dvh ? '100dvh' : (window.innerHeight + 'px');
  }
  function unlockPopupMode() {
    lockForPopup = false;
    requestChildHeight();
  }

  // re-center for 1s if visualViewport changes (iOS url bar effects)
  function recenterForASecond() {
    if (!window.visualViewport) return;
    var until = Date.now() + 1000;
    function onVV() {
      centerIframeNow();
      if (Date.now() > until) {
        visualViewport.removeEventListener('resize', onVV);
        visualViewport.removeEventListener('scroll', onVV);
      }
    }
    visualViewport.addEventListener('resize', onVV, { passive:true });
    visualViewport.addEventListener('scroll', onVV,  { passive:true });
  }

  // --- MESSAGE HANDLER ---
  function onMessage(e) {
    if (ALLOWED.indexOf(e.origin) === -1) return;
    var d = e.data || {};
    if (typeof d !== 'object') return;

    if ('frameHeight' in d) {
      var h = parseInt(d.frameHeight, 10);
      if (!isNaN(h)) setIframeHeight(h);
      return;
    }

    if (d.scrollTop) {
      // step change → top of iframe
      iframe.scrollIntoView({ behavior:'smooth', block:'start' });
      setTimeout(function(){ iframe.scrollIntoView({ block:'start' }); }, 180);
      return;
    }

    if (d.centerMe) {
      // popup open → lock height & center, with mobile-proof retries
      lockPopupMode();
      centerIframeNow();
      recenterForASecond();
      return;
    }

    if (d.popupClosed) {
      // (optional) child bheje to unlock
      unlockPopupMode();
    }
  }

  window.addEventListener('message', onMessage, false);

  // initial height request
  iframe.addEventListener('load', requestChildHeight);
  window.addEventListener('resize', function () {
    if (lockForPopup) {
      iframe.style.height = (window.CSS && CSS.supports('height','100dvh')) ? '100dvh' : (window.innerHeight + 'px');
    } else {
      requestChildHeight();
    }
  });
})();
