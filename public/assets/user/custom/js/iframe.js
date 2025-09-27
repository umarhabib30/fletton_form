(function () {
  function sendHeight() {
    var h = Math.max(
      document.body.scrollHeight,
      document.documentElement.scrollHeight,
      document.body.offsetHeight,
      document.documentElement.offsetHeight,
      document.body.clientHeight,
      document.documentElement.clientHeight
    );
    try { window.parent.postMessage({ frameHeight: h }, '*'); } catch (_) {}
  }

  // parent se "requestHeight" aya to reply karo
  window.addEventListener('message', function (e) {
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) {
      sendHeight();
    }
  });

  // normal triggers
  document.addEventListener('DOMContentLoaded', sendHeight);
  window.addEventListener('load', sendHeight);
  window.addEventListener('resize', () => requestAnimationFrame(sendHeight));

  // har step change / popup waghera detect
  if ('MutationObserver' in window) {
    new MutationObserver(() => requestAnimationFrame(sendHeight))
      .observe(document.body, { childList: true, subtree: true, attributes: true });
  }
  if ('ResizeObserver' in window) {
    new ResizeObserver(() => requestAnimationFrame(sendHeight))
      .observe(document.body);
  }

  // fallback ping
  setInterval(sendHeight, 1500);
})();
