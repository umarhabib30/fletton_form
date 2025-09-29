
(function () {
  // --- measure only main form wrapper (ignore overlay elements)
  function WRAP(){
    return document.querySelector('.form-container')   ||
           document.querySelector('#quote-container')  ||
           document.querySelector('.container')        ||
           document.body;
  }
  function measure(){
    var el = WRAP();
    var r  = el.getBoundingClientRect();
    var cs = getComputedStyle(el);
    var h  = Math.ceil(r.height + (parseFloat(cs.marginTop)||0) + (parseFloat(cs.marginBottom)||0));
    if (!isFinite(h) || h < 1){
      h = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight, 320);
    }
    return h;
  }

  var raf   = window.requestAnimationFrame || function(fn){ return setTimeout(fn,16); };
  var lastH = 0, ticking=false;
  function send(){
    var h = measure();
    if (h !== lastH){
      lastH = h;
      try { window.parent.postMessage({ frameHeight:h }, '*'); } catch(_){}
    }
    ticking=false;
  }
  function ping(){ if (!ticking){ ticking=true; (raf||setTimeout)(send,0); } }

  // parent height request
  window.addEventListener('message', function(e){
    if (e.data && typeof e.data==='object' && e.data.requestHeight) ping();
  });

  // init + observers
  if (document.readyState==='loading') document.addEventListener('DOMContentLoaded', ping); else ping();
  window.addEventListener('load', ping,   {passive:true});
  window.addEventListener('resize', ping, {passive:true});
  if ('MutationObserver' in window){
    new MutationObserver(ping).observe(document.body,{childList:true,subtree:true,attributes:true});
  }
  if ('ResizeObserver' in window){
    var ro=new ResizeObserver(ping);
    ro.observe(document.body);
    var w=WRAP(); if (w) ro.observe(w);
  }
  setInterval(ping, 1400);

  // step/nav → parent scroll to top (and remeasure soon after)
  document.addEventListener('click', function(e){
    if (e.target.closest('#nextBtn') ||
        e.target.closest('#prevBtn') ||
        e.target.closest('#proceedBtn') ||
        e.target.closest('.buy-now-btn')) {
      try { window.parent.postMessage({ scrollTop:true }, '*'); } catch(_){}
      setTimeout(ping, 30); setTimeout(ping, 250);
    }
  }, {passive:true});

  // ------- POPUP / LOADER DETECT & NOTIFY PARENT --------
  // add any overlay selectors you use:
  var OVERLAYS = [
    '#confirm-popup-conteiner',
    '.confirm-popup-conteiner',
    '.ewm-splash'             // loader
  ];

  function overlayOpen(){
    for (var i=0;i<OVERLAYS.length;i++){
      var el = document.querySelector(OVERLAYS[i]);
      if (!el) continue;
      var ds = getComputedStyle(el).display;
      if (ds && ds !== 'none') return true;
    }
    return false;
  }

  var wasOpen = false;
  function checkOverlay(){
    var isOpen = overlayOpen();
    if (isOpen && !wasOpen){
      wasOpen = true;
      // tell parent: lock + 100dvh + center
      try { window.parent.postMessage({ type:'POPUP_ON' }, '*'); } catch(_){}
      // a few recenter retries while CSS paints/keyboard moves stuff
      [80, 320, 900].forEach(function(t){
        setTimeout(function(){
          try { window.parent.postMessage({ type:'REQUEST_CENTER' }, '*'); } catch(_){}
        }, t);
      });
    } else if (!isOpen && wasOpen){
      wasOpen = false;
      try { window.parent.postMessage({ type:'POPUP_OFF' }, '*'); } catch(_){}
      setTimeout(ping, 60); // height restore
    }
  }

  setInterval(checkOverlay, 250);
  ['click','keyup','change','transitionend','animationend'].forEach(function(ev){
    document.addEventListener(ev, function(){ setTimeout(checkOverlay, 40); }, {passive:true});
  });

  // safety: neutralize theme min-heights that can block shrinking
  try{
    var fix = document.createElement('style');
    fix.textContent = `
      html, body { height:auto!important; min-height:0!important; overflow-x:hidden; }
      .form-container, #quote-container, .container { min-height:0!important; }
    `;
    document.head.appendChild(fix);
  }catch(_){}
})();

