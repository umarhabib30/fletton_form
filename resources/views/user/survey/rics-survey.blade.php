<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RICS Survey Instruction Form</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('assets/user/custom/css/customer-signup.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />


    <!-- Google Places JS (put your real API key) -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7xLp13hLBGIDOt4BIJZrJF99ItTsya0g&libraries=places&callback=initAddressAutocomplete"
        defer></script>
    <style>
        /* ---------- tiny page-specific tweaks ---------- */
        .header-back-btn {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary);
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-back-btn:hover {
            color: #7da817;
        }

        .form-header {
            position: relative;
        }

        /* Start in landing mode to avoid flash (FOUC) */
        body.is-landing .form-header {
            display: none !important;
        }

        body.is-landing .progress-container {
            display: none !important;
        }

        body.is-landing .form-navigation {
            display: none !important;
        }

        body.is-landing .form-content {
            padding: 0 !important;
        }

        body:not(.is-landing) .form-content {
            padding: 36px;
        }

        /* footer bar color (as per your ink background) */
        body:not(.is-landing) .form-navigation {
            padding: 20px;
            background: #1A202C;
            border-top: 1px solid var(--border);
        }

        /* Footer Previous is always hidden */
        #prevBtn {
            display: none !important;
        }
    </style>
</head>

<!-- IMPORTANT: render first paint in landing mode -->

