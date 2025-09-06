// =============================
// Global Variables
// =============================
let currentStep = 1;
const totalSteps = 6;
let isDrawing = false;
let signatureData = '';

// =============================
// Initialize Form
// =============================
document.addEventListener('DOMContentLoaded', function () {
    initializeSignature();
    setupEventListeners();
    setupFormValidation();
    updateProgressBar();

    // Toastr config (if available)
    if (window.toastr) {
        toastr.options = {
            closeButton: true,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 4000
        };
    }
});

// =============================
// Utilities: Styles & Notifications
// =============================
function applyInvalidStyle(el) {
    el.style.borderColor = '#ff6b6b';
    el.style.boxShadow = '0 0 10px rgba(255, 107, 107, 0.3)';
}
function applyValidStyle(el) {
    el.style.borderColor = '#93c120';
    el.style.boxShadow = '0 0 10px rgba(147, 193, 32, 0.2)';
}
function clearStyle(el) {
    el.style.borderColor = '';
    el.style.boxShadow = '';
}

function notifyError(message) {
    if (window.toastr) {
        toastr.error(message);
    } else {
        showError(message);
    }
}

// Prefer group label/legend/aria/data-label/placeholder/name/id
function getFieldLabel(field) {
    // Radios/checkbox groups
    if (field.type === 'radio' || field.type === 'checkbox') {
        const fs = field.closest('fieldset');
        if (fs && fs.querySelector('legend')) {
            return fs.querySelector('legend').textContent.trim();
        }
        const group = field.closest('.form-group, .form-row, .form-section');
        if (group) {
            const groupLegend = group.querySelector('legend');
            if (groupLegend) return groupLegend.textContent.trim();
            const groupLabel = group.querySelector('label:not([for])');
            if (groupLabel) return groupLabel.textContent.trim();
        }
    }

    // Direct label[for=id]
    if (field.id) {
        const label = document.querySelector(`label[for="${field.id}"]`);
        if (label) return label.textContent.trim();
    }

    // ARIA / data attributes
    const aria = field.getAttribute('aria-label');
    if (aria) return aria.trim();

    const dataLabel = field.getAttribute('data-label') || field.getAttribute('data-field-label');
    if (dataLabel) return dataLabel.trim();

    // Placeholder
    if (field.placeholder) return field.placeholder.trim();

    // Fallback: name or id, humanized
    const src = field.name || field.id || 'This field';
    return humanizeAttr(src);
}

function humanizeAttr(str) {
    return String(str)
        .replace(/[_-]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
        .replace(/\b\w/g, c => c.toUpperCase());
}

function getFieldIssue(field) {
    // Custom invalid class has priority
    if (field.classList && field.classList.contains('is-invalid')) {
        // Your custom message for phone, etc.
        return 'is invalid number.';
    }

    // Special case: radios -> evaluate as a group
    if (field.type === 'radio') {
        const group = document.querySelectorAll(`input[type="radio"][name="${field.name}"]`);
        const anyChecked = Array.from(group).some(r => r.checked);
        if (!anyChecked) return 'is required.';
    }

    const v = field.validity;
    if (v.valueMissing) return 'is required.';
    if (v.typeMismatch) return 'has an invalid format.';
    if (v.patternMismatch) return 'does not match the expected format.';
    if (v.tooShort) return `is too short (min ${field.minLength}).`;
    if (v.tooLong) return `is too long (max ${field.maxLength}).`;
    if (v.rangeUnderflow) return `is too low (min ${field.min}).`;
    if (v.rangeOverflow) return `is too high (max ${field.max}).`;
    if (v.stepMismatch) return 'is not on an allowed step.';
    if (v.badInput) return 'has an invalid value.';
    if (v.customError) return (field.validationMessage || 'is invalid.');
    return 'is invalid.';
}

// =============================
// Event Listeners (General)
// =============================
function setupEventListeners() {
    // Navigation buttons
    document.getElementById('nextBtn').addEventListener('click', nextStep);
    document.getElementById('prevBtn').addEventListener('click', prevStep);
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

    // Smooth animations
    addAnimations();
}

// =============================
// Conditional Field Displays
// =============================
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

// =============================
// Form Validation Wiring
// =============================
function setupFormValidation() {
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('blur', validateField);
        field.addEventListener('input', clearFieldError);
        if (field.type === 'radio') {
            // Radios validate on change
            field.addEventListener('change', validateField);
        }
    });
}

