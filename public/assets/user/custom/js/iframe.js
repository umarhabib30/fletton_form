
(function () {
  /* --- exact wrapper choose --- */
  function wrapper(){
    return document.querySelector('.form-container')
        || document.querySelector('#quote-container')
        || document.querySelector('.container')
        || document.body;
  }

  /* --- measure only wrapper height (overlay/loader ignore) --- */
  function measure(){
    var el = wrapper();
    var cs = getComputedStyle(el);
    // element ke khud ke height + margins
    var h  = Math.ceil(el.getBoundingClientRect().height
             + (parseFloat(cs.marginTop)||0)
             + (parseFloat(cs.marginBottom)||0));
    if (!isFinite(h) || h < 1) {
      // fallback
      h = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight, 320);
    }
    return h;
  }

  /* --- sender (grow + shrink) --- */
  var raf = window.requestAnimationFrame || function(fn){ return setTimeout(fn,16); };
  var lastH = 0, busy = false;
  function send(){
    var h = measure();
    if (h !== lastH){
      lastH = h;
      try{ window.parent.postMessage({ frameHeight:h }, '*'); }catch(_){}
    }
    busy = false;
  }
  function tick(){ if (!busy){ busy = true; (raf||setTimeout)(send,0); } }

  // parent request
  window.addEventListener('message', function(e){
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) tick();
  });

  // init + common triggers
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', tick); else tick();
  window.addEventListener('load',  tick, {passive:true});
  window.addEventListener('resize', tick, {passive:true});

  // observe DOM/layout changes
  if ('MutationObserver' in window){
    new MutationObserver(tick).observe(document.body, { childList:true, subtree:true, attributes:true });
  }
  if ('ResizeObserver' in window){
    var ro = new ResizeObserver(tick);
    ro.observe(document.body);
    var w = wrapper(); if (w) ro.observe(w);
  }
  setInterval(tick, 1400); // keep-alive

  /* --- step change -> top + remeasure --- */
  function stepTop(){
    try{ window.parent.postMessage({ scrollTop:true }, '*'); }catch(_){}
    tick();
  }
  document.addEventListener('click', function(e){
    if (e.target.closest('#nextBtn') ||
        e.target.closest('#prevBtn') ||
        e.target.closest('#proceedBtn') ||
        e.target.closest('.buy-now-btn')) {
      stepTop();
    }
  }, {passive:true});

  /* --- popup detect -> center (loader untouched) --- */
  var POPUPS = ['#confirm-popup-conteiner','.confirm-popup-conteiner','.modal.is-open'];
  var wasOpen = false;
  function anyOpen(){
    for (var i=0;i<POPUPS.length;i++){
      var el = document.querySelector(POPUPS[i]);
      if (!el) continue;
      if (getComputedStyle(el).display !== 'none') return true;
    }
    return false;
  }
  function checkPopup(){
    var open = anyOpen();
    if (open && !wasOpen){
      wasOpen = true;
      try{ window.parent.postMessage({ centerMe:true }, '*'); }catch(_){}
    } else if (!open && wasOpen){
      wasOpen = false;
    }
  }
  setInterval(function(){ checkPopup(); tick(); }, 250);
  ['click','keyup','change','transitionend','animationend'].forEach(function(ev){
    document.addEventListener(ev, function(){ setTimeout(function(){ checkPopup(); tick(); }, 40); }, {passive:true});
  });

  /* --- ensure no fixed min-heights keep old size --- */
  try{
    var fix = document.createElement('style');
    fix.textContent = `
      html, body { height:auto!important; min-height:0!important; overflow-x:hidden; }
      .form-container, #quote-container, .container { min-height:0!important; }
    `;
    document.head.appendChild(fix);
  }catch(_){}
})();

