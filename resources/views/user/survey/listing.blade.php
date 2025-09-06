<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RICS Survey Options</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
     <!-- App CSS -->
  <link rel="stylesheet" href="{{ asset('assets/user/custom/css/listing.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body style="overflow: hidden">
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
                            <h3 class="username">Hi {{ $survey->first_name }} {{ $survey->last_name }}</h3>
                            <p class="custom-m1">
                                Here are your Survey Options, prepared in coordination with
                                <b>Flettons Surveyors Ltd</b> (Regulated by RICS)
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
                                    <div class="level1-price level-price">£450</div>
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
                                    <div class="level2-price level-price">£750</div>
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
                                    <div class="level3-price level-price">£950</div>
                                </div>
                                <input type="hidden" id="level3-base-price" value="950">
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
                                    <div class="level4-price level-price">£1200</div>
                                </div>
                                <div class="btn-style buy-now-btn" data-level="4">
                                    <span class="btn-loader"></span><span class="btn-text">Instruct & Pay</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 (Add-ons) -->
                    <div class="step-2">
                        <div class="level3">
                            <h3>Level 3 Add-ons</h3>
                            <p>Choose additional services individually to add to your level 3 RICS Building Survey.</p>
                            <span class="quote_text addons">Add-ons</span>

                            <div class="addons-card">
                                <!-- Add-ons list -->
                                <div class="addons-grid">
                                    <div class="addon-item">
                                        <div class="addon-head">
                                            <span class="addon-badge">Add-on</span>
                                            <span class="addon-title">Breakdown of estimated repair costs, improvement
                                                costs &amp; provisional costs</span>
                                            <span class="addon-price-pill">£150</span>
                                        </div>
                                        <div class="addon-ctrl">
                                            <label class="addon-ctrl-label">Include this add-on?</label>
                                            <select name="breakdown_of_estimated_repair_costs" data-cost="150"
                                                class="addon">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                        <span class="addon-tick" aria-hidden="true">✓</span>
                                    </div>

                                    <div class="addon-item">
                                        <div class="addon-head">
                                            <span class="addon-badge">Add-on</span>
                                            <span class="addon-title">Aerial roof and chimney images</span>
                                            <span class="addon-price-pill">£100</span>
                                        </div>
                                        <div class="addon-ctrl">
                                            <label class="addon-ctrl-label">Include this add-on?</label>
                                            <select name="aerial_roof_and_chimney" data-cost="100" class="addon">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                        <span class="addon-tick" aria-hidden="true">✓</span>
                                    </div>

                                    <div class="addon-item">
                                        <div class="addon-head">
                                            <span class="addon-badge">Add-on</span>
                                            <span class="addon-title">Insurance reinstatement valuation (Rebuild
                                                Cost)</span>
                                            <span class="addon-price-pill">£200</span>
                                        </div>
                                        <div class="addon-ctrl">
                                            <label class="addon-ctrl-label">Include this add-on?</label>
                                            <select name="insurance_reinstatement_valuation" data-cost="200"
                                                class="addon">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                        <span class="addon-tick" aria-hidden="true">✓</span>
                                    </div>
                                </div>

                                <!-- Total + buttons (same classes so JS keeps working) -->
                                <div class="addons-footer">
                                    <div class="price-stack">
                                        <span class="label">Total</span>
                                        <div class="level3-price level-price addons">£950</div>
                                    </div>

                                    <div class="btns">
                                        <div class="btn-style level-3-confirm buy-now-btn" data-level="3">
                                            <span class="btn-loader"></span><span class="btn-text">Instruct &amp;
                                                Pay</span>
                                        </div>
                                        <div class="btn-style alt-btn" onclick="showStep1()">
                                            <i class="fa-solid fa-arrow-left"></i><span>Back</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- keep your existing upgrade hint (JS already uses it) -->
                            <div class="level4-all-inlcude-addons"
                                style="display:none;text-align:center;margin-top:10px">
                                <div class="btn-style-group">
                                    <div class="btn-style buy-now-btn" data-level="4">
                                        <span class="btn-loader"></span><span class="btn-text">Select level 3+</span>
                                    </div>
                                </div>
                                <p class="muted addons-select-message">Select Level 3+ to include all addons and you
                                    will save
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
                            By continuing, you agree to the process and to the <a href="#" target="_blank">Terms and
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

      <!-- App JS -->
  <script src="{{ asset('assets/user/custom/js/listing.js') }}"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
   <script>
    $(document).ready(function(){
        $('.buy-now-btn').click(function(){
            var selectedLevel = $(this).data('level');
            $('#selected_level').val(selectedLevel);

            var levelTotal = 0;
            if(selectedLevel == 1){
                levelTotal = 450;
            } else if(selectedLevel == 2){
                levelTotal = 750;
            } else if(selectedLevel == 3){
                // Calculate total for level 3 with addons
                levelTotal = 950; // base price
                $('.addon').each(function(){
                    if($(this).val() == '1'){
                        levelTotal += parseInt($(this).data('cost'));
                    }
                });
            } else if(selectedLevel == 4){
                levelTotal = 1200;
            }
            $('#level_total').val(levelTotal);

            // Show confirm popup
            document.getElementById('confirm-popup-conteiner').style.display = 'flex';
        });
    });
   </script>
</body>

</html>
