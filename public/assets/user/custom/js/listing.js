

/* ----------------------------- State & Helpers ----------------------------- */
let selectedLevel = null; // which level user selected in confirm popup
let currentStep = 1;    // 1 = level cards, 2 = add-ons step

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
function readAllAddonsSum() {
    let sum = 0;

    // a) legacy selects
    document.querySelectorAll('.addons-grid select.addon').forEach(sel => {
        sum += num(sel.dataset.cost);
    });

    // b) new radios (cost on the group)
    document.querySelectorAll('.addons-grid .radio-group.addon').forEach(group => {
        sum += num(group.getAttribute('data-cost'));
    });

    return sum;
}

function updateLevel3Totals() {
    // Base Level 3 price (prefer hidden input; fallback to inline hidden)
    const baseL3 =
        num(document.getElementById('level3_price')?.value) ||
        num(document.getElementById('level3-base-price')?.value);

    // Current selection summary
    const { selectedCount, totalAddons, addonsSum } = readAddonsState();

    // Determine if all add-ons are selected (3 in total)
    const allAddonsSelected = selectedCount === 3;

    // Current selection total (this powers the visible "Total Cost" on L3 step)
    let total;
    if (allAddonsSelected) {
        // If all add-ons are selected, use Level 4 price
        const baseL4 =
            num(document.getElementById('level4_price')?.value) ||
            num(document.getElementById('level4-base-price')?.value);
        total = baseL4;
    } else {
        // Otherwise, use Level 3 + selected add-ons
        total = baseL3 + addonsSum;
    }

    // Update Total UI (current selection)
    const totalEl = document.getElementById('total_with_addon');
    if (totalEl) {
        totalEl.setAttribute('data-total', String(total));
        totalEl.innerHTML = '<span class="label">Total Cost :</span> ' + gbp(total);
    }

    // --- NEW: compute "all add-ons selected" comparison for the banner ---
    const addonsSumAll = readAllAddonsSum();               // sum of all three (or all available)
    const totalAllAddons = baseL3 + addonsSumAll;          // L3 if user took every add-on
    const baseL4 =
        num(document.getElementById('level4_price')?.value) ||
        num(document.getElementById('level4-base-price')?.value);
    const savingsAll = totalAllAddons - baseL4;            // benefit if they went to Level 4 instead

    // Show the Level 4 banner ALWAYS, with savings based on the "all add-ons" scenario
    const upsellWrap = document.querySelector('.level4-all-inlcude-addons');
    const saveNumEl = upsellWrap?.querySelector('.save-price');
    const saveTxtEl = upsellWrap?.querySelector('.level-price');

    if (upsellWrap) {
        upsellWrap.style.display = 'block'; // always visible

        // If Level 4 is cheaper than L3+all-addons, show positive savings; else show £0
        const displaySavings = savingsAll > 0 ? savingsAll : 0;
        if (saveNumEl) saveNumEl.textContent = gbp(displaySavings);
        if (saveTxtEl) saveTxtEl.textContent = gbp(displaySavings);
    }
}


/* ---------------------- Add-to-Survey toggle (radio-style) ----------------- */
/** Returns true if group's "Yes" is currently checked */
function isGroupOn(groupEl) {
    const yes = groupEl?.querySelector('input[type="radio"][value="1"]');
    return !!(yes && yes.checked);
}

/** Toggle a single add-on button + its hidden radios */
function toggleAddon(btn) {
    const groupId = btn.getAttribute('data-group');
    const group = document.getElementById(groupId);
    if (!group) return;

    const yes = group.querySelector('input[type="radio"][value="1"]');
    const no = group.querySelector('input[type="radio"][value="0"]');

    const willActivate = !btn.classList.contains('active');

    if (willActivate && yes) {
        yes.checked = true;
        yes.dispatchEvent(new Event('change', { bubbles: true })); // let existing listeners fire
    } else if (!willActivate && no) {
        no.checked = true;
        no.dispatchEvent(new Event('change', { bubbles: true }));
    }

    btn.classList.toggle('active', willActivate);

    // Recompute using existing logic and refresh note
    updateLevel3Totals();
}


