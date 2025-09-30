
/* ---------- Google Places (optional) ---------- */
function initAddressAutocomplete() {
    if (!(window.google && google.maps && google.maps.places)) return;

    const addressField = document.getElementById("full_address");
    const postcodeEl   = document.getElementById("postcode");
    if (!addressField || !postcodeEl) return;

    // If the user types/edits the address manually, clear any stale postcode.
    // We'll temporarily suppress this when a valid Google "place_changed" fires.
    let suppressPostcodeClear = false;
    const clearPostcode = () => { if (postcodeEl) postcodeEl.value = ""; };

    addressField.addEventListener("input", function () {
        if (!suppressPostcodeClear) clearPostcode();
    });

    const ac = new google.maps.places.Autocomplete(addressField, {
        types: ["address"],
        componentRestrictions: { country: "gb" }
    });

    // For newer Maps JS versions, fields are picked via AutocompleteOptions or setOptions.
    // Keep this for older versions:
    if (ac.setFields) ac.setFields(["formatted_address", "address_components"]);

    ac.addListener("place_changed", function () {
        // Prevent our input handler from clearing immediately as Google sets the value.
        suppressPostcodeClear = true;

        const place = ac.getPlace();
        if (!place || !place.formatted_address) {
            clearPostcode(); // no valid selection -> ensure postcode is blank
            suppressPostcodeClear = false;
            return;
        }

        // Clean trailing ", United Kingdom" or ", UK"
        addressField.value = place.formatted_address
            .replace(/,\s*United Kingdom$/i, "")
            .replace(/,\s*UK$/i, "");

        const pc = (place.address_components || []).find((c) => c.types.includes("postal_code"));

        if (pc && pc.long_name) {
            const postcode = pc.long_name.toUpperCase();

            // Remove postcode from the address line if Google appended it there
            // (common in formatted_address), then set the dedicated field.
            addressField.value = addressField.value
                .replace(new RegExp(`\\s*${postcode}\\s*,?\\s*$`, "i"), "")
                .replace(new RegExp(`,\\s*${postcode}(,|\\s|$)`, "i"), "$1")
                .trim();

            postcodeEl.value = postcode;
        } else {
            // User selected a place without a postal code -> keep postcode empty.
            clearPostcode();
        }

        // Re-enable clearing for any subsequent manual edits.
        // SetTimeout lets Google finish any final programmatic value updates.
        setTimeout(() => { suppressPostcodeClear = false; }, 0);
    });
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
window.toggleSqftAreaBox = toggleSqftAreaBox; // keep for inline handler compatibility

/* ---------- Phone validation paint ---------- */
function paintPhoneValidity(el, isValid) {
    if (!el) return;
    el.style.borderColor = isValid ? "" : "#dc3545";
}

/* ---------- DOM Ready ---------- */
$(document).ready(function () {
    initIntlTel('telephone_number');
    initAddressAutocomplete();

    // Real-time phone validation paint
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

// form validations and submit
$(function () {
    toastr.options = {
        positionClass: "toast-top-right",
        timeOut: 3500,
        closeButton: true,
        progressBar: true,
        newestOnTop: true,
    };

    $('#proceedBtn').on('click', function (e) {
        e.preventDefault();
        //    make alert for all required fields
        for (const field of document.querySelectorAll('#quoteForm [required]')) {
            // Check if the field is a select element and its value is empty
            if (field.tagName === 'SELECT' && field.value === "") {
                toastr.error('Please select an option for ' + field.attributes['placeholder']
                    .value);
                field.focus();
                return;
            }

            // Continue with the original check for other input types
            if (field.tagName !== 'SELECT' && !field.value) {
                toastr.error('Please fill ' + field.placeholder);
                field.focus();
                return;
            }

            if(! $('#postcode').val()){
                toastr.error('Plese select a valid address from the dropdown');
                return;
            }

            if ($('#telephone_number').hasClass('is-invalid')) {
                toastr.error('Please enter a valid phone number');
                $('#telephone_number').focus();
                return;
            }

            if ($('#market_value').val() < 100000 || $('#market_value').val() > 6000000) {
                toastr.error('Market Value must be between £100,000 and £6,000,000');
                $('#market_value').focus();
                return;
            }

            if ($('#agree_terms').is(':checked') === false) {
                toastr.error('You must agree to the terms');
                $('#agree_terms').focus();
                return;
            }
        }
        $('#overlay').show();
        // submit the form
        $('#quoteForm').trigger('submit');
    });
});
