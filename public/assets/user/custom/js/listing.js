
let selectedLevel = null; let currentStep = 1;

function showStep1() {
    document.querySelector('.step-1').style.display = 'block';
    document.querySelector('.step-2').classList.remove('active');
    currentStep = 1; sendHeightToParent();
}
function showAddons() {
    document.querySelector('.step-1').style.display = 'none';
    document.querySelector('.step-2').classList.add('active');
    currentStep = 2; setTimeout(sendHeightToParent, 100);
}
function updateLevel3Price() {
    const base = parseInt(document.getElementById('level3-base-price').value);
    let total = base;
    document.querySelectorAll('.addon').forEach(a => { if (a.value === '1') { total += parseInt(a.dataset.cost) } });
    document.querySelector('.step-2 .level-price.addons').textContent = '£' + total;
   

    const selected = [...document.querySelectorAll('.addon')].filter(a => a.value === '1').length;
    const totalAdd = document.querySelectorAll('.addon').length;
    const level4 = parseInt(document.getElementById('level4-base-price').value);

    const wrap = document.querySelector('.level4-all-inlcude-addons');
    const saveSpan = document.querySelector('.save-price');
    if (selected === totalAdd && total > level4) {
        const save = total - level4;
        if (saveSpan) { saveSpan.textContent = '£' + save }
        wrap.style.display = 'block';
    } else { wrap.style.display = 'none' }
}

function showConfirmPopup(level) {
    selectedLevel = level;
    document.getElementById('confirm-popup-conteiner').style.display = 'flex';
}
function closePopup() {
    document.getElementById('confirm-popup-conteiner').style.display = 'none';
    document.getElementById('wait-notice').style.display = 'none';
    document.querySelectorAll('.btn-style').forEach(btn => {
        btn.classList.remove('disabled');
        const l = btn.querySelector('.btn-loader'); const t = btn.querySelector('.btn-text');
        if (l) l.style.display = 'none'; if (t) t.textContent = t.dataset?.defaultText || t.textContent.replace('Loading...', 'Instruct & Pay').replace('Processing...', 'Proceed');
    });
}
function proceedWithBooking() {
    const agree = document.getElementById('termsCheckbox');
    if (!agree.checked) {
        $(function () {
            toastr.options = {
                positionClass: "toast-top-right",
                timeOut: 3500,
                closeButton: true,
                progressBar: true,
                newestOnTop: true,
            };
            toastr.error('Please agree to the terms and conditions.');
        });
        return;
    }
    const btn = document.querySelector('.confirm-yes');
    const loader = btn.querySelector('.btn-loader'); const text = btn.querySelector('.btn-text');
    const wait = document.getElementById('wait-notice');
    btn.classList.add('disabled'); if (loader) loader.style.display = 'inline-block'; if (text) text.textContent = 'Processing...';
    wait.style.display = 'block';
    // setTimeout(() => { alert(`Redirecting to payment page for Level ${selectedLevel}...`); closePopup(); }, 2000);
    $('.quote-f').submit();
}
function handleBuyNow(button, level) {
    const loader = button.querySelector('.btn-loader'); const text = button.querySelector('.btn-text');
    button.classList.add('disabled'); if (loader) loader.style.display = 'inline-block';
    if (text) { text.dataset.defaultText = text.textContent; text.textContent = 'Loading...' }
    setTimeout(() => { showConfirmPopup(level); button.classList.remove('disabled'); if (loader) loader.style.display = 'none'; if (text) text.textContent = text.dataset.defaultText; }, 400);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.addon').forEach(a => a.addEventListener('change', updateLevel3Price));
    document.querySelectorAll('.buy-now-btn').forEach(btn => btn.addEventListener('click', function () { handleBuyNow(this, this.dataset.level) }));
    document.querySelectorAll('.level-choice').forEach(c => c.addEventListener('click', function () { document.querySelectorAll('.level-choice').forEach(x => x.classList.remove('selected')); this.classList.add('selected') }));

    // height postMessage (for iframe use)
    sendHeightToParent();
});

function sendHeightToParent() { setTimeout(() => { const h = document.body.scrollHeight; window.parent.postMessage({ frameHeight: h }, "*") }, 100) }
if ('ResizeObserver' in window) {
    const ro = new ResizeObserver(() => sendHeightToParent()); ro.observe(document.body);
}
window.addEventListener('load', sendHeightToParent);
window.addEventListener('resize', sendHeightToParent);
// --- toastr ---
