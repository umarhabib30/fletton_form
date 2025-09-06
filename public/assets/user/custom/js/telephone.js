let iti;
function initIntlTel(id) {
    const input = document.querySelector("#" + id);
    if (!input || !window.intlTelInput) return;

    iti = window.intlTelInput(input, {
        // Make sure this URL is reachable on your page
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        initialCountry: "GB",
        preferredCountries: ["GB", "US", "CA", "AU"],
        nationalMode: false, // keep +CC in the field
        autoPlaceholder: "polite", // show country-specific placeholder
        // GeoIP (optional)
        geoIpLookup: function (cb) {
            fetch("https://ipapi.co/json")
                .then(r => r.json())
                .then(d => cb(d && d.country_code ? d.country_code : "GB"))
                .catch(() => cb("GB"));
        },
    });

    const dial = () => `+${iti.getSelectedCountryData().dialCode}`;
    const iso2 = () => (iti.getSelectedCountryData().iso2 || "gb").toUpperCase();

    function ensurePrefix() {
        const code = dial();
        const v = input.value.trim();
        if (!v || !v.startsWith('+')) input.value = code + ' ';
        else if (!v.startsWith(code)) input.value = code + ' ' + v.replace(/^\+\d+\s*/, '');
    }
    function applyMasking() {
        const originalCursorPos = input.selectionStart;
        const originalValue = input.value;
        ensurePrefix();
        const prefix = dial();
        let nationalPart = '';
        if (input.value.startsWith(prefix)) {
            nationalPart = input.value.substring(prefix.length).replace(/\D/g, '');
        } else {
            nationalPart = input.value.replace(/\D/g, '');
        }

        // 3. Restrict length based on the country's placeholder
        const placeholder = input.placeholder || '';
        const maxNationalLen = (placeholder.substring(prefix.length).replace(/\D/g, '')).length;
        if (maxNationalLen > 0 && nationalPart.length > maxNationalLen) {
            nationalPart = nationalPart.substring(0, maxNationalLen);
        }

        // 4. Re-format the number using the library's utils
        const numberToFormat = prefix + nationalPart;
        let formattedValue = numberToFormat; // Default to sanitized value if formatting fails
        try {
            const formatted = window.intlTelInputUtils.formatNumber(
                numberToFormat,
                iso2(),
                window.intlTelInputUtils.numberFormat.INTERNATIONAL
            );
            if (formatted) {
                formattedValue = formatted;
            }
        } catch (_) {
            // Ignore formatting errors during typing
        }

        // 5. If the value changed, update the input and recalculate cursor position
        if (input.value === formattedValue) return;

        // Count digits before the original cursor to find its new logical position
        const digitsBeforeCursor = (originalValue.substring(0, originalCursorPos).replace(/\D/g, '')).length;

        input.value = formattedValue;

        let newCursorPos = 0;
        let digitsCounted = 0;
        for (const char of formattedValue) {
            newCursorPos++;
            if (/\d/.test(char)) {
                digitsCounted++;
            }
            // Stop once we've passed the same number of digits
            if (digitsCounted >= digitsBeforeCursor) {
                break;
            }
        }

        // Handle case where user deletes everything back to the prefix
        if (nationalPart.length === 0) {
            newCursorPos = formattedValue.length;
        }

        input.setSelectionRange(newCursorPos, newCursorPos);
    }

    // Wait until utils.js is loaded before wiring formatting logic
    (iti.promise || Promise.resolve()).then(() => {
        // First paint
        ensurePrefix();

        input.addEventListener('focus', () => {
            ensurePrefix();
            // put caret at end
            setTimeout(() => input.setSelectionRange(input.value.length, input.value.length), 0);
        });

        input.addEventListener('input', applyMasking);
        input.addEventListener('countrychange', () => {
            ensurePrefix();
            applyMasking();
        });
        input.addEventListener('blur', () => {

            applyMasking();
            const ok = (() => {
                try {
                    return iti.isValidNumber();
                } catch {
                    return true;
                }
            })();
            input.classList.toggle('is-invalid', !ok);
            input.dataset.phoneValid = ok ? '1' : '0';
        });
    });
}
