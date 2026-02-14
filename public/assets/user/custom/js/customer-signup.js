
// Global Variables
let currentStep = 2;
const totalSteps = 6;
let isDrawing = false;
let signatureData = '';

// Initialize Form
document.addEventListener('DOMContentLoaded', function () {
    initializeSignature();
    setupEventListeners();
    updateProgressBar();
});

// Event Listeners
function setupEventListeners() {
    // Navigation buttons
    document.getElementById('nextBtn').addEventListener('click', nextStep);
    document.getElementById('headerBackBtn').addEventListener('click', prevStep);
    document.getElementById('submitBtn').addEventListener('click', handleSubmit);

    // Progress steps click
    document.querySelectorAll('.progress-step').forEach(step => {
        step.addEventListener('click', function () {
            const stepNumber = parseInt(this.dataset.step);
            if (stepNumber < currentStep || canNavigateToStep(stepNumber)) {
                goToStep(stepNumber);
            }
        });
    });

    // Conditional field displays
    setupConditionalFields();

    // Form validation
    setupFormValidation();

    // Add smooth animations
    addAnimations();
}

// Setup conditional field displays
function setupConditionalFields() {
    // Garage location field
    document.querySelectorAll('input[name="inf_custom_Garage"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const garageLocationField = document.getElementById('garageLocationField');
            if (this.value === '1') {
                garageLocationField.style.display = 'block';
                garageLocationField.classList.add('fade-in');
            } else {
                garageLocationField.style.display = 'none';
                garageLocationField.classList.remove('fade-in');
            }
        });
    });

    // Garden location field
    document.querySelectorAll('input[name="inf_custom_Garden"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const gardenLocationField = document.getElementById('gardenLocationField');
            if (this.value === '1') {
                gardenLocationField.style.display = 'block';
                gardenLocationField.classList.add('fade-in');
            } else {
                gardenLocationField.style.display = 'none';
                gardenLocationField.classList.remove('fade-in');
            }
        });
    });

    // Solicitor fields
    document.querySelectorAll('input[name="inf_custom_SolicitorFirm"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const solicitorFields = document.getElementById('solicitorFields');
            const requiredFields = solicitorFields.querySelectorAll('input[required]');

            if (this.value === 'yes') {
                solicitorFields.style.display = 'block';
                solicitorFields.classList.add('fade-in');
                requiredFields.forEach(field => field.required = true);
            } else {
                solicitorFields.style.display = 'none';
                solicitorFields.classList.remove('fade-in');
                requiredFields.forEach(field => field.required = false);
            }
        });
    });

    // Exchange date field
    document.querySelectorAll('input[name="inf_custom_exchange_known"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const exchangeDateField = document.getElementById('exchangeDateField');
            const exchangeDateInput = document.getElementById('exchangeDate');

            if (this.value === 'yes') {
                exchangeDateField.style.display = 'block';
                exchangeDateField.classList.add('fade-in');
                exchangeDateInput.required = true;
            } else {
                exchangeDateField.style.display = 'none';
                exchangeDateField.classList.remove('fade-in');
                exchangeDateInput.required = false;
            }
        });
    });
}

// Form validation
function setupFormValidation() {
    // Real-time validation
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('blur', validateField);
        field.addEventListener('input', clearFieldError);
    });
}

function validateField(event) {
    const field = event.target;
    const isValid = field.checkValidity();

    if (!isValid) {
        field.style.borderColor = '#ff6b6b';
        // field.style.boxShadow = '0 0 10px rgba(255, 107, 107, 0.3)';
    } else {
        // field.style.borderColor = '#C1EC4A';
        // field.style.boxShadow = '0 0 10px rgba(147, 193, 32, 0.2)';
    }
}

function clearFieldError(event) {
    const field = event.target;
    field.style.borderColor = '';
    field.style.boxShadow = '';
}

// Navigation functions
function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            currentStep++;
            updateStep();
        }
    }
}

function prevStep() {

    if (currentStep > 2) {
        currentStep--;
        updateStep();
    }
    if (currentStep == 2) {
        showLanding();
    }
}

function goToStep(stepNumber) {
    currentStep = stepNumber;
    updateStep();
}

