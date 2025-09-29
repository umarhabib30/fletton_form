(function () {
  var raf = window.requestAnimationFrame || function (fn) { return setTimeout(fn,16); };
  var lastH=0, ticking=false;

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
    ticking=false;
  }
  function ping(){ if (!ticking){ ticking=true; (raf||setTimeout)(sendHeight,0); } }

  // parent request
  window.addEventListener('message', function(e){
    if (e.data && typeof e.data==='object' && e.data.requestHeight) ping();
  });

  // init
  if (document.readyState==='loading') document.addEventListener('DOMContentLoaded', ping); else ping();
  window.addEventListener('load', ping);
  window.addEventListener('resize', ping);

  if ('MutationObserver' in window){
    new MutationObserver(ping).observe(document.body,{childList:true,subtree:true,attributes:true});
  }
  if ('ResizeObserver' in window){
    var ro=new ResizeObserver(ping); ro.observe(document.body); ro.observe(document.documentElement);
  }
  setInterval(ping,1200);

  // Step buttons → parent scrollTop
  document.addEventListener('click',function(e){
    if (
      e.target.closest('#nextBtn') ||
      e.target.closest('#proceedBtn') ||
      e.target.closest('.buy-now-btn')
    ){
      try { window.parent.postMessage({ scrollTop:true }, '*'); } catch(_){}
      setTimeout(ping,30); setTimeout(ping,250);
    }
  },{passive:true});

  // Popup detect
  var POPUPS=['#confirm-popup-conteiner','.confirm-popup-conteiner'];
  function anyPopup(){
    for (var i=0;i<POPUPS.length;i++){
      var el=document.querySelector(POPUPS[i]);
      if (el && getComputedStyle(el).display!=='none') return true;
    }
    return false;
  }
  var was=false;
  function check(){
    var open=anyPopup();
    if(open && !was){ was=true; window.parent.postMessage({centerMe:true},'*'); }
    if(!open && was){ was=false; window.parent.postMessage({popupClosed:true},'*'); }
  }
  setInterval(check,200);
})();