<body class="is-landing">
    <div class="form-container">

        <!-- Header (hidden on landing, shown in wizard) -->
        <div class="form-header">
            <button type="button" id="headerBackBtn" class="header-back-btn" style="display:none;">
                <i class="fa-solid fa-angle-left"></i>
            </button>
            <div class="form-title">Instruction Request for RICS Survey</div>
            <div class="form-subtitle">Managed by Flettons Group</div>
        </div>

        {{-- ====== Always-visible Step-0 (Booking Summary) ====== --}}
        @php
        // 1) Resolve numeric level (e.g. "Level 3 +" -> 3)
        $levelRaw = $survey->level ?? $survey->survey_level ?? ($survey->level_selected ?? '');
        $numLevel = (int) preg_replace('/\D+/', '', (string) $levelRaw);

        // 2) Add-on flags
        $hasAerial = !empty($survey->aerial);
        $hasInsurance = !empty($survey->insurance);
        $hasBreakdown = !empty($survey->breakdown);
        $hasAddon = $hasAerial || $hasInsurance || $hasBreakdown;
        $hasAllAddons = $hasAerial && $hasInsurance && $hasBreakdown;

        // 3) Defaults + label rules
        $displayLabel = '—';
        $showAddonsBlock = false;
        $forceAllAddons = false;
        $isRoofReport = false;

        switch ($numLevel) {
        case 1:
        $displayLabel = 'Roof Report';
        $isRoofReport = true; // show drone icon (below)
        break;

        case 2:
        $displayLabel = 'Level 2';
        break;

        case 3:
        // If ALL 3 add-ons chosen, show "Level 3+"
        $displayLabel = $hasAllAddons ? 'Level 3+' : 'Level 3';
        $showAddonsBlock = true; // show selected or "No add-ons selected"
        break;

        case 4:
        // Backend Level 4 = UI "Level 3+" and always show ALL add-ons
        $displayLabel = 'Level 3+';
        $showAddonsBlock = true;
        $forceAllAddons = true;
        break;

        default:
        if ($numLevel > 0) $displayLabel = 'Level ' . $numLevel;
        }

        // 4) Icon: Roof Report -> Drone; otherwise Survey icon
        $surveyIcon = $isRoofReport
        ? asset('assets/user/icons/Drone.png') // update path if needed
        : asset('assets/user/icons/Survey type.png');
        @endphp

        <div id="bookingSummarySticky" class="booking-summary-sticky">
            <div class="summary-head">
                <h4 class="summary-head__title">Booking Summary</h4>
                <p class="summary-head__text">
                    Press proceed to confirm your chosen survey level and property details&nbsp;are&nbsp;correct.
                </p>
            </div>


            <!-- Survey Selected -->
            <div class="summary-tab">
                <div class="summary-icon-title">
                    <div class="summary-icon">
                        <img src="{{ $surveyIcon }}" style="width:80%" alt="">
                    </div>
                    <div class="summary-title">
                        <h4>Survey Selected:</h4>
                    </div>
                </div>
                <div class="summary-value">{{ $displayLabel }}</div>
            </div>

            {{-- Add-ons block rules --}}
            @if ($showAddonsBlock)
            <div class="summary-tab" id="addonsSummaryTab">
                <div class="summary-icon-title">
                    <div class="summary-icon">
                        <img src="{{ asset('assets/user/icons/Adda-on chosen.png') }}" style="width:80%" alt="">
                    </div>
                    <div class="summary-title">
                        <h4>Add-ons Selected:</h4>
                    </div>
                </div>
                <div class="summary-value">
                    @if ($forceAllAddons)
                    <ul class="custom-list">
                        <li>Aerial Images</li>
                        <li>Reinstatement</li>
                        <li>Estimated Costings</li>
                    </ul>
                    @else
                    @if (!$hasAddon)
                    <span>No add-ons selected</span>
                    @else
                    <ul class="custom-list">
                        @if ($hasAerial) <li>Aerial Images</li> @endif
                        @if ($hasInsurance) <li>Reinstatement</li> @endif
                        @if ($hasBreakdown) <li>Estimated Costings</li> @endif
                    </ul>
                    @endif
                    @endif
                </div>
            </div>
            @endif

            <!-- Property Address -->
            <div class="summary-tab">
                <div class="summary-icon-title">
                    <div class="summary-icon">
                        <img src="{{ asset('assets/user/icons/Property address.png') }}" style="width:80%" alt="">
                    </div>
                    <div class="summary-title">
                        <h4>Property Address:</h4>
                    </div>
                </div>
                <div class="summary-value">{{ $survey->full_address }}</div>
            </div>

            <!-- Property Type -->
            <div class="summary-tab">
                <div class="summary-icon-title">
                    <div class="summary-icon">
                        <img src="{{ asset('assets/user/icons/Property type.png') }}" style="width:80%" alt="">
                    </div>
                    <div class="summary-title">
                        <h4>Property Type:</h4>
                    </div>
                </div>
                <div class="summary-value">{{ $survey->house_or_flat }}</div>
            </div>

            <!-- Size -->
            <div class="summary-tab">
                <div class="summary-icon-title">
                    <div class="summary-icon">
                        <img src="{{ asset('assets/user/icons/Propert size.png') }}" style="width:80%" alt="">
                    </div>
                    <div class="summary-title">
                        <h4>Property Size (if over 1650 sqft):</h4>
                    </div>
                </div>
                @if ($survey->over1650 == 'yes')
                <div class="summary-value">{{ $survey->sqft_area }}</div>
                @else
                <div class="summary-value" style="width: 40%">The dwelling including any outbuildings and/or garages is below 1650 SqFt.</div>

                @endif
            </div>

            <!-- Bedrooms -->
            <div class="summary-tab">
                <div class="summary-icon-title">
                    <div class="summary-icon">
                        <img src="{{ asset('assets/user/icons/Number of bedrooms.png') }}" style="width:80%" alt="">
                    </div>
                    <div class="summary-title">
                        <h4>Number of Bedrooms:</h4>
                    </div>
                </div>
                <div class="summary-value">{{ $survey->number_of_bedrooms }}</div>
            </div>

            <!-- Total + Proceed -->
            <div class="summary-totalbar">
                <p class="summary-totalbar__label">
                    Total to be paid:
                    <span class="summary-totalbar__amount">£{{ $survey->level_total }}</span>
                </p>
                <button type="button" id="proceedFromSummaryBtn" class="summary-totalbar__btn">
                    Proceed
                </button>
            </div>

        </div>


        <!-- Progress -->
        <div class="progress-container" id="progressContainer">
            <div class="progress-bar" data-progress="2">
                {{-- <div class="progress-step active" data-step="1">
                    <!-- make step 1 look completed-style -->
                    <div class="step-circle" style="background:var(--primary); border-color:var(--primary); color:#1A202C;">1</div>
                    <div class="step-label">Summary</div>
                </div> --}}
                <div class="progress-step" data-step="2">
                    <div class="step-circle" style="background: var(--primary);  border-color: var(--primary); color: #1A202C;">1</div>
                    <div class="step-label">Client Details</div>
                </div>
                <div class="progress-step" data-step="3">
                    <div class="step-circle">2</div>
                    <div class="step-label">Property Details</div>
                </div>
                <div class="progress-step" data-step="4">
                    <div class="step-circle">3</div>
                    <div class="step-label">Solicitors</div>
                </div>
                <div class="progress-step" data-step="5">
                    <div class="step-circle">4</div>
                    <div class="step-label">Agents</div>
                </div>
                <div class="progress-step" data-step="6">
                    <div class="step-circle">5</div>
                    <div class="step-label">Terms & Payment</div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="surveyForm" method="POST" action="{{ route('user.flettons.rics.survey.submit') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $survey->id }}" />

            <div class="form-content">
                <!-- Step 1 placeholder (hidden) -->
                <div class="step" id="step1" style="display:none;"></div>

                <!-- Step 2: Client Details -->
                <div class="step" id="step2">
                    <h2 class="step-title">Client Details</h2>

                    <div class="form-group">
                        <label for="title">Title</label>
                        <select id="title" name="inf_field_Title" class="form-control">
                            <option value="">Please select one</option>
                            <option value="Dr.">Dr.</option>
                            <option value="Lord.">Lord.</option>
                            <option value="Miss.">Miss.</option>
                            <option value="Mr.">Mr.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Ms.">Ms.</option>
                            <option value="Prof">Prof</option>
                            <option value="Sir">Sir</option>
                            <option value="Other.">Other.</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="firstName">First Name <span class="required">*</span></label>
                        <input type="text" id="firstName" name="inf_field_FirstName" class="form-control"
                            value="{{ $survey->first_name }}" placeholder="Enter your first name" required />
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name <span class="required">*</span></label>
                        <input type="text" id="lastName" name="inf_field_LastName" class="form-control"
                            value="{{ $survey->last_name }}" placeholder="Enter your last name" required />
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="inf_field_Email" class="form-control"
                            value="{{ $survey->email_address }}" placeholder="Enter your email address" required readonly />
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <div class="telephone-field">
                            <input class="tel-input form-control" type="tel" id="telephone_number"
                                name="inf_field_Phone1" value="{{ $survey->telephone_number }}" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="homeAddress">Your Home Address <span class="required">*</span></label>
                        <input type="text" id="homeAddress" name="inf_field_StreetAddress1" class="form-control"
                            placeholder="Enter your home address" required />
                    </div>

                    <div class="form-group">
                        <label for="postalCode">Postal Code <span class="required">*</span></label>
                        <input type="text" id="postalCode" name="inf_field_PostalCode" class="form-control"
                            placeholder="Enter your postal code" required />
                    </div>
                </div>

                <!-- Step 3: Survey Property Details -->
                <div class="step" id="step3">
                    <h2 class="step-title">Survey Property Details</h2>

                    <div class="form-group">
                        <label for="surveyAddress">Survey Street Address <span class="required">*</span></label>
                        <input type="text" id="surveyAddress" name="inf_field_Address2Street1"
                            class="form-control" placeholder="Enter the property address to be surveyed"
                            value="{{ $survey->full_address }}" required />
                    </div>

                    <div class="form-group">
                        <label for="surveyPostalCode">Survey Postal Code <span class="required">*</span></label>
                        <input type="text" id="surveyPostalCode" name="inf_field_PostalCode2"
                            class="form-control" placeholder="Enter the property postal code"
                            value="{{ $survey->postcode }}" required />
                    </div>

                    <div class="form-group">
                        <label for="propertyLink">Rightmove/Zoopla/Agent's Link <span class="required">*</span></label>
                        <input type="url" id="propertyLink" name="inf_custom_PropertyLink" class="form-control"
                            placeholder="https://www.rightmove.co.uk/..." required />
                    </div>

                    <div class="form-group">
                        <label for="vacant">Vacant or Occupied <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="vacant" name="inf_custom_VacantorOccupied" value="Vacant" required />
                                <label for="vacant">Vacant</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="occupied" name="inf_custom_VacantorOccupied" value="Occupied" required />
                                <label for="occupied">Occupied</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="extensionsYes">Any Extensions? <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="extensionsYes" name="inf_custom_AnyExtensions" value="1" required />
                                <label for="extensionsYes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="extensionsNo" name="inf_custom_AnyExtensions" value="0" required />
                                <label for="extensionsNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="garageYes">Garage? <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="garageYes" name="inf_custom_Garage" value="1" required />
                                <label for="garageYes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="garageNo" name="inf_custom_Garage" value="0" required />
                                <label for="garageNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="garageLocationField" style="display:none">
                        <label for="garageLocation">Garage Location</label>
                        <input type="text" id="garageLocation" name="inf_custom_GarageLocation"
                            class="form-control" placeholder="Describe the garage location" />
                    </div>

                    <div class="form-group">
                        <label for="gardenYes">Garden? <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="gardenYes" name="inf_custom_Garden" value="1" required checked />
                                <label for="gardenYes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="gardenNo" name="inf_custom_Garden" value="0" required />
                                <label for="gardenNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="gardenLocationField" style="display:block">
                        <label for="gardenLocation">Garden Location</label>
                        <input type="text" id="gardenLocation" name="inf_custom_GardenLocation"
                            class="form-control" placeholder="Describe the garden location" />
                    </div>

                    <div class="form-group">
                        <label for="specificConcerns">Your Specific Concerns <span class="required">*</span></label>
                        <textarea id="specificConcerns" name="inf_custom_SpecificConcerns" class="form-control" rows="5"
                            placeholder="Please describe any specific concerns you have about the property..." required></textarea>
                    </div>
                </div>

                <!-- Step 4: Solicitors Details -->
                <div class="step" id="step4">
                    <h2 class="step-title">Solicitors Details</h2>

                    <div class="form-group">
                        <label for="solicitorYes">Do you have a solicitor? <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="solicitorYes" name="inf_custom_SolicitorFirm" value="yes" required />
                                <label for="solicitorYes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="solicitorNo" name="inf_custom_SolicitorFirm" value="no" required />
                                <label for="solicitorNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="solicitor-fields" id="solicitorFields" style="display:none">
                        <div class="form-group">
                            <label for="solicitorFirmName">Solicitor Firm Name <span class="required">*</span></label>
                            <input type="text" id="solicitorFirmName" name="inf_custom_SolicitorFirmName"
                                class="form-control" placeholder="Enter solicitor firm name" />
                        </div>

                        <div class="form-group">
                            <label for="conveyancerName">Conveyancer (Direct Contact Name) <span class="required">*</span></label>
                            <input type="text" id="conveyancerName" name="inf_custom_ConveyancerName"
                                class="form-control" placeholder="Enter conveyancer name" />
                        </div>

                        <div class="form-group">
                            <label for="solicitorPhone">Solicitor Phone Number <span class="required">*</span></label>
                            <div class="telephone-field">
                                <input type="tel" id="solicitorPhone" name="inf_custom_SolicitorPhoneNumber1"
                                    class="tel-input form-control" />


                            </div>
                        </div>


                        <div class="form-group">
                            <label for="solicitorEmail">Solicitor's Email <span class="required">*</span></label>
                            <input type="email" id="solicitorEmail" name="inf_custom_SolicitorsEmail"
                                class="form-control" placeholder="Enter solicitor's email" />
                        </div>

                        <div class="form-group">
                            <label for="solicitorAddress">Solicitor Address</label>
                            <input type="text" id="solicitorAddress" name="inf_custom_SolicitorAddress"
                                class="form-control" placeholder="Enter solicitor address" />
                        </div>

                        <div class="form-group">
                            <label for="solicitorPostalCode">Postal Code</label>
                            <input type="text" id="solicitorPostalCode" name="inf_custom_SolicitorPostalCode"
                                class="form-control" placeholder="Enter postal code" />
                        </div>

                        <div class="form-group">
                            <label>Do you know the exchange date?</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input type="radio" id="exchangeKnownYes" name="inf_custom_exchange_known" value="yes" />
                                    <label for="exchangeKnownYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="exchangeKnownNo" name="inf_custom_exchange_known" value="no" />
                                    <label for="exchangeKnownNo">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="exchangeDateField" style="display:none">
                            <label for="exchangeDate">Exchange Date <span class="required">*</span></label>
                            <input type="date" id="exchangeDate" name="inf_custom_ExchangeDate" class="form-control" />
                        </div>
                    </div>
                </div>

                <!-- Step 5: Agents Details -->
                <div class="step" id="step5">
                    <h2 class="step-title">Agents Details</h2>

                    <div class="form-group">
                        <label for="agentCompanyName">Agent Company Name <span class="required">*</span></label>
                        <input type="text" id="agentCompanyName" name="inf_custom_AgentCompanyName"
                            class="form-control" placeholder="Enter agent company name" required />
                    </div>

                    <div class="form-group">
                        <label for="agentName">Agent Name <span class="required">*</span></label>
                        <input type="text" id="agentName" name="inf_custom_AgentName" class="form-control"
                            placeholder="Enter agent name" required />
                    </div>

                    <div class="form-group">
                        <label for="agentPhone">Agent Phone Number <span class="required">*</span></label>
                        <div class="telephone-field">
                            <input class="tel-input form-control" type="tel" id="agentPhone"
                                name="inf_custom_AgentPhoneNumber" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="agentEmail">Agent's Email <span class="required">*</span></label>
                        <input type="email" id="agentEmail" name="inf_custom_AgentsEmail" class="form-control"
                            placeholder="Enter agent's email" required />
                    </div>

                    <div class="form-group">
                        <label for="agentAddress">Agent Address <span class="required">*</span></label>
                        <input type="text" id="agentAddress" name="inf_field_Address3Street1"
                            class="form-control" placeholder="Enter agent address" required />
                    </div>

                    <div class="form-group">
                        <label for="agentPostalCode">Agent Postal Code <span class="required">*</span></label>
                        <input type="text" id="agentPostalCode" name="inf_field_PostalCode3" class="form-control"
                            placeholder="Enter agent postal code" required />
                    </div>
                </div>

                <!-- Step 6: Terms & Payment -->
                <div class="step" id="step6">
                    <h2 class="step-title">Terms & Payment</h2>

                    <div class="important-notice">
                        <h3>IMPORTANT NOTICE:</h3>
                        <p>Please ensure that you have chosen the correct survey for your property before payment. Our policy is as follows:</p>
                        <p>1. You confirm that all of the information provided in the quote process is factual and correct.</p>
                        <p>2. I understand that If the property is house or flat built before 1985, or has been altered structurally in any way, a LEVEL THREE SURVEY is required, not a LEVEL TWO SURVEY. We do not deviate from this policy.</p>
                        <p>Please see our home page for the
                            <strong class="hover-a-tag"><a href="https://flettons.group/wp-content/uploads/2025/07/FLOW-CHART-FINAL-2023-1086x1536-1.webp" target="_blank" rel="noopener noreferrer">WHICH SURVEY</a></strong>
                            flow chart.
                        </p>
                        <p>If you proceed to book incorrectly, we will cancel the order and charge 1.25% of the transaction fee to cover the cost of the transaction.</p>
                    </div>

                    <div class="form-group" style="max-width:100%">
                        <label for="signature" style="font-weight:bold">Signature <span class="required">*</span></label>

                        <canvas id="signatureCanvas" width="400" height="120"></canvas>
                        <input type="hidden" id="signatureInput" name="inf_custom_infcustomSignature" required />

                        <div style="text-align:center; margin:15px 0; font-weight:bold; color:#ccc;">— OR —</div>

                        <input type="text" id="typedName" placeholder="Type your name instead" />

                        <button type="button" id="clearSignatureBtn">Clear Signature</button>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="termsCheckbox"
                                name="inf_option_IconfirmthatIhavereadandunderstandtheterms" value="921" required />
                            <label for="termsCheckbox">I confirm that I have read and understand the
                                <a target="_blank" href="https://www.flettons.com/toe">Terms of Engagement</a>. <span class="required">*</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer nav -->
            <div class="form-navigation">
                <button type="button" id="prevBtn" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <div></div>
                <button type="button" id="nextBtn" class="btn btn-primary">
                    Proceed <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" id="submitBtn" class="btn btn-submit" style="display:none">
                    <i class="fas fa-credit-card"></i> Instruct and Pay
                </button>
            </div>
        </form>
    </div>

    <!-- Loading Overlay -->
    <div id="pageSplashLoader" class="ewm-splash">
        <div class="ewm-box">
            <h2 class="ewm-title">Please Wait</h2>
            <p class="ewm-sub">
                You will now be taken to a secure payment page<br />
                <span class="ewm-red">Do not click back or refresh the screen.</span>
            </p>
            <div class="ewm-ring"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
    <script src="{{ asset('assets/user/custom/js/customer-signup.js') }}"></script>
    <script src="{{ asset('assets/user/custom/js/telephone.js') }}"></script>

    <!-- Unified wizard controller (no FOUC) -->
    <script>
        (function() {
            const $landing = document.getElementById('bookingSummarySticky');
            const $progress = document.getElementById('progressContainer');
            const $next = document.getElementById('nextBtn');
            const $submit = document.getElementById('submitBtn');
            const $backHead = document.getElementById('headerBackBtn');
            const $proceed = document.getElementById('proceedFromSummaryBtn');
            const $prev = document.getElementById('prevBtn'); // stay hidden by CSS

            const MIN_STEP = 2,
                MAX_STEP = 6;
            let currentStep = MIN_STEP;

            const qsAll = (s) => Array.from(document.querySelectorAll(s));

            function setProgressActive(step) {
                qsAll('.progress-step').forEach(p => p.classList.remove('active', 'completed'));
                for (let i = 2; i <= MAX_STEP; i++) {
                    const node = document.querySelector(`.progress-step[data-step="${i}"]`);
                    if (!node) continue;
                    if (i < step) node.classList.add('completed');
                    if (i === step) node.classList.add('active');
                }
                document.querySelector('.progress-bar')?.setAttribute('data-progress', String(step));
            }

            function setStep(step) {
                currentStep = Math.min(Math.max(step, MIN_STEP), MAX_STEP);
                qsAll('.step').forEach(s => s.classList.remove('active'));
                const el = document.getElementById('step' + currentStep);
                if (el) {
                    el.style.display = '';
                    el.classList.add('active');
                }
                setProgressActive(currentStep);
                if ($next) $next.style.display = 'block'; // Hide Next button after first step
                if ($submit) $submit.style.display = 'none'; // Hide Submit button
                if ($backHead) $backHead.style.display = 'block'; // Show back button
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            function showWizard(startStep = MIN_STEP) {
                document.body.classList.remove('is-landing');
                if ($landing) $landing.style.display = 'none';
                if ($progress) $progress.style.display = '';
                // if ($next) {
                //     $next.innerHTML = `Proceed <i class="fas fa-arrow-right"></i>`;
                //     $next.style.display = 'none'; // Hide Next button, as we don’t need to go to the next step
                // }
                setStep(startStep); // Ensure only the first step is shown
            }

            function showLanding() {
                document.body.classList.add('is-landing');
                if ($landing) $landing.style.display = '';
                if ($progress) $progress.style.display = 'none';
                if ($submit) $submit.style.display = 'none';
                if ($next) {
                    $next.style.display = '';
                    $next.innerHTML = `Proceed <i class="fas fa-arrow-right"></i>`;
                    $next.onclick = () => showWizard(MIN_STEP); // Proceed to the first step only
                }
                if ($backHead) $backHead.style.display = 'none';
                qsAll('.step').forEach(s => s.classList.remove('active'));
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // Click handlers
            $proceed?.addEventListener('click', () => showWizard(MIN_STEP)); // Ensure we start at step 2
            // $backHead?.addEventListener('click', () => {
            //     showLanding(); // Only go back to the landing, no navigation to previous steps
            // });

            // IMPORTANT: Do NOT call showLanding() here — body already has class="is-landing"
        })();
    </script>

    <noscript>
        <style>
            .form-header,
            .progress-container,
            .form-navigation {
                display: none !important;
            }
        </style>
        <div style="padding:16px;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;margin:16px">
            JavaScript is required to continue.
        </div>
    </noscript>
</body>

</html>