function updateStep() {
    // Hide all steps
    $('.step').css('display', 'none').removeClass('active');

    // Show current step
    const $currentStep = $('#step' + currentStep);
    $currentStep.show().addClass('active');

    // Update progress
    updateProgressBar();

    // Update navigation buttons
    updateNavigationButtons();

    // Add animation for the step transition
    $currentStep.addClass('slide-up');

    // Scroll to top of the form container
    $('.form-container').get(0).scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

function updateProgressBar() {
    const progressBar = document.querySelector('.progress-bar');
    const steps = document.querySelectorAll('.progress-step');

    progressBar.setAttribute('data-progress', currentStep);

    steps.forEach((step, index) => {
        step.classList.remove('active', 'completed');

        if (index + 1 === currentStep) {
            step.classList.add('active');
        } else if (index + 1 < currentStep) {
            step.classList.add('completed');
        }
    });
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('headerBackBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    // Previous button
    prevBtn.style.display = currentStep > 1 ? 'block' : 'none';

    // Next/Submit button
    if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'block';
    } else {
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
    }
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;

    // Prevent duplicate toasts for the same radio group
    const processedRadioNames = new Set();

    requiredFields.forEach(field => {
        // Skip duplicate radios by name
        if (field.type === 'radio') {
            if (processedRadioNames.has(field.name)) return;
            processedRadioNames.add(field.name);
        }

        // Native validity UI styling you already have
        if (!field.checkValidity()) {
            isValid = false;
            validateField({ target: field });
        }

        // Toastr for empty required fields (label text without *)
        if (isEmptyRequiredField(field)) {
            isValid = false;
            toastRequired(field);
        }
    });

    // Special validation for signature on step 6
    if (currentStep === 6) {
        const hasSignature = signatureData !== '';
        const hasTypedName = document.getElementById('typedName').value.trim() !== '';

        if (!hasSignature && !hasTypedName) {
            isValid = false;
            showError('Please provide either a signature or type your name.');
            if (window.toastr) toastr.error('Signature or typed name is required.');
        }
    }

    if (!isValid) {
        showError('Please fill in all required fields before continuing.');
    }

    return isValid;
}

function canNavigateToStep(stepNumber) {
    // Allow navigation to previous steps or next step if current is valid
    return stepNumber <= currentStep + 1 && (stepNumber <= currentStep || validateCurrentStep());
}

// Signature functionality
function initializeSignature() {
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');
    const typedNameField = document.getElementById('typedName');
    const clearBtn = document.getElementById('clearSignatureBtn');

    // Set canvas properties
    ctx.strokeStyle = '#1A202C';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    // Mouse events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // Touch events
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);

    // Typed name events
    typedNameField.addEventListener('input', function () {
        if (this.value.trim() !== '') {
            clearCanvas();
            drawTypedName(this.value);
        }
    });

    // Clear button
    clearBtn.addEventListener('click', function () {
        clearCanvas();
        typedNameField.value = '';
        signatureData = '';
        document.getElementById('signatureInput').value = '';
    });

    function startDrawing(e) {
        isDrawing = true;
        const rect = canvas.getBoundingClientRect();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }

    function draw(e) {
        if (!isDrawing) return;
        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
        updateSignatureData();
    }

    function stopDrawing() {
        if (isDrawing) {
            isDrawing = false;
            updateSignatureData();
        }
    }

    function handleTouch(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' :
            e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function drawTypedName(name) {
        ctx.font = '24px "Poppins", sans-serif';
        ctx.fillStyle = '#1A202C';
        ctx.textAlign = 'center';
        ctx.fillText(name, canvas.width / 2, canvas.height / 2 + 8);
        updateSignatureData();
    }

    function updateSignatureData() {
        signatureData = canvas.toDataURL();
        document.getElementById('signatureInput').value = signatureData;
    }
}

// Form submission
function handleSubmit(e) {
    e.preventDefault();

    if (!validateCurrentStep()) {
        return;
    }

    // Show loading
    showLoadingOverlay();
    $('#surveyForm').submit();
}

// Utility functions
function showError(message) {
    // Create or update error message
    let errorDiv = document.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = `
            background: rgba(255, 107, 107, 0.1);
            border: 2px solid rgba(255, 107, 107, 0.3);
            color: #ff6b6b;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            font-weight: 500;
        `;
        document.querySelector(`#step${currentStep}`).appendChild(errorDiv);
    }

    errorDiv.textContent = message;
    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Remove error after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.parentNode.removeChild(errorDiv);
        }
    }, 5000);
}

function showLoadingOverlay() {
    document.getElementById('pageSplashLoader').style.display = 'flex';
}

function hideLoadingOverlay() {
    document.getElementById('pageSplashLoader').style.display = 'none';
}

