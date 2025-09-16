/* ============================================================================
 * listing.js  (Fully Commented)
 * ----------------------------------------------------------------------------
 * Supports BOTH: <select class="addon"> and <div class="radio-group addon">
 * Radio markup example:
 *   <div class="radio-group addon" data-cost="123">
 *     <input type="radio" name="..." value="0" checked> No
 *     <input type="radio" name="..." value="1"> Yes
 *   </div>
 * Select markup example:
 *   <select class="addon" data-cost="123">
 *     <option value="0">No</option>
 *     <option value="1">Yes</option>
 *   </select>
 * ----------------------------------------------------------------------------
 * Pages expect hidden inputs:
 *   #level1_price, #level2_price, #level3_price, #level4_price
 *   #selected_level, #level_total
 * And totals UI:
 *   #total_with_addon (we set text + data-total)
 * ----------------------------------------------------------------------------
 * Toastr is optional; used only for terms checkbox error.
 * ========================================================================== */

/* ----------------------------- State & Helpers ----------------------------- */
let selectedLevel = null; // which level user selected in confirm popup
let currentStep   = 1;    // 1 = level cards, 2 = add-ons step

/** Convert any currency/text like "£1,234.50" to number 1234.5 */
function num(val) {
  const n = parseFloat(String(val || '').replace(/[^\d.]/g, ''));
  return isNaN(n) ? 0 : n;
}

/** Format a number to GBP string (no trailing .00) */
function gbp(n) {
  const rounded = Math.round(n * 100) / 100;
  const s = rounded.toFixed(2).replace(/\.00$/, '');
  return '£' + s;
}

/* ----------------------------- Step Navigation ----------------------------- */
function showStep1() {
  // Show level cards
  const s1 = document.querySelector('.step-1');
  const s2 = document.querySelector('.step-2');
  if (s1) s1.style.display = 'block';
  if (s2) s2.classList.remove('active');
  currentStep = 1;
  sendHeightToParent();
}

function showAddons() {
  // Show add-ons panel
  const s1 = document.querySelector('.step-1');
  const s2 = document.querySelector('.step-2');
  if (s1) s1.style.display = 'none';
  if (s2) s2.classList.add('active');
  currentStep = 2;

  // Ensure totals reflect current choices after panel opens
  setTimeout(() => {
    updateLevel3Totals();
    sendHeightToParent();
  }, 100);
}

/* ---------------------- Add-ons Reading (Select + Radio) ------------------- */
/**
 * Returns:
 *  {
 *    selectedCount: <number of add-ons that are 'Yes'>,
 *    totalAddons:   <total number of add-ons on page>,
 *    addonsSum:     <sum of costs for chosen add-ons>
 *  }
 */
function readAddonsState() {
  let addonsSum = 0;
  let selectedCount = 0;
  let totalAddons = 0;

  // a) legacy selects: <select class="addon" data-cost="...">
  document.querySelectorAll('.addons-grid select.addon').forEach(sel => {
    totalAddons += 1;
    const yes = sel.value === '1';
    const cost = num(sel.dataset.cost);
    if (yes) {
      addonsSum += cost;
      selectedCount += 1;
    }
  });

  // b) new radios: <div class="radio-group addon" data-cost="...">
  document.querySelectorAll('.addons-grid .radio-group.addon').forEach(group => {
    totalAddons += 1;
    const checked = group.querySelector('input[type="radio"]:checked');
    const val = checked ? checked.value : '0';
    const yes = val === '1';
    const cost = num(group.getAttribute('data-cost'));
    if (yes) {
      addonsSum += cost;
      selectedCount += 1;
    }
  });

  return { selectedCount, totalAddons, addonsSum };
}

/* ------------------------------ Totals Update ------------------------------ */
/**
 * Calculates L3 total = base L3 + all chosen add-ons
 * Updates:
 *   #total_with_addon text  => "Total £X"
 *   #total_with_addon[data-total] => numeric total
 * Controls "Select Level 3+" upsell visibility when ALL add-ons are selected
 */