// Handle radio groups: one selected -> that one green, others neutral.
// None selected for required group -> first radio red, others neutral.
function validateRadioGroup(anyRadioInGroup) {
    const name = anyRadioInGroup.name;
    const group = Array.from(document.querySelectorAll(`input[type="radio"][name="${name}"]`));
    const anyChecked = group.some(r => r.checked);

    if (!anyChecked) {
        if (group[0]) applyInvalidStyle(group[0]);
        group.slice(1).forEach(clearStyle);
        return false;
    }

    group.forEach(r => {
        if (r.checked) applyValidStyle(r);
        else clearStyle(r);
    });
    return true;
}

// Field-level validation (blur/change)
function validateField(event) {
    const field = event.target;

    // Radios are handled as a group
    if (field.type === 'radio') {
        validateRadioGroup(field);
        return;
    }

    // If field has 'is-invalid', force red
    if (field.classList.contains('is-invalid')) {
        applyInvalidStyle(field);
        return;
    }

    // HTML5 validity
    const isValid = field.checkValidity();
    if (!isValid) applyInvalidStyle(field);
    else applyValidStyle(field);
}

function clearFieldError(event) {
    const field = event.target;
    if (field.type === 'radio') {
        const group = document.querySelectorAll(`input[type="radio"][name="${field.name}"]`);
        group.forEach(clearStyle);
        return;
    }
    clearStyle(field);
}

// =============================
// Navigation
// =============================
function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            currentStep++;
            updateStep();
        }
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStep();
    }
}

function goToStep(stepNumber) {
    currentStep = stepNumber;
    updateStep();
}

function updateStep() {
    // Hide all steps
    document.querySelectorAll('.step').forEach(step => {
        step.classList.remove('active');
    });

    // Show current step
    document.getElementById(`step${currentStep}`).classList.add('active');

    // Update progress
    updateProgressBar();

    // Update navigation buttons
    updateNavigationButtons();

    // Add animation
    document.getElementById(`step${currentStep}`).classList.add('slide-up');

    // Scroll to top
    document.querySelector('.form-container').scrollIntoView({
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
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    prevBtn.style.display = currentStep > 1 ? 'block' : 'none';

    if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'block';
    } else {
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
    }
}

// Validate current step (group-aware for radios) + named toasts
function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;
    const invalidFields = [];
    const seenRadioNames = new Set();

    requiredFields.forEach(field => {
        if (field.type === 'radio') {
            if (seenRadioNames.has(field.name)) return; // validate once per group
            seenRadioNames.add(field.name);

            const group = Array.from(currentStepElement.querySelectorAll(`input[type="radio"][name="${field.name}"]`));
            const anyChecked = group.some(r => r.checked);
            const groupHasCustomInvalid = group.some(r => r.classList.contains('is-invalid'));

            if (!anyChecked || groupHasCustomInvalid) {
                isValid = false;
                invalidFields.push(group[0] || field);
                validateRadioGroup(field); // styles: first red, rest neutral OR selected green
            } else {
                validateRadioGroup(group.find(r => r.checked) || field);
            }
            return;
        }

        // Non-radio fields
        const failsHtml5 = !field.checkValidity();
        const hasCustomInvalid = field.classList && field.classList.contains('is-invalid');

        if (failsHtml5 || hasCustomInvalid) {
            isValid = false;
            invalidFields.push(field);
            validateField({ target: field });
        }
    });

    // Special validation for signature on step 6
    if (currentStep === 6) {
        const hasSignature = signatureData !== '';
        const hasTypedName = document.getElementById('typedName').value.trim() !== '';
        if (!hasSignature && !hasTypedName) {
            isValid = false;
            notifyError('Signature / Typed Name is required.');
        }
    }

    if (!isValid) {
        if (invalidFields.length > 0) {
            const firstInvalid = invalidFields[0];
            const label = getFieldLabel(firstInvalid);
            const issue = getFieldIssue(firstInvalid);

            if (typeof firstInvalid.focus === 'function') firstInvalid.focus();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Example: "Email Address is required." or "Phone Number has an invalid format."
            notifyError(`${label} ${issue}`);
        }
    }

    return isValid;
}

