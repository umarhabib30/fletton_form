
(function () {
  // --- Helpers to talk to parent ---
  function parentMsg(obj){ try { window.parent.postMessage(obj, '*'); } catch(_){} }

  // STEP CHANGE → parent upar laao + loader short burst (optional)
  function stepChange() {
    parentMsg({ scrollTop: true });
  }

  // “heavy” actions: show overlay immediately for UX
  function showLoader(){ parentMsg({ type: 'show-loader' }); }
  function hideLoader(){ parentMsg({ type: 'hide-loader' }); }

  // POPUP detect → true center while open
  var POPUPS = [
    '#confirm-popup-conteiner',
    '.confirm-popup-conteiner',
    '.ewm-splash',
    '.modal.is-open'
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
  var popupWasOpen = false;
  function checkPopup(){
    var el = anyPopup();
    if (el && !popupWasOpen){
      popupWasOpen = true;
      parentMsg({ centerMe: true });       // center + (parent shows overlay in our setup)
    } else if (!el && popupWasOpen){
      popupWasOpen = false;
      parentMsg({ popupClosed: true });    // parent hide overlay
    }
  }
  setInterval(checkPopup, 250);
  ['click','keyup','change'].forEach(function(ev){
    document.addEventListener(ev, function(){ setTimeout(checkPopup, 40); }, {passive:true});
  });

  // BUTTON WIRING
  document.addEventListener('click', function(e){
    // next/previous/proceed inside form wizard
    if (e.target.closest('#nextBtn') || e.target.closest('#prevBtn') || e.target.closest('#proceedBtn')) {
      stepChange();
      showLoader();              // brief loader; hide after small delay if you like
      setTimeout(hideLoader, 1200);
    }

    // cards (booking step) / confirm proceed
    if (e.target.closest('.buy-now-btn') || e.target.closest('.confirm-yes')) {
      stepChange();
      showLoader();
      // popup khulne par overlay parent hi control karega via checkPopup()
    }

    // final submit → keep loader until navigation
    if (e.target.closest('#submitBtn')) {
      showLoader();
      stepChange();
      // navigation ke baad parent page hi change ho jata hai; hide ki zaroorat nahi
    }
  }, {passive:true});

  // On page ready, ensure parent not stuck
  window.addEventListener('load', function(){ hideLoader(); });

  // OPTIONAL: jab aapke paas final payment URL ho:
  // parentMsg({ type:'go-to-payment', url:'https://flettons.group/flettons-order/?...' });
})();

