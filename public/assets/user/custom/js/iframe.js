(function () {
  function docHeight() {
    return Math.max(
      document.body.scrollHeight, document.documentElement.scrollHeight,
      document.body.offsetHeight,  document.documentElement.offsetHeight,
      document.body.clientHeight,  document.documentElement.clientHeight
    );
  }
  function sendHeight() {
    try { window.parent.postMessage({ frameHeight: docHeight() }, '*'); } catch(_){}
  }

  // parent se request
  window.addEventListener('message', function(e){
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) sendHeight();
  });

  // initial + resize
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', sendHeight); else sendHeight();
  window.addEventListener('load', sendHeight);
  window.addEventListener('resize', sendHeight);

  // observers
  if ('MutationObserver' in window) new MutationObserver(sendHeight).observe(document.body, {childList:true,subtree:true,attributes:true});
  if ('ResizeObserver' in window) { var ro=new ResizeObserver(sendHeight); ro.observe(document.body); ro.observe(document.documentElement); }

  // keep-alive
  setInterval(sendHeight, 1200);

  // step nav → parent scroll top + height refresh
  document.addEventListener('click', function(e){
    if (e.target.closest('#nextBtn') || e.target.closest('#prevBtn') || e.target.closest('#proceedBtn')) {
      window.parent.postMessage({ scrollTop:true }, '*');
      setTimeout(sendHeight, 30);
      setTimeout(sendHeight, 250);
    }
  }, {passive:true});

  // ✅ Step 4 (Solicitor) toggles par explicit height updates
  var ids = [
    '#solicitorYes','#solicitorNo',
    '#exchangeKnownYes','#exchangeKnownNo',
    '#solicitorFields','#exchangeDateField'
  ];
  ids.forEach(function(sel){
    var el = document.querySelector(sel);
    if (el) ['change','input','click'].forEach(function(ev){
      el.addEventListener(ev, function(){ setTimeout(sendHeight, 20); setTimeout(sendHeight, 200); }, {passive:true});
    });
  });

  // popup detect (optional)
  var lastPop = false;
  setInterval(function(){
    var el = document.querySelector('#confirm-popup-conteiner');
    var vis = el && getComputedStyle(el).display !== 'none';
    if (vis && !lastPop) { lastPop = true;  window.parent.postMessage({ centerMe:true }, '*'); }
    if (!vis && lastPop) { lastPop = false; window.parent.postMessage({ popupClosed:true }, '*'); }
  }, 250);
})();