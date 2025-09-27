
(function() {
    function sendHeight() {
        var h = Math.max(
            document.body.scrollHeight,
            document.documentElement.scrollHeight
        );

        try {
            window.parent.postMessage({ frameHeight: h }, '*');
        } catch(e) {}
    }

    document.addEventListener('DOMContentLoaded', sendHeight);
    window.addEventListener('load', sendHeight);
    window.addEventListener('resize', sendHeight);

    // Form steps ke liye
    if (window.MutationObserver) {
        var observer = new MutationObserver(sendHeight);
        observer.observe(document.body, {
            childList: true, subtree: true, attributes: true
        });
    }

    setInterval(sendHeight, 2000);
})();

