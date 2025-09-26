
(function () {
  function docHeight() {
    return Math.max(
      document.body.scrollHeight, document.documentElement.scrollHeight,
      document.body.offsetHeight, document.documentElement.offsetHeight,
      document.body.clientHeight, document.documentElement.clientHeight
    );
  }

  function sendHeight() {
    var h = docHeight();
    try { window.parent.postMessage({ frameHeight: h }, '*'); } catch (_) { }
  }

  // parent requests
  window.addEventListener('message', function (e) {
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) sendHeight();
  });

  // always send on events
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', sendHeight);
  } else {
    sendHeight();
  }
  window.addEventListener('load', sendHeight);
  window.addEventListener('resize', sendHeight);

  // Mutation + Resize observers (live updates)
  if ('MutationObserver' in window) {
    new MutationObserver(sendHeight).observe(document.body, { childList: true, subtree: true, attributes: true });
  }
  if ('ResizeObserver' in window) {
    var ro = new ResizeObserver(sendHeight);
    ro.observe(document.body);
    ro.observe(document.documentElement);
  }

  // fallback timer
  setInterval(sendHeight, 1500);

  // step change → scroll parent top
  document.addEventListener('click', function (e) {
    if (e.target.closest('#nextBtn') || e.target.closest('#proceedBtn')) {
      try { window.parent.postMessage({ scrollTop: true }, '*'); } catch (_) { }
    }
  });

  // popup detect
  var popupOpen = false;
  setInterval(function () {
    var el = document.querySelector('#confirm-popup-conteiner');
    var visible = el && getComputedStyle(el).display !== 'none';
    if (visible && !popupOpen) {
      popupOpen = true;
      window.parent.postMessage({ centerMe: true }, '*');
    } else if (!visible && popupOpen) {
      popupOpen = false;
      window.parent.postMessage({ popupClosed: true }, '*');
    }
  }, 300);

})();

