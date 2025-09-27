(function(){
  // ---------- A) Auto-height broadcaster ----------
  function sendHeight(){
    var h = Math.max(
      document.body.scrollHeight, document.documentElement.scrollHeight,
      document.body.offsetHeight, document.documentElement.offsetHeight,
      document.body.clientHeight, document.documentElement.clientHeight
    );
    try{ window.parent.postMessage({ frameHeight: h }, '*'); }catch(_){}
  }

  window.addEventListener('message', function(e){
    if (e.data && e.data.requestHeight) sendHeight();
  });

  document.addEventListener('DOMContentLoaded', sendHeight);
  window.addEventListener('load', sendHeight);
  window.addEventListener('resize', function(){ requestAnimationFrame(sendHeight); });

  if ('MutationObserver' in window){
    var mo = new MutationObserver(function(){ requestAnimationFrame(sendHeight); });
    mo.observe(document.body, { childList:true, subtree:true, attributes:true });
  }
  if ('ResizeObserver' in window){
    var ro = new ResizeObserver(function(){ requestAnimationFrame(sendHeight); });
    ro.observe(document.documentElement);
    ro.observe(document.body);
  }
  setInterval(sendHeight, 1200);

  // ---------- B) Popup open/close detector ----------
  var POP = document.getElementById('confirm-popup-conteiner');

  function notifyOpen(){
    // lock child page scroll and tell parent to lock + 100vh
    document.documentElement.classList.add('child-lock');
    document.body.classList.add('child-lock');
    try{
      window.parent.postMessage({ popupOpen:true, centerMe:true }, '*');
    }catch(_){}
  }
  function notifyClose(){
    document.documentElement.classList.remove('child-lock');
    document.body.classList.remove('child-lock');
    try{
      window.parent.postMessage({ popupClose:true }, '*');
    }catch(_){}
    // height normal pe wapas
    sendHeight();
  }

  // show/hide helpers you can call from your flow
  window.openConfirmPopup = function(){
    if (!POP) return;
    POP.style.display = 'flex';
    notifyOpen();
  };
  window.closeConfirmPopup = function(){
    if (!POP) return;
    POP.style.display = 'none';
    notifyClose();
  };

  // Agar aap already popup ko CSS/JS se display:flex/block par laa rahe ho,
  // to MutationObserver se bhi catch kar lete hain:
  if (POP && 'MutationObserver' in window){
    var wasVisible = false;
    var ob = new MutationObserver(function(){
      var nowVisible = window.getComputedStyle(POP).display !== 'none';
      if (nowVisible && !wasVisible) notifyOpen();
      if (!nowVisible && wasVisible) notifyClose();
      wasVisible = nowVisible;
    });
    ob.observe(POP, { attributes:true, attributeFilter:['style','class'] });
    // init state
    wasVisible = window.getComputedStyle(POP).display !== 'none';
    if (wasVisible) notifyOpen();
  }
})();
