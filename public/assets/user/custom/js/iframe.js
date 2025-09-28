
(function () {
  // --- Auto height (fast + reliable) ---
  var raf = window.requestAnimationFrame || function(fn){ return setTimeout(fn,16); };
  var lastH = 0, ticking = false;
  function docHeight(){
    return Math.max(
      document.body.scrollHeight, document.documentElement.scrollHeight,
      document.body.offsetHeight,  document.documentElement.offsetHeight,
      document.body.clientHeight,  document.documentElement.clientHeight
    );
  }
  function sendHeight(){
    var h = docHeight();
    if (h !== lastH){
      lastH = h;
      try { window.parent.postMessage({ frameHeight:h }, '*'); } catch(_){}
    }
    ticking = false;
  }
  function ping(){ if (!ticking){ ticking = true; (raf||setTimeout)(sendHeight,0); } }

  // parent requests height
  window.addEventListener('message', function(e){
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) ping();
  });

  // init + observers
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ping); else ping();
  window.addEventListener('load',  ping);
  window.addEventListener('resize', ping);
  if ('MutationObserver' in window){
    new MutationObserver(ping).observe(document.body, { childList:true, subtree:true, attributes:true });
  }
  if ('ResizeObserver' in window){
    var ro = new ResizeObserver(ping);
    ro.observe(document.body); ro.observe(document.documentElement);
  }
  setInterval(ping, 1500);

  // --- Step change -> parent top pe
  function notifyStep(){ try { window.parent.postMessage({ scrollTop:true }, '*'); } catch(_){ } }
  document.addEventListener('click', function(e){
    if (
      e.target.closest('#nextBtn') ||
      e.target.closest('#prevBtn') ||
      e.target.closest('#proceedBtn') ||
      e.target.closest('.buy-now-btn')
    ){
      notifyStep();
    }
  }, {passive:true});

  // --- Popup detect -> center only (no loader touch) ---
  var POPUPS = ['#confirm-popup-conteiner','.confirm-popup-conteiner','.modal.is-open','.ewm-splash'];
  function anyPopup(){
    for (var i=0;i<POPUPS.length;i++){
      var el = document.querySelector(POPUPS[i]);
      if (!el) continue;
      var disp = getComputedStyle(el).display;
      if (disp && disp !== 'none') return el;
    }
    return null;
  }
  var popupOpen = false;
  function watchPopup(){
    var el = anyPopup();
    if (el && !popupOpen){ popupOpen = true;  try { window.parent.postMessage({ centerMe:true   }, '*'); } catch(_){ } }
    else if (!el && popupOpen){ popupOpen = false; try { window.parent.postMessage({ popupClosed:true }, '*'); } catch(_){ } }
  }
  setInterval(watchPopup, 250);
  ['click','keyup','change'].forEach(function(ev){
    document.addEventListener(ev, function(){ setTimeout(watchPopup, 40); }, {passive:true});
  });

  // ⚠️ Loader events aapka existing flow handle karta hai — hum yahan kuch change nahi kar rahe:
  // window.parent.postMessage({ type:'show-loader' }, '*');  // (use only when you need)
  // window.parent.postMessage({ type:'hide-loader'  }, '*');

})();