/** Sync initial button .active state from radios (in case of server defaults) */
function initAddonButtons() {
    document.querySelectorAll('.addon-btn').forEach(btn => {
        const group = document.getElementById(btn.getAttribute('data-group'));
        if (group && isGroupOn(group)) btn.classList.add('active');
    });
}

/* ----------------------------- Confirm Popup UX ---------------------------- */
function showConfirmPopup(level) {
    selectedLevel = level;
    const popup = document.getElementById('confirm-popup-conteiner');
    if (popup) popup.style.display = 'flex';
}

function closePopup() {
    const popup = document.getElementById('confirm-popup-conteiner');
    const wait = document.getElementById('wait-notice');
    if (popup) popup.style.display = 'none';
    if (wait) wait.style.display = 'none';

    // Reset any button loaders in the popup & elsewhere
    document.querySelectorAll('.btn-style').forEach(btn => {
        btn.classList.remove('disabled');
        const loader = btn.querySelector('.btn-loader');
        const text = btn.querySelector('.btn-text');
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
    const btn = document.querySelector('.confirm-yes');
    const loader = btn?.querySelector('.btn-loader');
    const text = btn?.querySelector('.btn-text');
    const wait = document.getElementById('wait-notice');

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
    const text = button.querySelector('.btn-text');

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
        const levelTotalInput = document.getElementById('level_total');

        // Check if all three add-ons are selected
        const { selectedCount } = readAddonsState(); // Returns the number of selected add-ons

        let levelTotal = 0;
        let selectedLevel = level;

        // If all three add-ons are selected, set level to 4 and use Level 4 price
        if (selectedCount === 3) {
            selectedLevel = 4; // Set to Level 4
            levelTotal = document.getElementById('level4_price')?.value || 0;
        } else {
            // Use the selected level price (fallback to Level 3 logic)
            if (String(level) === '1') {
                levelTotal = document.getElementById('level1_price')?.value || 0;
            } else if (String(level) === '2') {
                levelTotal = document.getElementById('level2_price')?.value || 0;
            } else if (String(level) === '3') {
                // Use numeric data from #total_with_addon
                levelTotal = document.getElementById('total_with_addon')?.getAttribute('data-total') || 0;
            }
        }

        // Update the level and total in the hidden inputs
        if (selectedLevelInput) selectedLevelInput.value = selectedLevel;
        if (levelTotalInput) levelTotalInput.value = levelTotal;

        // Show the confirmation popup
        showConfirmPopup(selectedLevel);

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

    /* 2) Add-ons changes → recalc totals (legacy select + radios) */
    const recalcSelector =
        '.addons-grid select.addon, .addons-grid .radio-group.addon input[type="radio"]';

    document.addEventListener('change', function (e) {
        if (e.target.matches(recalcSelector)) {
            updateLevel3Totals();
        }
    });

    // 2b) Add-to-Survey button clicks (radio-style)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.addon-btn');
        if (btn) toggleAddon(btn);
    });

    // Initial compute & sync on load
    updateLevel3Totals();
    initAddonButtons();

    /* 3) “Instruct & Pay” buttons */
    document.querySelectorAll('.buy-now-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const level = this.getAttribute('data-level'); // "1" | "2" | "3" | "4"
            handleBuyNow(this, level);
        });
    });

    /* 4) Height sync */
    sendHeightToParent();
    if ('ResizeObserver' in window) {
        const ro = new ResizeObserver(() => sendHeightToParent());
        ro.observe(document.body);
    }
    window.addEventListener('load', sendHeightToParent);
    window.addEventListener('resize', sendHeightToParent);
});

/* ------------------------------ Global Exports ----------------------------- */
/* If these are called inline from HTML, ensure they exist on window */
window.showStep1 = showStep1;
window.showAddons = showAddons;
window.showConfirmPopup = showConfirmPopup;
window.closePopup = closePopup;
window.proceedWithBooking = proceedWithBooking;
window.sendHeightToParent = sendHeightToParent;
// in case HTML needs it
