
(function () {
  // ---- Make shrink possible (safety) ----
  try{
    var style = document.createElement('style');
    style.textContent = `
      html, body { height:auto !important; min-height:0 !important; overflow-x:hidden; }
      /* koi wrapper accidentally fixed height na laga de */
      .form-container, #quote-container { min-height:0 !important; }
    `;
    document.head.appendChild(style);
  }catch(_){}

  // ---- Precise height of the visible form area ----
  function measureHeight(){
    // Preferred wrappers (adjust if your top wrapper differs)
    var wrap =
      document.querySelector('.form-container') ||
      document.querySelector('#quote-container') ||
      document.querySelector('.container') ||
      document.body;

    var rect   = wrap.getBoundingClientRect();
    var styles = getComputedStyle(wrap);
    var mb     = parseFloat(styles.marginBottom) || 0;
    // viewport top is 0 inside iframe, so rect.bottom is visible content height
    var h = Math.ceil(rect.bottom + mb + 1);

    // Minimum sensible height (avoid 0 during reflow)
    if (!isFinite(h) || h < 320) h = Math.max(
      document.documentElement.scrollHeight,
      document.body.scrollHeight,
      320
    );
    return h;
  }

  // ---- Throttled sender (both grow and shrink) ----
  var raf   = window.requestAnimationFrame || function(fn){ return setTimeout(fn,16); };
  var lastH = 0, sending = false;
  function send(){
    var h = measureHeight();
    if (h !== lastH){
      lastH = h;
      try { window.parent.postMessage({ frameHeight:h }, '*'); } catch(_){}
    }
    sending = false;
  }
  function queue(){ if (!sending){ sending = true; (raf||setTimeout)(send, 0); } }

  // Parent requests
  window.addEventListener('message', function(e){
    if (e.data && typeof e.data === 'object' && e.data.requestHeight) queue();
  });

  // Initial + common triggers
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', queue); else queue();
  window.addEventListener('load',  queue, {passive:true});
  window.addEventListener('resize', queue, {passive:true});

  // React to DOM changes (step switches, validations, etc.)
  if ('MutationObserver' in window){
    new MutationObserver(queue).observe(document.body, { childList:true, subtree:true, attributes:true });
  }
  if ('ResizeObserver' in window){
    var ro = new ResizeObserver(queue);
    ro.observe(document.body);
    var pref = document.querySelector('.form-container') || document.querySelector('#quote-container');
    if (pref) ro.observe(pref);
  }
  // Safety keep-alive
  setInterval(queue, 1400);

  // ---- Step → parent top ----
  function notifyStepTop(){
    try { window.parent.postMessage({ scrollTop:true }, '*'); } catch(_){}
  }
  document.addEventListener('click', function(e){
    if (e.target.closest('#nextBtn') ||
        e.target.closest('#prevBtn') ||
        e.target.closest('#proceedBtn') ||
        e.target.closest('.buy-now-btn')) {
      notifyStepTop();
      queue(); // height may shrink on new step
    }
  }, {passive:true});

  // ---- Popup detect → center only (loader untouched) ----
  var POPUPS = ['#confirm-popup-conteiner','.confirm-popup-conteiner','.modal.is-open'];
  var wasOpen = false;
  function popupOpenEl(){
    for (var i=0;i<POPUPS.length;i++){
      var el = document.querySelector(POPUPS[i]);
      if (!el) continue;
      var disp = getComputedStyle(el).display;
      if (disp && disp !== 'none') return el;
    }
    return null;
  }
  function checkPopup(){
    var el = popupOpenEl();
    if (el && !wasOpen){ wasOpen = true;  try{ window.parent.postMessage({ centerMe:true }, '*'); }catch(_){} }
    else if (!el && wasOpen){ wasOpen = false; }
  }
  setInterval(function(){ checkPopup(); queue(); }, 250); // also refresh height while popups toggle
  ['click','keyup','change','transitionend','animationend'].forEach(function(ev){
    document.addEventListener(ev, function(){ setTimeout(function(){ checkPopup(); queue(); }, 40); }, {passive:true});
  });

})();