function canNavigateToStep(stepNumber) {
    // Allow navigation to previous steps or next step if current is valid
    return stepNumber <= currentStep + 1 && (stepNumber <= currentStep || validateCurrentStep());
}

// =============================
// Signature functionality
// =============================
function initializeSignature() {
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');
    const typedNameField = document.getElementById('typedName');
    const clearBtn = document.getElementById('clearSignatureBtn');

    // Set canvas properties
    ctx.strokeStyle = '#93c120';
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
        const mouseEvent = new MouseEvent(
            e.type === 'touchstart' ? 'mousedown' :
            e.type === 'touchmove'  ? 'mousemove' : 'mouseup',
            { clientX: touch.clientX, clientY: touch.clientY }
        );
        canvas.dispatchEvent(mouseEvent);
    }

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function drawTypedName(name) {
        ctx.font = '24px "Poppins", sans-serif';
        ctx.fillStyle = '#93c120';
        ctx.textAlign = 'center';
        ctx.fillText(name, canvas.width / 2, canvas.height / 2 + 8);
        updateSignatureData();
    }

    function updateSignatureData() {
        signatureData = canvas.toDataURL();
        document.getElementById('signatureInput').value = signatureData;
    }
}

// =============================
// Form submission
// =============================
function handleSubmit(e) {
    e.preventDefault();

    if (!validateCurrentStep()) {
        return;
    }

    // Show loading
    showLoadingOverlay();
    $('#surveyForm').submit();
}

// =============================
// Utility functions
// =============================
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
    // Staggered animations to summary items
    const summaryItems = document.querySelectorAll('.summary-item');
    summaryItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });

    // Hover effects to form groups
    document.querySelectorAll('.form-group').forEach(group => {
        group.addEventListener('mouseenter', function () {
            // Intentionally left blank for future effects
        });

        group.addEventListener('mouseleave', function () {
            this.style.transform = 'translateX(0)';
        });
    });
}

// =============================
// Keyboard navigation
// =============================
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

// =============================
// Google address autocomplete
// =============================
function initAddressAutocomplete() {
    if (!(window.google && google.maps && google.maps.places)) return;

    const addressField = document.getElementById("homeAddress");
    if (!addressField) return;

    const ac = new google.maps.places.Autocomplete(addressField, {
        types: ["address"],
        componentRestrictions: { country: "gb" }
    });

    ac.setFields(["formatted_address", "address_components"]);

    ac.addListener("place_changed", function () {
        const place = ac.getPlace();
        if (!place || !place.formatted_address) return;

        // Clean trailing ", United Kingdom" or ", UK"
        addressField.value = place.formatted_address
            .replace(/,\s*United Kingdom$/i, "")
            .replace(/,\s*UK$/i, "");

        const pc = (place.address_components || []).find((c) => c.types.includes("postal_code"));
        if (pc) {
            const postcode = pc.long_name.toUpperCase();
            addressField.value = addressField.value.replace(`${postcode} `, "");
            document.getElementById("postalCode").value = postcode;
        }
    });
}

// =============================
// jQuery DOM Ready (intl-tel & address)
// =============================
$(document).ready(function () {
    initIntlTel('telephone_number');
    initIntlTel('agentPhone');
    initAddressAutocomplete();
});