function updateLevel3Totals() {
  // Base Level 3 price (prefer hidden input; fallback to inline hidden)
  const baseL3 =
    num(document.getElementById('level3_price')?.value) ||
    num(document.getElementById('level3-base-price')?.value);

  // Add-ons selection summary
  const { selectedCount, totalAddons, addonsSum } = readAddonsState();

  // Compute total
  const total = baseL3 + addonsSum;

  // Update Total UI
  const totalEl = document.getElementById('total_with_addon');
  if (totalEl) {
    totalEl.setAttribute('data-total', String(total));
    totalEl.innerHTML = '<span class="label">Total</span> ' + gbp(total);
  }

  // Level 3+ comparison (show upsell banner only if ALL add-ons selected & L3+ cheaper)
  const baseL4 =
    num(document.getElementById('level4_price')?.value) ||
    num(document.getElementById('level4-base-price')?.value);

  const upsellWrap = document.querySelector('.level4-all-inlcude-addons');
  const saveNumEl  = upsellWrap?.querySelector('.save-price');
  const saveTxtEl  = upsellWrap?.querySelector('.level-price');

  const savings = total - baseL4;

  if (upsellWrap) {
    if (totalAddons > 0 && selectedCount === totalAddons && savings > 0) {
      // All add-ons selected & L3+ is cheaper => show banner with dynamic savings
      if (saveNumEl) saveNumEl.textContent = gbp(savings);
      if (saveTxtEl) saveTxtEl.textContent = gbp(savings);
      upsellWrap.style.display = 'block';
    } else {
      upsellWrap.style.display = 'none';
    }
  }
}

/* ----------------------------- Confirm Popup UX ---------------------------- */
function showConfirmPopup(level) {
  selectedLevel = level;
  const popup = document.getElementById('confirm-popup-conteiner');
  if (popup) popup.style.display = 'flex';
}

function closePopup() {
  const popup = document.getElementById('confirm-popup-conteiner');
  const wait  = document.getElementById('wait-notice');
  if (popup) popup.style.display = 'none';
  if (wait)  wait.style.display  = 'none';

  // Reset any button loaders in the popup & elsewhere
  document.querySelectorAll('.btn-style').forEach(btn => {
    btn.classList.remove('disabled');
    const loader = btn.querySelector('.btn-loader');
    const text   = btn.querySelector('.btn-text');
    if (loader) loader.style.display = 'none';
    if (text) {
      // Restore default text if we stored it earlier
      if (text.dataset?.defaultText) {
        text.textContent = text.dataset.defaultText;
      } else {
        // Heuristic fallback
        text.textContent = text.textContent
          .replace('Loading...', 'Instruct & Pay')
          .replace('Processing...', 'Proceed');
      }
    }
  });
}

function proceedWithBooking() {
  // Terms checkbox must be ticked
  const agree = document.getElementById('termsCheckbox');
  if (!agree || !agree.checked) {
    // Toastr error (requires jQuery + toastr)
    if (typeof $ !== 'undefined' && typeof toastr !== 'undefined') {
      (function () {
        toastr.options = {
          positionClass: 'toast-top-right',
          timeOut: 3500,
          closeButton: true,
          progressBar: true,
          newestOnTop: true,
        };
        toastr.error('Please agree to the terms and conditions.');
      })();
    } else {
      alert('Please agree to the terms and conditions.');
    }
    return;
  }

  // Lock the confirm button with loader
  const btn    = document.querySelector('.confirm-yes');
  const loader = btn?.querySelector('.btn-loader');
  const text   = btn?.querySelector('.btn-text');
  const wait   = document.getElementById('wait-notice');

  if (btn) btn.classList.add('disabled');
  if (loader) loader.style.display = 'inline-block';
  if (text) {
    text.dataset.defaultText = text.textContent;
    text.textContent = 'Processing...';
  }
  if (wait) wait.style.display = 'block';

  // Submit main form
  const form = document.querySelector('.quote-f');
  if (form) form.submit();
}

