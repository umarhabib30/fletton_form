(function () {
    var raf = window.requestAnimationFrame || function (fn) { return setTimeout(fn, 16); };
    var lastH = 0, ticking = false;
    var heightInterval = null; // Variable to hold the interval timer

    // --- Helper Functions for Iframe Resizing ---
    function docHeight() {
        return Math.max(
            document.body.scrollHeight,
            document.documentElement.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.offsetHeight,
            document.body.clientHeight,
            document.documentElement.clientHeight
        );
    }

    function sendHeight() {
        var h = docHeight();
        if (h !== lastH) {
            lastH = h;
            // Send the new content height to the parent
            try { window.parent.postMessage({ frameHeight: h }, '*'); } catch (_) {}
        }
        ticking = false;
    }

    function ping() {
        if (!ticking) {
            ticking = true;
            (raf || setTimeout)(sendHeight, 0);
        }
    }

    // --- NEW LOGIC: Full Height Control ---

    /**
     * Notifies the parent to set the iframe height to 100vh
     * and stops the automatic height calculation.
     */
    function setIframeFullHeight() {
        // Clear the periodic pinging interval
        if (heightInterval) {
            clearInterval(heightInterval);
            heightInterval = null;
        }

        // Send message to parent
        try {
            window.parent.postMessage({ setFullHeight: true }, '*');
        } catch (_) {}

        // Optional: Scroll the iframe content to the top when modal opens
        window.scrollTo(0, 0);
    }

    /**
     * Notifies the parent to restore the height based on content
     * and restarts the automatic height calculation.
     */
    function setIframeNormalHeight() {
        // Send message to parent
        try {
            window.parent.postMessage({ setNormalHeight: true }, '*');
        } catch (_) {}

        // Restart the periodic pinging
        if (!heightInterval) {
            heightInterval = setInterval(ping, 1500);
        }
        ping(); // Ping immediately to restore size
    }


    // --- Event Listeners and Initial Setup ---

    // Parent se ping aaya (for initial or manual request)
    window.addEventListener('message', function (e) {
        if (e.data && typeof e.data === 'object' && e.data.requestHeight) ping();
    });

    // Initial pinging on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ping);
    } else {
        ping();
    }
    window.addEventListener('load', ping);
    window.addEventListener('resize', ping);

    // DOM changes observe (Mutations/Resizes)
    if ('MutationObserver' in window) {
        var mo = new MutationObserver(ping);
        mo.observe(document.body, { childList: true, subtree: true, attributes: true });
    }
    if ('ResizeObserver' in window) {
        var ro = new ResizeObserver(ping);
        ro.observe(document.body);
        ro.observe(document.documentElement);
    }

    // Periodic keep-alive
    heightInterval = setInterval(ping, 1500);

    // --- Modal/Popup Event Handling (You must customize these selectors) ---

    // 🔹 Popup open hone par (assuming a click opens the modal)
    document.addEventListener('click', function (e) {
        // Customize these selectors to match your modal open buttons
        if (e.target.closest('.buy-now-btn') || e.target.closest('.confirm-yes') || e.target.closest('.modal-open-selector')) {
            setIframeFullHeight();
        }
    });

    // 🔹 Popup close hone par (must be triggered by a close action)
    document.addEventListener('click', function (e) {
        // Customize these selectors to match your modal close buttons or backdrop
        if (e.target.closest('.modal-close') || e.target.closest('.confirm-no') || e.target.classList.contains('modal-backdrop') || e.target.closest('.modal-close-selector')) {
            // Use a slight delay to ensure the modal's DOM cleanup is complete
            setTimeout(setIframeNormalHeight, 50);
        }
    });

    // --- Original Scroll Notifications (Kept for compatibility) ---

    // 🔹 Step change hone par parent ko upar scroll karna
    function notifyStepChange() {
      try { window.parent.postMessage({ scrollTop: true }, '*'); } catch (_) {}
    }
    document.addEventListener('click', function (e) {
      if (e.target.closest('#nextBtn') || e.target.closest('#proceedBtn')) {
        notifyStepChange();
      }
    });

})();
