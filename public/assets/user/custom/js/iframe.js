
(function () {
  var raf = window.requestAnimationFrame || function (fn) { return setTimeout(fn, 16); };
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
  function ping(){ if (!ticking){ ticking = true; (raf||setTimeout)(sendHeight, 0); } }

  // parent request
  window.addEventListener('message', function(e){
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) ping();
  });

  // init + observers
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ping); else ping();
  window.addEventListener('load', ping);
  window.addEventListener('resize', ping);
  if ('MutationObserver' in window){
    new MutationObserver(ping).observe(document.body, { childList:true, subtree:true, attributes:true });
  }
  if ('ResizeObserver' in window){
    var ro = new ResizeObserver(ping);
    ro.observe(document.body); ro.observe(document.documentElement);
  }
  setInterval(ping, 1500);

  // step buttons → parent scroll to top
  document.addEventListener('click', function (e) {
    if (
      e.target.closest('#nextBtn') ||
      e.target.closest('#proceedBtn') ||
      e.target.closest('.buy-now-btn')  // list step cards
    ){
      try { window.parent.postMessage({ scrollTop:true }, '*'); } catch(_){}
    }
  }, {passive:true});

  // POPUP detect (any overlay) — issue center/close signals
  var POPUPS = [
    '#confirm-popup-conteiner', // your confirm popup
    '.confirm-popup-conteiner', // safety
    '.modal.is-open',           // generic
    '.ewm-splash'               // payment splash (if any)
  ];
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
  function checkPopup(){
    var el = anyPopup();
    if (el && !popupOpen){
      popupOpen = true;
      try { window.parent.postMessage({ centerMe:true }, '*'); } catch(_){}
    } else if (!el && popupOpen){
      popupOpen = false;
      try { window.parent.postMessage({ popupClosed:true }, '*'); } catch(_){}
    }
  }
  setInterval(checkPopup, 200);
  document.addEventListener('click',  function(){ setTimeout(checkPopup, 40); }, {passive:true});
  document.addEventListener('keyup',   function(){ setTimeout(checkPopup, 40); }, {passive:true});
  document.addEventListener('change',  function(){ setTimeout(checkPopup, 40); }, {passive:true});

  // OPTIONAL: when you actually have the final payment URL ready, call:
  // window.parent.postMessage({ type:'go-to-payment', url:'https://flettons.group/flettons-order/?ref=...' }, '*');
})();

