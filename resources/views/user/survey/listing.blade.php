<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RICS Survey Options</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('assets/user/custom/css/listing.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body style="overflow-x: hidden">
    <div class="container" id="quote-container">
        {{-- <input type="hidden" id="email_address" value="john@example.com">
        <input type="hidden" id="contact_id" value="12345"> --}}

        <form class="quote-f" action="{{ route('user.flettons.listing.submit') }}" method="POST" id="survey-form">
            @csrf
            <input type="hidden" name="id" id="survey_id" value="{{ $survey->id }}">
            <input type="hidden" name="level" id="selected_level" value="">
            <input type="hidden" name="level_total" id="level_total" value="">
            <div class="overlay">
                <div class="inner">

                    <!-- Step 1 -->
                    <div class="step-1">
                        <div class="header-section">
                            <h3 class="username">{{ $survey->first_name }}’s RICS Surveyor Quotes</h3>
                            <p class="custom-m1">
                                Your survey options, prepared in coordination with
                                <b>Flettons Surveyors Ltd</b> (Regulated by RICS).
                            </p>
                        </div>

                        <div class="level-choices-container">
                            <!-- Card 1 -->
                            <div class="level-choice" data-level="1">
                                <h3>Level 1</h3>
                                <div class="media">
                                    <img src="https://flettons.group/wp-content/plugins/flettons-survey/assets/images/ROOF-SURVEY-4.png"
                                        alt="Level 1 Survey">
                                </div>
                                <div class="price-custom">
                                    <label>Total Price</label>
                                    <div class="level1-price level-price">£{{ $survey->level1_price }}</div>
                                </div>
                                <div class="btn-style buy-now-btn" data-level="1">
                                    <span class="btn-loader"></span><span class="btn-text">Instruct & Pay</span>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="level-choice" data-level="2">
                                <h3>Level 2</h3>
                                <div class="media">
                                    <img src="https://flettons.group/wp-content/plugins/flettons-survey/assets/images/FLETTONS-LEVEL-2-4.png"
                                        alt="Level 2 Survey">
                                </div>
                                <div class="price-custom">
                                    <label>Total Price</label>
                                    <div class="level2-price level-price">£{{ $survey->level2_price }}</div>
                                </div>
                                <div class="btn-style buy-now-btn" data-level="2">
                                    <span class="btn-loader"></span><span class="btn-text">Instruct & Pay</span>
                                </div>
                            </div>

                            <!-- Card 3 -->
                            <div class="level-choice" data-level="3">
                                <h3>Level 3</h3>
                                <div class="media">
                                    <img src="https://flettons.group/wp-content/plugins/flettons-survey/assets/images/FLETTONS-LEVEL-3-4.png"
                                        alt="Level 3 Survey">
                                </div>
                                <div class="price-custom">
                                    <label>Total Price</label>
                                    <div class="level3-price level-price">£{{ $survey->level3_price }}</div>
                                </div>
                                <input type="hidden" id="level3-base-price" value="{{ $survey->level3_price }}">
                                <div class="btn-style" onclick="showAddons()">
                                    <i class="fa-solid fa-sliders"></i><span>Choose Add-ons</span>
                                </div>
                            </div>

                            <!-- Card 4 -->
                            <div class="level-choice" data-level="4">
                                <h3>Level 3+</h3>
                                <div class="media">
                                    <img src="https://flettons.group/wp-content/plugins/flettons-survey/assets/images/FLETTONS-LEVEL-3-4-1.png"
                                        alt="Level 3+ Survey">
                                </div>
                                <div class="price-custom">
                                    <label>Total Price</label>
                                    <input type="hidden" id="level4-base-price" value="1200">
                                    <div class="level4-price level-price">£{{ $survey->level4_price }}</div>
                                </div>
                                <div class="btn-style buy-now-btn" data-level="4">
                                    <span class="btn-loader"></span><span class="btn-text">Instruct & Pay</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== STEP 2 (Add-ons) — FINAL ==================== -->
                    <div class="step-2">
                        <div class="level3">
                            <h3>Level 3 Add-ons</h3>
                            <p>Choose additional services individually to add to your level 3 RICS Building Survey.</p>

                            <div class="addons-card">
                                <!-- 3-card grid -->
                                <div class="addons-grid cards-3">

                                    <!-- Estimated Costs Package -->
                                    <div class="addon-card">
                                        <div class="addon-icon">
                                            <img src="{{ asset('assets/img/estimated-costs.png') }}" alt="Estimated Costs">
                                        </div>

                                        <h4 class="addon-title-strong">Estimated Costs Package</h4>
                                        <div class="addon-price-line">£{{ $price->repair_cost }}</div>
                                        <div class="addon-sub">(Repair & improvement costs)</div>
                                        <p class="addon-desc">Breakdown of repairs and upgrades to help you budget and negotiate.</p>

                                        <!-- Toggle Button -->
                                        <button type="button" class="addon-btn" data-group="grp-repair">Add to Survey</button>

                                        <!-- Hidden radios for existing JS/backend (DON'T TOUCH) -->
                                        <div id="grp-repair" class="radio-group addon" data-cost="{{ $price->repair_cost }}" style="display:none">
                                            <input type="radio" name="breakdown_of_estimated_repair_costs" value="0" checked>
                                            <input type="radio" name="breakdown_of_estimated_repair_costs" value="1">
                                        </div>
                                    </div>

                                    <!-- Drone Package -->
                                    <div class="addon-card">
                                        <div class="addon-icon">
                                            <img src="{{ asset('assets/img/drone.png') }}" alt="Drone Package">
                                        </div>

                                        <h4 class="addon-title-strong">Drone Package</h4>
                                        <div class="addon-price-line">£{{ $price->aerial_chimney_cost }}</div>
                                        <div class="addon-sub">(Aerial roof & chimney images)</div>
                                        <p class="addon-desc">Drone images of roofs and chimneys for a clear view of condition.</p>

                                        <button type="button" class="addon-btn" data-group="grp-drone">Add to Survey</button>

                                        <div id="grp-drone" class="radio-group addon" data-cost="{{ $price->aerial_chimney_cost }}" style="display:none">
                                            <input type="radio" name="aerial_roof_and_chimney" value="0" checked>
                                            <input type="radio" name="aerial_roof_and_chimney" value="1">
                                        </div>
                                    </div>

                                    <!-- Reinstatement Package -->
                                    <div class="addon-card">
                                        <div class="addon-icon">
                                            <img src="{{ asset('assets/img/reinstatement.png') }}" alt="Reinstatement Package">
                                        </div>

                                        <h4 class="addon-title-strong">Reinstatement Package</h4>
                                        <div class="addon-price-line">£{{ $price->insurance_cost }}</div>
                                        <div class="addon-sub">(Rebuild valuation)</div>
                                        <p class="addon-desc">Accurate rebuild cost for insurance, ensuring full protection without overpaying.</p>

                                        <button type="button" class="addon-btn" data-group="grp-ins">Add to Survey</button>

                                        <div id="grp-ins" class="radio-group addon" data-cost="{{ $price->insurance_cost }}" style="display:none">
                                            <input type="radio" name="insurance_reinstatement_valuation" value="0" checked>
                                            <input type="radio" name="insurance_reinstatement_valuation" value="1">
                                        </div>
                                    </div>

                                </div><!-- /.addons-grid -->

                                <!-- Auto-upgrade note -->
                                <div id="upgrade-note" class="upgrade-note" style="display:none"></div>

                                <!-- Totals + buttons (unchanged IDs/classes) -->
                                <div class="addons-footer">
                                    <div class="price-stack">
                                        <div class="level3-price level-price addons" id="total_with_addon" data-total="">
                                            <span class="label">Total</span>
                                            £{{ $survey->level3_price }}
                                        </div>
                                    </div>
                                    <div class="btns-container">
                                        <div class="btn-style alt-btn" onclick="showStep1()" style="margin-right:10px;">
                                            <i class="fa-solid fa-arrow-left"></i><span>Back</span>
                                        </div>
                                        <div class="btn-style level-3-confirm buy-now-btn" data-level="3">
                                            <span class="btn-loader"></span><span class="btn-text">Instruct &amp; Pay</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Keep client’s upsell block -->
                            <div class="level4-all-inlcude-addons" style="display:none;text-align:center;margin-top:10px">
                                <div class="btn-style-group">
                                    <div class="btn-style buy-now-btn" data-level="4">
                                        <span class="btn-loader"></span><span class="btn-text">Select level 3+</span>
                                    </div>
                                </div>
                                <p class="muted addons-select-message" style="margin-bottom: 0;">
                                    Select Level 3+ to include all addons and you will save
                                    <span class="level-price" style="font-size:18px">£250</span>
                                </p>
                                <span class="save-price hidden">£250</span>
                            </div>

                        </div>
                    </div>



                </div>
            </div>
        </form>
    </div>

    <!-- Confirm Popup -->
    <div class="confirm-popup-conteiner" id="confirm-popup-conteiner">
        <div class="confirm-popup">
            <div class="confirm-popup-inner">
                <div class="confirm-popup-close" onclick="closePopup()"><span>×</span></div>
                <h3 class="confirm-popup-title">Confirm and Proceed</h3>
                <p>Flettons Group LLC will manage your payment and booking through the platform, and your data will be
                    shared with relevant parties involved in the property transaction to deliver the service.</p>
                <p>Your survey will be carried out by <br><b>Flettons Surveyors Ltd – Regulated by RICS.</b></p>
                <p>You will then be taken to an instruction form to complete, where you can review the terms of
                    engagement and finalise your instruction.</p>
                <div class="terms-checkbox">
                    <label>
                        <input type="checkbox" id="termsCheckbox" name="terms_agreed" value="1" required>
                        <div>
                            By continuing, you agree to the process and to the <a href="#" target="_blank">Terms
                                and
                                Conditions</a>.</div>
                    </label>
                </div>
                <div class="btn-style-group">
                    <div class="btn-style confirm-yes" onclick="proceedWithBooking()">
                        <span class="btn-loader"></span><span class="btn-text">Proceed</span>
                    </div>
                    <div class="btn-style" onclick="closePopup()">Go Back</div>
                </div>
                <div class="wait-notice" id="wait-notice">
                    <div class="wait-title">Please Wait</div>
                    <div class="wait-sub">(Please do not click back or refresh the screen)</div>
                </div>
                <p class="muted" style="margin-top:10px">Powered by Flettons Group</p>
            </div>
        </div>
    </div>
    <input type="hidden" name="" id="level1_price" value="{{ $survey->level1_price }}">
    <input type="hidden" name="" id="level2_price" value="{{ $survey->level2_price }}">
    <input type="hidden" name="" id="level3_price" value="{{ $survey->level3_price }}">
    <input type="hidden" name="" id="level4_price" value="{{ $survey->level4_price }}">
    <!-- App JS -->
    <script src="{{ asset('assets/user/custom/js/listing.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

</body>

</html>