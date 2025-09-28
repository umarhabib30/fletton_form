
(function () {
  // ---------- Height ----------
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

  // init + resize + observers
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

  // keep alive
  setInterval(ping, 1200);

  // ---------- Step change -> parent scrollTop + fresh height ----------
  document.addEventListener('click', function (e) {
    if (
      e.target.closest('#nextBtn') ||
      e.target.closest('#prevBtn') ||
      e.target.closest('#proceedBtn') ||
      e.target.closest('.buy-now-btn') // card/select buttons
    ){
      try { window.parent.postMessage({ scrollTop:true }, '*'); } catch(_){}
      setTimeout(ping, 30);
      setTimeout(ping, 250);
    }
  }, {passive:true});

  // ---------- Solicitor/Agents toggles: force re-measure ----------
  var remeasureIds = [
    '#solicitorYes', '#solicitorNo',
    '#exchangeKnownYes', '#exchangeKnownNo',
    '#solicitorFields','#exchangeDateField',
    '#gardenYes','#gardenNo','#garageYes','#garageNo'
  ];
  remeasureIds.forEach(function(sel){
    var el = document.querySelector(sel);
    if (el) ['change','input','click'].forEach(function(evt){
      el.addEventListener(evt, function(){ setTimeout(ping, 20); setTimeout(ping, 200); }, {passive:true});
    });
  });

  // ---------- Popup detection: center while open ----------
  var POPUPS = [
    '#confirm-popup-conteiner',  // your confirm popup
    '.confirm-popup-conteiner',  // safety alias
    '.ewm-splash',               // payment splash, if used
    '.modal.is-open'             // any generic modal
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
  var popupWas = false;
  function checkPopup(){
    var el = anyPopup();
    var open = !!el;
    if (open && !popupWas) {
      popupWas = true;
      try { window.parent.postMessage({ centerMe:true }, '*'); } catch(_){}
      setTimeout(ping, 30);
    } else if (!open && popupWas) {
      popupWas = false;
      try { window.parent.postMessage({ popupClosed:true }, '*'); } catch(_){}
      setTimeout(ping, 30);
    }
  }
  setInterval(checkPopup, 220);
  ['click','keyup','change'].forEach(function(ev){
    document.addEventListener(ev, function(){ setTimeout(checkPopup, 40); }, {passive:true});
  });

  // ---------- Optional: when you have a final payment URL ready ----------
  // window.parent.postMessage({ type:'go-to-payment', url:'https://flettons.group/flettons-order/?...' }, '*');
})();

