(function () {
  /* 1) Measure ONLY the main wrapper (overlay/loader ko ignore) */
  function $wrap(){
    return document.querySelector('.form-container')
        || document.querySelector('#quote-container')
        || document.querySelector('.container')
        || document.body;  // fallback
  }

  function measure(){
    var el = $wrap();
    var r  = el.getBoundingClientRect();
    var cs = getComputedStyle(el);
    var h  = Math.ceil(r.height + (parseFloat(cs.marginTop)||0) + (parseFloat(cs.marginBottom)||0));
    if (!isFinite(h) || h < 1){
      h = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight, 320);
    }
    return h;
  }

  var raf   = window.requestAnimationFrame || function(fn){ return setTimeout(fn,16); };
  var lastH = 0, ticking = false;
  function send(){
    var h = measure();
    if (h !== lastH){
      lastH = h;
      try{ window.parent.postMessage({ frameHeight:h }, '*'); }catch(_){}
    }
    ticking = false;
  }
  function ping(){ if (!ticking){ ticking = true; (raf||setTimeout)(send,0); } }

  // Parent request
  window.addEventListener('message', function(e){
    if (e.data && typeof e.data==='object' && e.data.requestHeight) ping();
  });

  // Init + common triggers
  if (document.readyState==='loading') document.addEventListener('DOMContentLoaded', ping); else ping();
  window.addEventListener('load', ping, {passive:true});
  window.addEventListener('resize', ping, {passive:true});

  if ('MutationObserver' in window){
    new MutationObserver(ping).observe(document.body,{childList:true,subtree:true,attributes:true});
  }
  if ('ResizeObserver' in window){
    var ro = new ResizeObserver(ping);
    ro.observe(document.body);
    var w = $wrap(); if (w) ro.observe(w);
  }
  setInterval(ping, 1400);

  /* 2) Step buttons → parent scrollTop (aur height re-measure) */
  document.addEventListener('click', function(e){
    if (e.target.closest('#nextBtn') ||
        e.target.closest('#prevBtn') ||
        e.target.closest('#proceedBtn') ||
        e.target.closest('.buy-now-btn')) {
      try { window.parent.postMessage({ scrollTop:true }, '*'); } catch(_){}
      setTimeout(ping, 30); setTimeout(ping, 250);
    }
  }, {passive:true});

  /* 3) Popup/loader detect → center (parent lock karega) */
  // apne selectors yahan add/adjust kar sakte ho:
  var POPUPS = [
    '#confirm-popup-conteiner',     // confirm popup
    '.confirm-popup-conteiner',
    '.ewm-splash',                  // payment/loader overlay (agar use hota ho)
    '.modal.is-open'
  ];
  function anyOpen(){
    for (var i=0;i<POPUPS.length;i++){
      var el = document.querySelector(POPUPS[i]);
      if (el && getComputedStyle(el).display !== 'none') return true;
    }
    return false;
  }
  var wasOpen = false;
  function checkPopup(){
    var open = anyOpen();
    if (open && !wasOpen){
      wasOpen = true;
      try{ window.parent.postMessage({ centerMe:true }, '*'); }catch(_){}
    } else if (!open && wasOpen){
      wasOpen = false;
      try{ window.parent.postMessage({ popupClosed:true }, '*'); }catch(_){}
    }
  }
  setInterval(checkPopup, 250);
  ['click','keyup','change','transitionend','animationend'].forEach(function(ev){
    document.addEventListener(ev, function(){ setTimeout(function(){ checkPopup(); ping(); }, 40); }, {passive:true});
  });

  /* 4) Safety: koi min-height theme se aa rahi ho to neutralize (shrink-enable) */
  try{
    var fix = document.createElement('style');
    fix.textContent = `
      html, body { height:auto!important; min-height:0!important; overflow-x:hidden; }
      .form-container, #quote-container, .container { min-height:0!important; }
    `;
    document.head.appendChild(fix);
  }catch(_){}
})();

