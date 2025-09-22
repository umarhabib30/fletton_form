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
                        <div class="grw-card">
                            <div class="grw-left">
                                <div class="grw-badge">
                                    <img src="/assets/img/trustindex.png" alt="Trustindex" class="grw-logo">
                                   
                                </div>
                                <div class="grw-badge">
                                    <img src="/assets/img/google.png" alt="Google Reviews" class="grw-logo">
                                    
                                </div>
                            </div>

                            <div class="grw-center">
                                <div class="grw-title">
                                    {{ $name ?? 'Our Business' }} –
                                    <strong>{{ number_format($rating ?? 0, 1) }}</strong> out of 5 stars
                                    ({{ $total ?? 0 }} Google reviews)
                                </div>
                                <div class="grw-sub">
                                    Over 25 years of experience. In-house surveyors only.
                                </div>

                                @if(!empty($reviews))
                                <div class="grw-list">
                                    @foreach($reviews as $rev)
                                    <div class="grw-item">
                                        <div class="grw-item-head">
                                            @if($rev['profile_photo_url'])
                                            <img src="{{ $rev['profile_photo_url'] }}" alt="{{ $rev['author_name'] }}" class="grw-avatar">
                                            @endif
                                            <div>
                                                <div class="grw-name">{{ $rev['author_name'] }}</div>
                                                <div class="grw-inline-stars" style="--rating: {{ (float) $rev['rating'] }};"></div>
                                                <div class="grw-time">{{ $rev['time_desc'] }}</div>
                                            </div>
                                        </div>
                                        <p class="grw-text">{{ $rev['text'] }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <!-- <div class="grw-right">
                                @if(!empty($url))
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="grw-btn">View on Google</a>
                                @endif
                            </div> -->
                        </div>

                    </div>

                    <!-- ==================== STEP 2 (Add-ons) — UPDATED HTML ==================== -->
                    <div class="step-2">
                        <div class="level3">
                            <div class="ddons-header">
                                <h3>Level 3 Add-ons</h3>
                                <p>Choose additional services individually to add to your level 3 RICS Building Survey.</p>
                            </div>

                            <div class="addons-card">
                                <!-- 3-card grid -->
                                <div class="addons-grid cards-3">

                                    <!-- Estimated Costs Package -->
                                    <div class="addon-card">
                                        <!-- info icon + popover -->
                                        <div class="addon-info">
                                            <button type="button" class="info-btn" aria-label="More info">
                                                <i class="fa-solid fa-info"></i>
                                            </button>
                                            <div class="addon-pop">
                                                Breakdown of repairs and upgrades to help you budget and negotiate.
                                            </div>
                                        </div>

                                        <div class="addon-icon">
                                            <img src="{{ asset('assets/img/estimated-costs.png') }}" alt="Estimated Costs">
                                        </div>

                                        <h4 class="addon-title-strong">Estimated Costs Package</h4>
                                        <div class="addon-price-line">£{{ $price->repair_cost }}</div>
                                        <div class="addon-sub">(Repair & improvement costs)</div>

                                        <!-- Toggle Button -->
                                        <button type="button" class="addon-btn" data-group="grp-repair">Add</button>

                                        <!-- Hidden radios (DON'T TOUCH) -->
                                        <div id="grp-repair" class="radio-group addon" data-cost="{{ $price->repair_cost }}" style="display:none">
                                            <input type="radio" name="breakdown_of_estimated_repair_costs" value="0" checked>
                                            <input type="radio" name="breakdown_of_estimated_repair_costs" value="1">
                                        </div>
                                    </div>

                                    <!-- Drone Package -->
                                    <div class="addon-card">
                                        <div class="addon-info">
                                            <button type="button" class="info-btn" aria-label="More info">
                                                <i class="fa-solid fa-info"></i>
                                            </button>
                                            <div class="addon-pop">
                                                Drone images of roofs and chimneys for a clear view of condition.
                                            </div>
                                        </div>

                                        <div class="addon-icon">
                                            <img src="{{ asset('assets/img/drone.gif') }}" alt="Drone Package">
                                        </div>

                                        <h4 class="addon-title-strong">Aerial Drone Package</h4>
                                        <div class="addon-price-line">£{{ $price->aerial_chimney_cost }}</div>
                                        <div class="addon-sub">(Aerial roof & chimney images)</div>

                                        <button type="button" class="addon-btn" data-group="grp-drone">Add</button>

                                        <div id="grp-drone" class="radio-group addon" data-cost="{{ $price->aerial_chimney_cost }}" style="display:none">
                                            <input type="radio" name="aerial_roof_and_chimney" value="0" checked>
                                            <input type="radio" name="aerial_roof_and_chimney" value="1">
                                        </div>
                                    </div>

                                    <!-- Reinstatement Package -->
                                    <div class="addon-card">
                                        <div class="addon-info">
                                            <button type="button" class="info-btn" aria-label="More info">
                                                <i class="fa-solid fa-info"></i>
                                            </button>
                                            <div class="addon-pop">
                                                Accurate rebuild cost for insurance, ensuring full protection without overpaying.
                                            </div>
                                        </div>

                                        <div class="addon-icon">
                                            <img src="{{ asset('assets/img/reinstatement.png') }}" alt="Reinstatement Package">
                                        </div>

                                        <h4 class="addon-title-strong">Reinstatement Cost Package</h4>
                                        <div class="addon-price-line">£{{ $price->insurance_cost }}</div>
                                        <div class="addon-sub">(Rebuild valuation)</div>

                                        <button type="button" class="addon-btn" data-group="grp-ins">Add</button>

                                        <div id="grp-ins" class="radio-group addon" data-cost="{{ $price->insurance_cost }}" style="display:none">
                                            <input type="radio" name="insurance_reinstatement_valuation" value="0" checked>
                                            <input type="radio" name="insurance_reinstatement_valuation" value="1">
                                        </div>
                                    </div>

                                </div><!-- /.addons-grid -->

                                <!-- (optional legacy note kept hidden; JS still safe) -->
                                <div id="upgrade-note" style="display:none"></div>

                                <!-- ===== Single Bottom Box: savings + total + actions ===== -->
                                <div class="addons-bottom-bar">
                                    <!-- left: savings when all selected (JS toggles .level4-all-inlcude-addons) -->
                                    <div class="abb-left">
                                        <div class="level4-all-inlcude-addons" style="display:none">
                                            <span class="save-copy">Add all three and save
                                                <strong class="level-price">£250</strong>
                                            </span>
                                            <span class="save-price hidden">£250</span>
                                        </div>
                                    </div>

                                    <!-- center: total -->
                                    <div class="abb-center">
                                        <div class="level3-price level-price addons" id="total_with_addon" data-total="">
                                            <span class="label">Total Cost:</span> £{{ $survey->level3_price }}
                                        </div>
                                    </div>

                                    <!-- right: actions -->
                                    <div class="abb-right">
                                        <div class="btns">
                                            <div class="btn-style alt-btn" onclick="showStep1()">
                                                <i class="fa-solid fa-arrow-left"></i><span>Back</span>
                                            </div>
                                            <div class="btn-style level-3-confirm buy-now-btn" data-level="3">
                                                <span class="btn-loader"></span><span class="btn-text">Proceed</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /bottom box -->
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