<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RICS Survey Instruction Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('assets/user/custom/css/customer-signup.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7xLp13hLBGIDOt4BIJZrJF99ItTsya0g&libraries=places&callback=initAddressAutocomplete"
        defer></script>
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body>
    <div class="form-container">
        <div class="form-header">
            <div class="form-title">Instruction Request for RICS Survey</div>
            <div class="form-subtitle">Managed by Flettons Group</div>
        </div>

        <div class="progress-container">
            <div class="progress-bar" data-progress="1">
                <div class="progress-step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Summary</div>
                </div>
                <div class="progress-step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Client Details</div>
                </div>
                <div class="progress-step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Property Details</div>
                </div>
                <div class="progress-step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Solicitors</div>
                </div>
                <div class="progress-step" data-step="5">
                    <div class="step-circle">5</div>
                    <div class="step-label">Agents</div>
                </div>
                <div class="progress-step" data-step="6">
                    <div class="step-circle">6</div>
                    <div class="step-label">Terms & Payment</div>
                </div>
            </div>
        </div>

        <form id="surveyForm" method="POST" action="{{ route('user.flettons.rics.survey.submit') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $survey->id }}" id="">
            <div class="form-content">
                <!-- Step 1: Summary -->
                <div class="step active" id="step1">
                    <h2 class="step-title">Instruction Summary</h2>

                    <div class="summary-item slide-up">
                        <h4>Survey Type</h4>
                        <p><strong>Level @if ($survey->level == 4)
                                    3+
                                @else
                                    {{ $survey->level }}
                                @endif
                            </strong> RICS Building Survey</p>
                    </div>

                    <div class="summary-item slide-up">
                        <h4>Property Address</h4>
                        <p>{{ $survey->full_address }}</p>
                    </div>

                    <div class="summary-item slide-up">
                        <h4>Property Size</h4>
                        <p>
                            The floor area of the main dwellinghouse including outbuildings @if ($survey->over1650)
                                <strong>is over 1650sqft</strong>
                            @else
                                <strong>does not exceed {{ $survey->sqft_area }}sqft</strong>
                            @endif
                        </p>
                    </div>

                    <div class="summary-item slide-up">
                        <h4>Property Type</h4>
                        <p>{{ $survey->house_or_flat }}</p>
                    </div>

                    <div class="summary-item slide-up">
                        <h4>Number of Bedrooms</h4>
                        <p>{{ $survey->number_of_bedrooms }}</p>
                    </div>
                    @if ($survey->addons)
                        <div class="summary-item slide-up">
                            <h4>Add-ons Chosen</h4>
                            <ul class="add-ons-list">
                                @if ($survey->breakdown)
                                    <li>Breakdown of estimated repair costs</li>
                                @endif
                                @if ($survey->aerial)
                                    <li>Aerial roof and chimney inspection</li>
                                @endif
                                @if ($survey->insurance)
                                    <li>Insurance reinstatement valuation</li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>

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
                            value="{{ $survey->email_address }}" placeholder="Enter your email address" required
                            readonly />
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <div class="telephone-field">
                            <input class="tel-input form-control" type="tel" id="telephone_number" name="inf_field_Phone1"
                                value="{{ $survey->telephone_number }}" required />
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
                        <label for="propertyLink">Rightmove/Zoopla/Agent's Link
                            <span class="required">*</span></label>
                        <input type="url" id="propertyLink" name="inf_custom_PropertyLink" class="form-control"
                            placeholder="https://www.rightmove.co.uk/..." required />
                    </div>

                    <div class="form-group">
                        <label>Vacant or Occupied <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="vacant" name="inf_custom_VacantorOccupied"
                                    value="Vacant" required />
                                <label for="vacant">Vacant</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="occupied" name="inf_custom_VacantorOccupied"
                                    value="Occupied" required />
                                <label for="occupied">Occupied</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Any Extensions? <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="extensionsYes" name="inf_custom_AnyExtensions"
                                    value="1" required />
                                <label for="extensionsYes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="extensionsNo" name="inf_custom_AnyExtensions"
                                    value="0" required />
                                <label for="extensionsNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Garage? <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="garageYes" name="inf_custom_Garage" value="1"
                                    required />
                                <label for="garageYes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="garageNo" name="inf_custom_Garage" value="0"
                                    required />
                                <label for="garageNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="garageLocationField" style="display: none">
                        <label for="garageLocation">Garage Location</label>
                        <input type="text" id="garageLocation" name="inf_custom_GarageLocation"
                            class="form-control" placeholder="Describe the garage location" />
                    </div>

                    <div class="form-group">
                        <label>Garden? <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="gardenYes" name="inf_custom_Garden" value="1"
                                    required checked />
                                <label for="gardenYes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="gardenNo" name="inf_custom_Garden" value="0"
                                    required />
                                <label for="gardenNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="gardenLocationField" style="display: block">
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
                        <label>Do you have a solicitor?</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="solicitorYes" name="inf_custom_SolicitorFirm"
                                    value="yes" />
                                <label for="solicitorYes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="solicitorNo" name="inf_custom_SolicitorFirm"
                                    value="no" />
                                <label for="solicitorNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="solicitor-fields" id="solicitorFields" style="display: none">
                        <div class="form-group">
                            <label for="solicitorFirmName">Solicitor Firm Name <span class="required">*</span></label>
                            <input type="text" id="solicitorFirmName" name="inf_custom_SolicitorFirmName"
                                class="form-control" placeholder="Enter solicitor firm name" />
                        </div>

                        <div class="form-group">
                            <label for="conveyancerName">Conveyancer (Direct Contact Name)
                                <span class="required">*</span></label>
                            <input type="text" id="conveyancerName" name="inf_custom_ConveyancerName"
                                class="form-control" placeholder="Enter conveyancer name" />
                        </div>

                        <div class="form-group">
                            <label for="solicitorPhone">Solicitor Phone Number <span class="required">*</span></label>
                            <input type="tel" id="solicitorPhone" name="inf_custom_SolicitorPhoneNumber1"
                                class="form-control" placeholder="Enter solicitor phone number" />
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
                                    <input type="radio" id="exchangeKnownYes" name="inf_custom_exchange_known"
                                        value="yes" />
                                    <label for="exchangeKnownYes">Yes</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="exchangeKnownNo" name="inf_custom_exchange_known"
                                        value="no" />
                                    <label for="exchangeKnownNo">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="exchangeDateField" style="display: none">
                            <label for="exchangeDate">Exchange Date <span class="required">*</span></label>
                            <input type="date" id="exchangeDate" name="inf_custom_ExchangeDate"
                                class="form-control" />
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
                            <input class="tel-input form-control" type="tel" id="agentPhone" name="inf_custom_AgentPhoneNumber"
                               required />
                        </div>

                        {{-- <input type="tel" id="agentPhone" name="inf_custom_AgentPhoneNumber"
                            class="form-control" placeholder="Enter agent phone number" required /> --}}
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
                        <p>
                            Please ensure that you have chosen the correct survey for your
                            property before payment. Our policy is as follows:
                        </p>
                        <p>
                            1. You confirm that all of the information provided in the quote
                            process is factual and correct.
                        </p>
                        <p>
                            2. I understand that If the property is house or flat built
                            before 1985, or has been altered structurally in any way, a
                            LEVEL THREE SURVEY is required, not a LEVEL TWO SURVEY. We do
                            not deviate from this policy.
                        </p>
                        <p>
                            Please see our home page for the
                            <strong class="hover-a-tag"><a
                                    href="https://flettons.group/wp-content/uploads/2025/07/FLOW-CHART-FINAL-2023-1086x1536-1.webp"
                                    target="_blank" rel="noopener noreferrer">WHICH SURVEY</a></strong>
                            flow chart.
                        </p>
                        <p>
                            If you proceed to book incorrectly, we will cancel the order and
                            charge 1.25% of the transaction fee to cover the cost of the
                            transaction.
                        </p>
                    </div>

                    <div class="form-group" style="max-width: 100%">
                        <label for="signature" style="font-weight: bold">Signature <span
                                class="required">*</span></label>

                        <!-- Signature Canvas -->
                        <canvas id="signatureCanvas" width="400" height="120"></canvas>
                        <input type="hidden" id="signatureInput" name="inf_custom_infcustomSignature" required />

                        <div  style=" text-align: center;  margin: 15px 0; font-weight: bold; color: #ccc; ">
                            — OR —
                        </div>

                        <!-- Name Field Alternative -->
                        <input type="text" id="typedName" placeholder="Type your name instead" />

                        <!-- Clear Button -->
                        <button type="button" id="clearSignatureBtn">
                            Clear Signature
                        </button>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="termsCheckbox"
                                name="inf_option_IconfirmthatIhavereadandunderstandtheterms" value="921"
                                required />
                            <label for="termsCheckbox">I confirm that I have read and understand the
                                <a target="_blank" href="https://www.flettons.com/toe">Terms of Engagement</a>. <span
                                    class="required">*</span></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-navigation">
                <button type="button" id="prevBtn" class="btn btn-secondary" style="display: none">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <div></div>
                <button type="button" id="nextBtn" class="btn btn-primary">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" id="submitBtn" class="btn btn-submit" style="display: none">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- intl-tel-input JS + utils -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
    <!-- App JS -->
    <script src="{{ asset('assets/user/custom/js/customer-signup.js') }}"></script>
    <script src="{{ asset('assets/user/custom/js/telephone.js') }}"></script>

</body>

</html>