function addAnimations() {
    // Add staggered animations to summary items
    const summaryItems = document.querySelectorAll('.summary-item');
    summaryItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });

    // Add hover effects to form groups
    document.querySelectorAll('.form-group').forEach(group => {
        group.addEventListener('mouseenter', function () {

        });

        group.addEventListener('mouseleave', function () {
            this.style.transform = 'translateX(0)';
        });
    });
}

// Keyboard navigation
document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.ctrlKey) {
        if (currentStep < totalSteps) {
            nextStep();
        } else {
            handleSubmit(e);
        }
    } else if (e.key === 'ArrowLeft' && e.ctrlKey) {
        prevStep();
    } else if (e.key === 'ArrowRight' && e.ctrlKey) {
        nextStep();
    }
});


$(document).ready(function () {
    initIntlTel('solicitorPhone');
    initIntlTel('telephone_number');
    initIntlTel('agentPhone');
    initAddressAutocomplete();
});


// === Toastr + validation helpers ===
(function initToastr() {
    if (!window.toastr) return;
    toastr.options = Object.assign(
        {
            closeButton: true,
            newestOnTop: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 4000,
            preventDuplicates: true
        },
        toastr.options || {}
    );
})();

function escSel(s) {
    return (window.CSS && CSS.escape) ? CSS.escape(s) : String(s).replace(/([^\w-])/g, "\\$1");
}

function prettifyLabelText(text) {
    return (text || "")
        .replace(/\*/g, "")        // remove any asterisks
        .replace(/[:：]\s*$/, "")  // remove trailing colon
        .trim();
}

function getFieldLabel(field) {
    if (!field) return "";

    // <label for="id">
    if (field.id) {
        const lbl = document.querySelector(`label[for="${escSel(field.id)}"]`);
        if (lbl) return prettifyLabelText(lbl.textContent);
    }

    // Wrapped by a <label>
    const wrapping = field.closest("label");
    if (wrapping) return prettifyLabelText(wrapping.textContent);

    // Sibling/ancestor label in group
    const group = field.closest(".form-group") || field.parentElement;
    if (group) {
        const lbl = group.querySelector("label");
        if (lbl) return prettifyLabelText(lbl.textContent);
    }

    // Fallbacks
    return prettifyLabelText(
        field.getAttribute("aria-label") || field.placeholder || field.name || "This field"
    );
}

function isEmptyRequiredField(field) {
    if (!field || !field.required) return false;
    const tag = field.tagName.toUpperCase();
    const type = (field.type || "").toLowerCase();

    if (type === "radio") {
        const group = document.querySelectorAll(`input[type="radio"][name="${escSel(field.name)}"]`);
        return !Array.from(group).some(r => r.checked);
    }
    if (type === "checkbox") {
        return !field.checked;
    }
    if (tag === "SELECT") {
        return (field.value || "") === "";
    }
    return (field.value || "").trim() === "";
}

function toastRequired(field) {
    if (!window.toastr) return;
    const label = getFieldLabel(field);
    toastr.error(`${label} is required.`);
}


// Solicitor fields
document.querySelectorAll('input[name="inf_custom_SolicitorFirm"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const solicitorFields = document.getElementById('solicitorFields');

        // Select the fields that should become required when "Yes"
        const mustRequire = [
            document.getElementById('solicitorFirmName'),
            document.getElementById('conveyancerName'),
            document.getElementById('solicitorPhone'),
            document.getElementById('solicitorEmail')
        ];

        if (this.value === 'yes') {
            solicitorFields.style.display = 'block';
            solicitorFields.classList.add('fade-in');
            mustRequire.forEach(field => field.setAttribute('required', 'required'));
        } else {
            solicitorFields.style.display = 'none';
            solicitorFields.classList.remove('fade-in');
            mustRequire.forEach(field => field.removeAttribute('required'));
        }
    });
});