/* --------------------------- "Instruct & Pay" UX --------------------------- */
function handleBuyNow(button, level) {
  // Button loader while opening confirm popup
  const loader = button.querySelector('.btn-loader');
  const text   = button.querySelector('.btn-text');

  button.classList.add('disabled');
  if (loader) loader.style.display = 'inline-block';
  if (text) {
    text.dataset.defaultText = text.textContent;
    text.textContent = 'Loading...';
  }

  // A tiny delay to show feedback
  setTimeout(() => {
    // Stash selected level + total BEFORE showing confirm
    const selectedLevelInput = document.getElementById('selected_level');
    const levelTotalInput    = document.getElementById('level_total');
    if (selectedLevelInput) selectedLevelInput.value = level || '';

    let levelTotal = 0;
    if (String(level) === '1') {
      levelTotal = document.getElementById('level1_price')?.value || 0;
    } else if (String(level) === '2') {
      levelTotal = document.getElementById('level2_price')?.value || 0;
    } else if (String(level) === '3') {
      // Use numeric data from #total_with_addon
      levelTotal = document.getElementById('total_with_addon')?.getAttribute('data-total') || 0;
    } else if (String(level) === '4') {
      levelTotal = document.getElementById('level4_price')?.value || 0;
    }
    if (levelTotalInput) levelTotalInput.value = levelTotal;

    showConfirmPopup(level);

    // Restore button to normal (since popup took over)
    button.classList.remove('disabled');
    if (loader) loader.style.display = 'none';
    if (text) text.textContent = text.dataset.defaultText || text.textContent;
  }, 400);
}

/* ---------------------------- Height PostMessage --------------------------- */
function sendHeightToParent() {
  setTimeout(() => {
    const h = document.body.scrollHeight;
    try {
      window.parent.postMessage({ frameHeight: h }, '*');
    } catch (e) {
      // ignore if cross-origin blocked
    }
  }, 100);
}

/* -------------------------- DOM Ready: Wire Events ------------------------- */
document.addEventListener('DOMContentLoaded', () => {
  /* 1) Level tile visual selection (optional) */
  document.querySelectorAll('.level-choice').forEach(card => {
    card.addEventListener('click', function () {
      document.querySelectorAll('.level-choice').forEach(x => x.classList.remove('selected'));
      this.classList.add('selected');
    });
  });

  /* 2) “Choose Add-ons” button (in Level 3 card) should call showAddons()
   *    (Your HTML already calls onclick="showAddons()", so nothing to do here)
   */

  /* 3) Add-ons changes → recalc totals
   *    We listen to BOTH legacy selects and radios inside .radio-group.addon
   */
  const recalcSelector =
    '.addons-grid select.addon, .addons-grid .radio-group.addon input[type="radio"]';

  document.addEventListener('change', function (e) {
    if (e.target.matches(recalcSelector)) {
      updateLevel3Totals();
    }
  });

  // Initial compute on load (ensures totals are consistent with defaults)
  updateLevel3Totals();

  /* 4) “Instruct & Pay” buttons */
  document.querySelectorAll('.buy-now-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const level = this.getAttribute('data-level'); // "1" | "2" | "3" | "4"
      handleBuyNow(this, level);
    });
  });

  /* 5) Back button from Step 2 → Step 1
   *    Your HTML uses onclick="showStep1()", so no extra binding required.
   */

  /* 6) Confirm popup buttons are inline onclick; we keep them. */

  /* 7) iFrame height sync */
  sendHeightToParent();

  /* 8) Optional live resize observer for dynamic height */
  if ('ResizeObserver' in window) {
    const ro = new ResizeObserver(() => sendHeightToParent());
    ro.observe(document.body);
  }

  window.addEventListener('load', sendHeightToParent);
  window.addEventListener('resize', sendHeightToParent);
});

/* ------------------------------ Global Exports ----------------------------- */
/* If these are called inline from HTML, ensure they exist on window */
window.showStep1            = showStep1;
window.showAddons           = showAddons;
window.showConfirmPopup     = showConfirmPopup;
window.closePopup           = closePopup;
window.proceedWithBooking   = proceedWithBooking;
window.sendHeightToParent   = sendHeightToParent;
