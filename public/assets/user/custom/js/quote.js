/* ---------- Google Places (optional) ---------- */
function initAddressAutocomplete() {
  if (!(window.google && google.maps && google.maps.places)) return;

  const addressField = document.getElementById("full_address");
  const postcodeEl = document.getElementById("postcode");
  if (!addressField || !postcodeEl) return;

  let suppressPostcodeClear = false;
  const clearPostcode = () => { postcodeEl.value = ""; };

  const geocoder = new google.maps.Geocoder();
  let lastBiasPostcode = "";
  let biasTimer = null;

  // Use geocode rather than address for better postcode behavior
  const ac = new google.maps.places.Autocomplete(addressField, {
    types: ["geocode"],
    componentRestrictions: { country: "gb" }
  });

  if (ac.setFields) ac.setFields(["formatted_address", "address_components", "geometry"]);

  // Clear postcode when user edits manually (unless suppressed)
  addressField.addEventListener("input", function () {
    if (!suppressPostcodeClear) clearPostcode();

    const v = addressField.value.trim();

    // Debounce biasing so we don't hammer Geocoder
    clearTimeout(biasTimer);
    biasTimer = setTimeout(() => {
      const pc = extractUKPostcode(v);
      if (!pc) return;

      // Don’t re-bias for same postcode repeatedly
      if (pc.toUpperCase() === lastBiasPostcode) return;
      lastBiasPostcode = pc.toUpperCase();

      geocoder.geocode(
        { address: pc, componentRestrictions: { country: "GB" } },
        (results, status) => {
          if (status !== "OK" || !results || !results[0]) return;
          const r = results[0];
          if (r.geometry && r.geometry.viewport) {
            ac.setBounds(r.geometry.viewport);
            ac.setOptions({ strictBounds: false });
          }
        }
      );
    }, 250);
  });

  ac.addListener("place_changed", function () {
    suppressPostcodeClear = true;

    const place = ac.getPlace();
    if (!place || !place.formatted_address) {
      clearPostcode();
      suppressPostcodeClear = false;
      return;
    }

    // Remove trailing UK
    addressField.value = place.formatted_address
      .replace(/,\s*United Kingdom$/i, "")
      .replace(/,\s*UK$/i, "");

    const pc = (place.address_components || []).find(c => c.types.includes("postal_code"));

    if (pc && pc.long_name) {
      const postcode = pc.long_name.toUpperCase();

      // Remove postcode from address text (keep it in postcode field)
      addressField.value = addressField.value
        .replace(new RegExp(`\\s*${escapeRegExp(postcode)}\\s*,?\\s*$`, "i"), "")
        .replace(new RegExp(`,\\s*${escapeRegExp(postcode)}(,|\\s|$)`, "i"), "$1")
        .trim();

      postcodeEl.value = postcode;
    } else {
      // If they selected something without a postcode, don't force-clear if you don't want to
      clearPostcode();
    }

    setTimeout(() => { suppressPostcodeClear = false; }, 0);
  });

  function extractUKPostcode(str) {
    // Find a postcode anywhere in the string (handles "N1 5QL", "n15ql", etc.)
    const m = str.match(/\b([A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2})\b/i);
    return m ? normalizeUKPostcode(m[1]) : "";
  }

  function normalizeUKPostcode(pc) {
    pc = pc.toUpperCase().replace(/\s+/g, "");
    // Insert a space before last 3 chars
    return pc.length > 3 ? pc.slice(0, -3) + " " + pc.slice(-3) : pc;
  }

  function escapeRegExp(s) {
    return s.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
  }
}

/* ---------- UI: sqft toggle ---------- */
function toggleSqftAreaBox() {
    const box = document.getElementById("sqftPriceBox");
    const over1650 = document.getElementById("over1650");
    if (!box || !over1650) return;

    box.style.display = over1650.checked ? "block" : "none";

    if (over1650.checked) {
        setTimeout(function () {
            const target = document.querySelector(".footer-text");
            if (target) target.scrollIntoView({ behavior: "smooth", block: "center" });
        }, 300);
    }
}
window.toggleSqftAreaBox = toggleSqftAreaBox;

/* ---------- Phone validation paint ---------- */
function paintPhoneValidity(el, isValid) {
    if (!el) return;
    el.style.borderColor = isValid ? "" : "#dc3545";
}

/* ---------- Helper: show inline errors ---------- */
function showFieldError(field, message) {
    const $field = $(field);
    $field.addClass('error');
    $field.next('.field-error').remove();
    $field.after('<span class="field-error">' + message + '</span>');
    $field[0].scrollIntoView({ behavior: "smooth", block: "center" });
    $field.focus();
}

function clearFieldErrors() {
    $('.field-error').remove();
    $('input, select').removeClass('error');
}

/* ---------- DOM Ready ---------- */
$(document).ready(function () {
    initIntlTel('telephone_number');
    initAddressAutocomplete();

    const tel = document.getElementById("telephone_number");
    if (tel) {
        tel.addEventListener("keyup", function () {
            if (iti) paintPhoneValidity(tel, iti.isValidNumber());
        });
        tel.addEventListener("countrychange", function () {
            if (iti) paintPhoneValidity(tel, iti.isValidNumber());
        });
    }
});

/* ---------- Toastr config ---------- */
toastr.options = {
    positionClass: "toast-top-center",
    timeOut: 3500,
    closeButton: true,
    progressBar: true,
    newestOnTop: true,
};

/* ---------- Form validation & submit ---------- */
$(function () {
    $('#proceedBtn').on('click', function (e) {
        e.preventDefault();
        clearFieldErrors(); // remove old errors

        // Loop through required fields
        for (const field of document.querySelectorAll('#quoteForm [required]')) {

            // Selects
            if (field.tagName === 'SELECT' && field.value === "") {
                showFieldError(field, 'Select an option for ' + field.attributes['placeholder'].value);
                return;
            }

            // Inputs
            if (field.tagName !== 'SELECT' && !field.value) {
                showFieldError(field, ' Enter ' + field.placeholder);
                return;
            }
        }

        // Address check
        // if (!$('#postcode').val()) {
        //     showFieldError($('#full_address'), 'Select a valid address from the dropdown');
        //     return;
        // }

        // Phone validation
        if ($('#telephone_number').hasClass('is-invalid')) {
            showFieldError($('#telephone_number'), 'Enter a valid phone number');
            return;
        }

        // Market value
        const marketVal = parseInt($('#market_value').val(), 10);
        if (marketVal < 100000 || marketVal > 6000000) {
            showFieldError($('#market_value'), 'Market Value must be between £100,000 and £6,000,000');
            return;
        }

        // Terms
        if ($('#agree_terms').is(':checked') === false) {
            showFieldError($('#agree_terms'), 'You must agree to the terms');
            return;
        }

        // Sqft validation
        if ($('#over1650').is(':checked')) {
            const sqftVal = parseInt($('#sqft_area').val(), 10);
            if (sqftVal > 5000) {
                showFieldError($('#sqft_area'), 'Floor area cannot exceed 5000 sqft');
                return;
            }
        }

        // If everything is valid
        $('#overlay').show();
        $('#quoteForm').trigger('submit');
    });
});