/* ---------- Google Places (apply to all address fields) ---------- */
function initAddressAutocomplete() {
    if (!(window.google && google.maps && google.maps.places)) return;

    const pairs = [
        { addr: "homeAddress", pc: "postalCode" },
        { addr: "surveyAddress", pc: "surveyPostalCode" },
        { addr: "agentAddress", pc: "agentPostalCode" },
        { addr: "solicitorAddress", pc: "solicitorPostalCode" }
    ];

    const geocoder = new google.maps.Geocoder();

    // Track suppression per address field so we don't clear postcode during a valid selection.
    const suppressClear = new Map();
    const lastBiasPostcode = new Map();
    const biasTimers = new Map();

    const clearBiasTimer = (addressField) => {
        const t = biasTimers.get(addressField);
        if (t) clearTimeout(t);
        biasTimers.delete(addressField);
    };

    const scheduleBiasFromPostcode = (ac, addressField, postcodeCandidate) => {
        clearBiasTimer(addressField);
        biasTimers.set(addressField, setTimeout(() => {
            const pc = extractUKPostcode(postcodeCandidate);
            if (!pc) return;

            const pcUpper = pc.toUpperCase();
            if (pcUpper === (lastBiasPostcode.get(addressField) || "")) return;
            lastBiasPostcode.set(addressField, pcUpper);

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
        }, 250));
    };

    const attachAutocomplete = (addressId, postcodeId) => {
        const addressField = document.getElementById(addressId);
        const pcField = postcodeId ? document.getElementById(postcodeId) : null;
        if (!addressField) return;

        suppressClear.set(addressField, false);
        lastBiasPostcode.set(addressField, "");

        const clearPC = () => { if (pcField) pcField.value = ""; };

        // Use geocode rather than address for better postcode behavior
        const ac = new google.maps.places.Autocomplete(addressField, {
            types: ["geocode"],
            componentRestrictions: { country: "gb" }
        });
        if (ac.setFields) ac.setFields(["formatted_address", "address_components", "geometry"]);

        // Clear postcode when user edits address manually (unless suppressed) + bias suggestions by postcode.
        addressField.addEventListener("input", () => {
            if (!suppressClear.get(addressField)) clearPC();

            const pcFromField = pcField ? String(pcField.value || "").trim() : "";
            const pcFromAddress = String(addressField.value || "").trim();
            scheduleBiasFromPostcode(ac, addressField, pcFromField || pcFromAddress);
        });

        // If they type a postcode in the dedicated postcode field, also bias the dropdown for address.
        if (pcField) {
            pcField.addEventListener("input", () => {
                scheduleBiasFromPostcode(ac, addressField, String(pcField.value || "").trim());
            });
        }

        ac.addListener("place_changed", function () {
            suppressClear.set(addressField, true);

            const place = ac.getPlace();
            if (!place || !place.formatted_address) {
                clearPC();
                setTimeout(() => suppressClear.set(addressField, false), 0);
                return;
            }

            // Remove trailing UK
            let formatted = place.formatted_address
                .replace(/,\s*United Kingdom$/i, "")
                .replace(/,\s*UK$/i, "");

            const comps = place.address_components || [];
            const pcComp = comps.find(c => c.types && c.types.includes("postal_code"));
            const subpremiseComp = comps.find(c => c.types && c.types.includes("subpremise"));

            // Ensure flat/unit (subpremise) is included in the SAME address field if available.
            // Google sometimes returns subpremise in address_components but not in formatted_address.
            if (subpremiseComp && subpremiseComp.long_name) {
                const flatVal = String(subpremiseComp.long_name).trim();
                if (flatVal) {
                    const flatRegex = new RegExp(`(^|,\\s*)${escapeRegExp(flatVal)}(,|\\s|$)`, "i");
                    if (!flatRegex.test(formatted)) {
                        formatted = `${flatVal}, ${formatted}`;
                    }
                }
            }

            if (pcComp && pcComp.long_name) {
                const postcode = normalizeUKPostcode(pcComp.long_name);

                // Remove postcode from address text (keep it in postcode field)
                formatted = formatted
                    .replace(new RegExp(`\\s*${escapeRegExp(postcode)}\\s*,?\\s*$`, "i"), "")
                    .replace(new RegExp(`,\\s*${escapeRegExp(postcode)}(,|\\s|$)`, "i"), "$1")
                    .trim();

                if (pcField) pcField.value = postcode;
            } else {
                clearPC();
            }

            addressField.value = formatted;

            setTimeout(() => suppressClear.set(addressField, false), 0);
        });
    };

    pairs.forEach(({ addr, pc }) => attachAutocomplete(addr, pc));

    function extractUKPostcode(str) {
        const s = String(str || "");
        // Find a postcode anywhere in the string (handles "N1 5QL", "n15ql", etc.)
        const m = s.match(/\b([A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2})\b/i);
        return m ? normalizeUKPostcode(m[1]) : "";
    }

    function normalizeUKPostcode(pc) {
        pc = String(pc || "").toUpperCase().replace(/\s+/g, "");
        // Insert a space before last 3 chars
        return pc.length > 3 ? pc.slice(0, -3) + " " + pc.slice(-3) : pc;
    }

    function escapeRegExp(s) {
        return String(s || "").replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    }
}

