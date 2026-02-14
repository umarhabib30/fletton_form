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
    
    <!-- offer model css -->
  <style>
:root {
  --navy: #1A202C;
  --navy-soft: #1b2742;
  --lime: #C1EC4A;
}

/* =====================================================
   PROMO MODAL BACKDROP
===================================================== */
.promo-modal-backdrop{
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.70);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 99999;
  padding: 18px;
}

.promo-modal-backdrop.is-open{
  display: flex;
}

/* =====================================================
   MODAL WRAPPER (CENTERED + SLOW ZOOM)
===================================================== */
.promo-modal{
  position: relative;
  width: min(420px, 100%);
  transform-origin: center;

  /* Initial hidden state */
  opacity: 0;
  transform: scale(0.88);

  /* Slower premium transition */
  transition:
    transform 1400ms cubic-bezier(.16,1,.3,1),
    opacity 1100ms ease;
}

/* Visible state */
.promo-modal-backdrop.is-open .promo-modal{
  opacity: 1;
  transform: scale(1);
  animation: promoZoomSlow 1500ms cubic-bezier(.16,1,.3,1) both;
}

/* Luxury zoom animation */
@keyframes promoZoomSlow {
  0%   { transform: scale(0.88); opacity: 0; }
  70%  { transform: scale(1.01); opacity: 1; }
  100% { transform: scale(1); }
}

/* =====================================================
   CLOSE BUTTON
===================================================== */
.promo-modal-close{
  position: absolute;
  top: 10px;
  right: 45px;
  width: 34px;
  height: 34px;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  background: var(--navy);
  color: #fff;
  font-size: 18px;
  font-weight: 700;
  display: grid;
  place-items: center;
  box-shadow: 0 10px 24px rgba(0,0,0,.35);
  z-index: 2;
}

@media (max-width: 480px){
  .promo-modal-close{
    right: 10px;
  }
}


/* =====================================================
   PROMO CARD CONTENT
===================================================== */
#promoModal .promo-banner{
  margin: auto;
  width: 350px;
  max-width: 100%;
  background: radial-gradient(circle at center, var(--navy-soft) 0%, var(--navy) 70%);
  border: 6px solid var(--lime);
  border-radius: 16px;
  padding: 18px 14px;
  text-align: center;
  font-family: 'Poppins', sans-serif;
  box-shadow:
    0 14px 32px rgba(0, 0, 0, 0.45),
    0 0 0 1px rgba(200, 240, 0, 0.15);
}

#promoModal a{
  text-decoration: none;
}

/* Logo */
#promoModal .brand-logo{
  margin: 0 auto 10px;
}

#promoModal .brand-logo img{
  max-width: 130px;
  width: 100%;
  height: auto;
  display: block;
  margin: 0 auto;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.45));
}

/* Divider */
#promoModal .divider-container{
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin-bottom: 8px;
}

#promoModal .divider-line{
  width: 48px;
  height: 1px;
  background: var(--lime);
}

#promoModal .discount-subtitle{
  font-size: 12px;
  color: #e2e8f0;
  white-space: nowrap;
}

/* Text */
#promoModal .save-text{
  font-size: 28px;
  font-weight: 500;
  color: #fff;
  margin: 8px 0;
}

#promoModal .save-text span{
  color: var(--lime);
}

#promoModal .discount-info{
  font-size: 12px;
  color: #e2e8f0;
  margin-bottom: 8px;
}

/* Discount Code */
#promoModal .code-box{
  background: var(--lime);
  color: var(--navy);
  font-size: 20px;
  font-weight: 600;
  padding: 6px 16px;
  border-radius: 10px;
  display: inline-block;
  letter-spacing: 3px;
  margin-bottom: 10px;
  box-shadow:
    0 4px 10px rgba(0, 0, 0, 0.35),
    inset 0 -2px 0 rgba(0, 0, 0, 0.18);
}

/* Terms */
#promoModal .terms-text{
  font-size: 11px;
  color: #cbd5e1;
  line-height: 1.4;
}

/* =====================================================
   CONFETTI CANVAS
===================================================== */
canvas.confetti-canvas{
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 99998;
}

/* =====================================================
   REDUCED MOTION (ACCESSIBILITY)
===================================================== */
@media (prefers-reduced-motion: reduce){
  .promo-modal{
    transition: none !important;
    animation: none !important;
    transform: none !important;
    opacity: 1 !important;
  }
}

</style>


    
</head>

<body style="overflow-x: hidden">
    <div class="container" id="quote-container">

        <input type="hidden" id="apply_tag_url" value="{{ url('update-survey-tag') }}" name="" id="">

        <form class="quote-f" action="{{ route('user.flettons.listing.submit') }}" method="POST" id="survey-form">
            @csrf
            <input type="hidden" name="id" id="survey_id" value="{{ $survey->id }}">
            <input type="hidden" name="contact_id" id="contact_id" value="{{ $survey->contact_id }}">
            <input type="hidden" name="level" id="selected_level" value="">
            <input type="hidden" name="level_total" id="level_total" value="">
            <div class="overlay">
                <div class="inner">

                    <!-- Step 1 -->
                    <div class="step-1">
                        <div class="header-section">
                            <h3 class="username">Your RICS Regulated Surveyor Quotes</h3>
                            <p class="custom-m1">
                                Surveying undertaken by Flettons Surveyors Ltd -
                                Regulated by RICS. All price quote are final.
                            </p>
                        </div>

                        <div class="level-choices-container">
                            <!-- Card 1 -->
                            <div class="level-choice" data-level="1">
                                <div class="level-head-section">
                                    <div class="addon-info" bis_skin_checked="1">
                                        
                                        <span style="width: 100%;text-align: center;font-size: 14px; font-weight: bold;">A stand-alone roof survey </span>
                                        <button type="button" class="info-btn" aria-label="More info">
                                            <i class="fa-solid fa-info"></i>
                                        </button>
                                        <div class="addon-pop" bis_skin_checked="1">
                                            A stand-alone external roof survey using a drone, providing a full independent
                                            report on the condition of the roof structure, roof coverings, flashings, gutters,
                                            chimneys, and related elements. External inspection only; no internal inspection
                                            is included.
                                        </div>
                                    </div>
                                </div>
                                <div class="level-img">
                                    <img src="{{ asset('assets/user/icons/dron.gif') }}" alt="Level 1 Survey">
                                </div>
                                <h3 class="card-heading">Roof Survey</h3>
                                <div class="price-custom">
                                    <div class="level1-price level-price">£{{ $survey->level1_price }}</div>
                                    <label>Total Price</label>
                                </div>
                                <div class="btn-style buy-now-btn" data-level="1">
                                    <span class="btn-loader"></span><span class="btn-text">Book</span>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="level-choice" data-level="2">
                                <div class="level-head-section">
                                    <div class="addon-info" bis_skin_checked="1">
                                        
                                        <span style="width: 100%;text-align: center;font-size: 14px; font-weight: bold;">For properties built after
                                            1985 </span>
                                        <button type="button" class="info-btn" aria-label="More info">
                                            <i class="fa-solid fa-info"></i>
                                        </button>
                                        <div class="addon-pop" bis_skin_checked="1">
                                            A Level 2 Home Survey provides a detailed visual inspection of the property,
                                            reporting on its condition, construction, and any significant issues such as
                                            defects, repairs, and maintenance requirements. It is suitable for conventional
                                            homes in reasonable condition. The inspection is non-intrusive and does not
                                            include opening up the structure or specialist testing.
                                        </div>
                                    </div>

                                </div>
                                <div class="level-img">
                                    <img src="{{ asset('assets/user/icons/Level 2 survey.png') }}" alt="Level 1 Survey">
                                </div>
                                <h3 class="card-heading">Level 2 Survey</h3>
                                <div class="price-custom">
                                    <div class="level2-price level-price">£{{ $survey->level2_price }}</div>
                                    <label>Total Price</label>
                                </div>
                                <div class="btn-style buy-now-btn" data-level="2">
                                    <span class="btn-loader"></span><span class="btn-text">Book</span>
                                </div>
                            </div>

                            <!-- Card 3 -->
                            <div class="level-choice" data-level="3">
                                <div class="level-head-section">
                                    <div class="addon-info" bis_skin_checked="1">
                                       
                                        <span style="width: 100%;text-align: center;font-size: 16px; font-weight: bold; margin-right: 10px">For all
                                            Property Types </span>
                                        <button type="button" class="info-btn" aria-label="More info">
                                            <i class="fa-solid fa-info"></i>
                                        </button>
                                        <div class="addon-pop" bis_skin_checked="1">
                                            A Level 3 Home Survey provides a comprehensive and detailed inspection of
                                            the property, covering its construction, condition, and all visible defects. It
                                            includes advice on remedial works, future maintenance, and potential risks to
                                            the building. This survey is suitable for older, altered, or non-standard
                                            properties, or where significant defects are suspected. The inspection is
                                            thorough but non-intrusive and does not include opening up the structure or
                                            specialist testing.

                                        </div>
                                    </div>

                                </div>
                                <div class="level-img">
                                    <img src="{{ asset('assets/user/icons/Level 3 survey.png') }}" alt="Level 1 Survey">
                                </div>
                                <h3 class="card-heading">Level 3 Survey</h3>
                                <div class="price-custom">
                                    <div class="level3-price level-price">£{{ $survey->level3_price }}</div>
                                    <label>Total Price</label>
                                </div>
                                <input type="hidden" id="level3-base-price" value="{{ $survey->level3_price }}">
                                <div class="btn-style" onclick="showAddons()">
                                    Book
                                </div>
                            </div>

                            <!-- Card 4 -->
                            <div class="level-choice" data-level="4">
                                <div class="level-head-section">
                                    <div class="addon-info" bis_skin_checked="1">
                                      
                                        <span style="width: 100%;text-align: center;font-size: 20px; font-weight: bold; margin-right: 25px">Most
                                            Popular </span>
                                        <button type="button" class="info-btn" aria-label="More info">
                                            <i class="fa-solid fa-info"></i>
                                        </button>
                                        <div class="addon-pop" bis_skin_checked="1">
                                            A Level 3+ Home Survey provides the most comprehensive inspection available,
                                            combining the full Level 3 service with all additional add-on packages. This
                                            includes drone roof images, an insurance reinstatement cost assessment, a
                                            breakdown of estimated repair costs, and extension or conversion feasibility
                                            advice. It offers the highest level of detail, giving you a complete understanding
                                            of the property’s condition, costs, and future potential.
                                        </div>
                                    </div>

                                </div>
                                <div class="level-img">
                                    <img src="{{ asset('assets/user/icons/Level 3 plus survey.png') }}"
                                        alt="Level 1 Survey">
                                </div>
                                <h3 class="card-heading">Level 3+ Survey</h3>
                                <div class="price-custom">
                                    <input type="hidden" id="level4-base-price" value="1200">
                                    <div class="level4-price level-price">£{{ $survey->level4_price }}</div>
                                    <label>Total Price</label>
                                </div>
                                <div class="btn-style buy-now-btn" data-level="4">
                                    <span class="btn-loader"></span><span class="btn-text">Book</span>
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
                                    Flettons Surveyors – 4.7 out 5 stars Rated Excellent. Over 25 years of experience.
                                    In house Surveyors only.
                                </div>

                                @if (!empty($reviews))
                                <div class="grw-list">
                                    @foreach ($reviews as $rev)
                                    <div class="grw-item">
                                        <div class="grw-item-head">
                                            @if ($rev['profile_photo_url'])
                                            <img src="{{ $rev['profile_photo_url'] }}"
                                                alt="{{ $rev['author_name'] }}" class="grw-avatar">
                                            @endif
                                            <div>
                                                <div class="grw-name">{{ $rev['author_name'] }}</div>
                                                <div class="grw-inline-stars"
                                                    style="--rating: {{ (float) $rev['rating'] }};"></div>
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
                                @if (!empty($url))
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="grw-btn">View on Google</a>
                                @endif
                            </div> -->
                        </div>

                    </div>

                    <!-- ==================== STEP 2 (Add-ons) — CLEAN HTML ==================== -->
                    <div class="step-2">
                        <div class="level3">
                            <header class="addons-header">
                                <button type="button" class="addons-back" onclick="showStep1()" aria-label="Back">
                                    <i class="fa-solid fa-angle-left"></i>
                                </button>
                                <h3 class="addons-title">Choose Your Add-Ons</h3>
                                <p class="addons-subtitle">
                                    Tailor your survey to your needs with exclusive add-on options.
                                </p>
                            </header>

                            <section class="addons-card">
                                <!-- 3-card grid -->
                                <div class="addons-grid cards-3">

                                    <!-- Estimated Costs Package -->
                                    <article class="addon-card">
                                        <div class="addon-info">
                                            <span></span>
                                            <button type="button" class="info-btn" aria-label="More info">
                                                <i class="fa-solid fa-info"></i>
                                            </button>
                                            <div class="addon-pop">
                                                The Breakdown of Estimated Costs package provides a detailed schedule of
                                                essential works, recommended improvement works, and provisional sums.
                                                Provisional sums cover costs that may be required subject to further
                                                investigation or specialist input. This add-on gives you clarity on likely
                                                expenditure, helping you budget accurately and plan for both immediate and
                                                future property needs.
                                            </div>
                                        </div>

                                        <div class="addon-icon">
                                            <img src="{{ asset('assets/user/icons/Estimated cost package.png') }}" alt="Estimated Costs">
                                        </div>

                                        <h4 class="addon-title-strong">Estimated Costs Package</h4>
                                        <div class="addon-price-line">£{{ $price->repair_cost }}</div>

                                        <button type="button" class="addon-btn" data-group="grp-repair">Add</button>

                                        <!-- hidden radios (unchanged) -->
                                        <div id="grp-repair" class="radio-group addon" data-cost="{{ $price->repair_cost }}" style="display:none">
                                            <input type="radio" name="breakdown_of_estimated_repair_costs" value="0" checked>
                                            <input type="radio" name="breakdown_of_estimated_repair_costs" value="1">
                                        </div>
                                    </article>

                                    <!-- Drone Package -->
                                    <article class="addon-card">
                                        <div class="addon-info">
                                            <span></span>
                                            <button type="button" class="info-btn" aria-label="More info">
                                                <i class="fa-solid fa-info"></i>
                                            </button>
                                            <div class="addon-pop">
                                                The Drone Package provides high-level aerial photographs of the roof and
                                                other hard-to-reach areas of the property. These images supplement your
                                                survey by offering a clear visual record of the condition of roof coverings,
                                                chimneys, gutters, and other external features that may not be fully visible from
                                                ground level. This add-on enhances your report but does not constitute a full
                                                stand-alone roof survey
                                            </div>
                                        </div>

                                        <div class="addon-icon">
                                            <img src="{{ asset('assets/user/icons/dron.gif') }}" alt="Drone Package">
                                        </div>

                                        <h4 class="addon-title-strong">Aerial Drone <br> Package</h4>
                                        <div class="addon-price-line">£{{ $price->aerial_chimney_cost }}</div>

                                        <button type="button" class="addon-btn" data-group="grp-drone">Add</button>

                                        <div id="grp-drone" class="radio-group addon" data-cost="{{ $price->aerial_chimney_cost }}" style="display:none">
                                            <input type="radio" name="aerial_roof_and_chimney" value="0" checked>
                                            <input type="radio" name="aerial_roof_and_chimney" value="1">
                                        </div>
                                    </article>

                                    <!-- Reinstatement Package -->
                                    <article class="addon-card">
                                        <div class="addon-info">
                                            <span></span>
                                            <button type="button" class="info-btn" aria-label="More info">
                                                <i class="fa-solid fa-info"></i>
                                            </button>
                                            <div class="addon-pop">
                                                The Reinstatement Cost package provides an insurance reinstatement
                                                valuation, assessing the estimated cost of rebuilding the property in the event
                                                of total loss. This figure is essential for arranging the correct level of buildings
                                                insurance and helps ensure you are neither under-insured nor overpaying on
                                                your premiums.
                                            </div>
                                        </div>

                                        <div class="addon-icon">
                                            <img src="{{ asset('assets/user/icons/Reinstatement cost package.png') }}" alt="Reinstatement Package">
                                        </div>

                                        <h4 class="addon-title-strong">Reinstatement Cost Package</h4>
                                        <div class="addon-price-line">£{{ $price->insurance_cost }}</div>

                                        <button type="button" class="addon-btn" data-group="grp-ins">Add</button>

                                        <div id="grp-ins" class="radio-group addon" data-cost="{{ $price->insurance_cost }}" style="display:none">
                                            <input type="radio" name="insurance_reinstatement_valuation" value="0" checked>
                                            <input type="radio" name="insurance_reinstatement_valuation" value="1">
                                        </div>
                                    </article>

                                </div><!-- /.addons-grid -->

                                <!-- Savings + total + proceed -->
                                <div class="addons-bottom-bar">
                                    <div class="abb-left">
                                        <div class="level4-all-inlcude-addons" style="display:none">
                                            <span class="save-copy">Add all three and save <span class="level-price">£250</span></span>
                                            <span class="save-price hidden">£250</span>
                                        </div>
                                    </div>

                                    <div class="abb-center">
                                        <div class="level3-price level-price addons" id="total_with_addon" data-total="">
                                            <span class="label">Total Cost:</span> £{{ $survey->level3_price }}
                                        </div>
                                    </div>

                                    <div class="abb-right">
                                        <div class="btns">
                                            <div class="btn-style level-3-confirm buy-now-btn color-fix" data-level="3">
                                                <span class="btn-loader"></span><span class="btn-text">Proceed</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                </div>
        </form>
    </div>

    <!-- Confirm Popup -->
    <div class="confirm-popup-conteiner" id="confirm-popup-conteiner">
        <div class="confirm-popup">
            <div class="confirm-popup-inner">
                <!-- NEW: top-left back arrow -->
                <div class="confirm-popup-back" onclick="goBackFromPopup()" aria-label="Back"><span><i class="fa-solid fa-angle-left"></i></span></div>

                <!-- existing close (X) stays -->
                <!-- <div class="confirm-popup-close" onclick="closePopup()" aria-label="Close"><span><i class="fa-solid fa-xmark"></i></span></div> -->

                <h3 class="confirm-popup-title">Confirm and Proceed</h3>
                <p>Flettons Group LLC will manage your payment and booking through the platform, and your data will be
                    shared with relevant parties involved in the property transaction to deliver the service.</p>
                <p>Your survey will be carried out by <br><b>Flettons Surveyors Ltd – Regulated by RICS.</b></p>
                <p>You will then be taken to an instruction form to complete, where you can review the terms of
                    engagement and finalise your instruction.</p>

                <div class="terms-checkbox">
                    <label>
                        <input type="checkbox" id="termsCheckbox" name="terms_agreed" value="1" required>
                        <div>By continuing, you agree to the process and to the
                            <a href="#" target="_blank">Terms and Conditions</a>.
                        </div>
                    </label>
                </div>

                <div class="btn-style-group">
                    <!-- REMOVED: <div class="btn-style" onclick="closePopup()">Go Back</div> -->
                    <div class="btn-style confirm-yes proceed" onclick="proceedWithBooking()">
                        <span class="btn-loader"></span><span class="btn-text">Proceed</span>
                    </div>
                </div>

                <div class="wait-notice" id="wait-notice">
                    <div class="wait-title">Please Wait</div>
                    <div class="wait-sub">(Please do not click back or refresh the screen)</div>
                </div>
                <p class="muted" style="margin-top:10px">Powered by Flettons Group</p>
            </div>
        </div>
    </div>
    

    <!-- ===== PROMO MODAL ===== -->
    <div class="promo-modal-backdrop" id="promoModal" aria-hidden="true">
      <div class="promo-modal" role="dialog" aria-modal="true" aria-label="January Discount">
    
        <button type="button" class="promo-modal-close" id="promoModalClose" aria-label="Close">
          ✕
        </button>
    
        <!-- Your existing promo content -->
        <!--<a href="" style="text-decoration:none;">-->
          <div class="promo-banner">
    
            <!-- LOGO -->
            <div class="brand-logo">
              <img src="https://flettons.com/wp-content/uploads/2025/04/Flettons-Logo-White-Transparent.png" alt="Flettons Logo">
            </div>
    
            <div class="divider-container">
              <div class="divider-line"></div>
              <div class="discount-subtitle">January Discount!</div>
              <div class="divider-line"></div>
            </div>
    
            <div class="save-text">SAVE <span>10%</span></div>
    
            <div class="discount-info">use discount code</div>
    
            <div class="code-box">JAN10</div>
    
            <div class="terms-text">
              Only valid if you book your survey before<br>
              the end of January.
            </div>
    
          </div>
        <!--</a>-->
    
      </div>
    </div>
    

    
    

    <input type="hidden" name="" id="level1_price" value="{{ $survey->level1_price }}">
    <input type="hidden" name="" id="level2_price" value="{{ $survey->level2_price }}">
    <input type="hidden" name="" id="level3_price" value="{{ $survey->level3_price }}">
    <input type="hidden" name="" id="level4_price" value="{{ $survey->level4_price }}">
    <!-- App JS -->
    <script src="{{ asset('assets/user/custom/js/listing.js') }}"></script>
    <script src="{{ asset('assets/user/custom/js/iframe.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        function goBackFromPopup() {
            // Optional: jey “back” da matlab step-1 dikhana hai
            if (typeof showStep1 === 'function') {
                showStep1();
            }
            // popup close
            if (typeof closePopup === 'function') {
                closePopup();
            } else {
                // safety fallback
                document.getElementById('confirm-popup-conteiner')?.style &&
                    (document.getElementById('confirm-popup-conteiner').style.display = 'none');
            }
        }
    </script>

<style>
  /* make sure the bubble can sit above cards */
  .addon-info { position: relative; }
  .addon-pop  { display: none; z-index: 10; }
  .addon-info.is-open .addon-pop { display: block; }
</style>

<script>
  (function () {
    // Close all open popovers
    function closeAll() {
      document.querySelectorAll('.addon-info.is-open')
        .forEach(n => n.classList.remove('is-open'));
    }

    // Toggle one popover
    function togglePopover(wrapper) {
      const already = wrapper.classList.contains('is-open');
      closeAll();
      if (!already) wrapper.classList.add('is-open');
    }

    // Event delegation: works for both desktop & mobile
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.info-btn');
      const pop = e.target.closest('.addon-pop');

      // Clicked the info button -> toggle its own popover
      if (btn) {
        const wrapper = btn.closest('.addon-info');
        if (wrapper) {
          e.preventDefault();
          e.stopPropagation();
          togglePopover(wrapper);
        }
        return;
      }

      // Clicked *inside* an open popover -> keep it open
      if (pop) {
        e.stopPropagation();
        return;
      }

      // Clicked anywhere else -> close all
      closeAll();
    }, { passive: true });

    // Escape key closes
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeAll();
    });

    // Prevent scroll-jump when tapping inside popover on iOS
    document.addEventListener('touchstart', function (e) {
      if (e.target.closest('.addon-pop')) {
        e.stopPropagation();
      }
    }, { passive: true });
  })();
</script>

<!-- promo model js -->
<!--<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>-->
<!--<script>-->
<!--(function () {-->
<!--  const modal = document.getElementById('promoModal');-->
<!--  const closeBtn = document.getElementById('promoModalClose');-->

<!--  if (!modal || !closeBtn) return;-->

<!--  let scrollY = 0;-->

<!--  function lockScroll() {-->
<!--    scrollY = window.scrollY || document.documentElement.scrollTop;-->
<!--    document.body.style.position = 'fixed';-->
<!--    document.body.style.top = `-${scrollY}px`;-->
<!--    document.body.style.left = '0';-->
<!--    document.body.style.right = '0';-->
<!--    document.body.style.width = '100%';-->
<!--  }-->

<!--  function unlockScroll() {-->
<!--    document.body.style.position = '';-->
<!--    document.body.style.top = '';-->
<!--    document.body.style.left = '';-->
<!--    document.body.style.right = '';-->
<!--    document.body.style.width = '';-->
<!--    window.scrollTo(0, scrollY);-->
<!--  }-->

<!--  function fireConfettiBombOnce() {-->
<!--    if (typeof confetti !== 'function') return;-->
<!--    if (sessionStorage.getItem('confettiBombFired')) return;-->

<!--    sessionStorage.setItem('confettiBombFired', '1');-->

<!--    const myConfetti = confetti.create(undefined, {-->
<!--      resize: true,-->
<!--      useWorker: true-->
<!--    });-->

<!--    myConfetti({-->
<!--      particleCount: 220,-->
<!--      spread: 90,-->
<!--      startVelocity: 55,-->
<!--      gravity: 1.2,-->
<!--      scalar: 0.9,-->
<!--      ticks: 160,-->
<!--      origin: { x: 0.5, y: 0.65 },-->
<!--      colors: [-->
<!--        '#C1EC4A',-->
<!--        '#00E5FF',-->
<!--        '#FF1744',-->
<!--        '#FFD600',-->
<!--        '#7C4DFF',-->
<!--        '#FFFFFF'-->
<!--      ],-->
<!--      shapes: ['square'],-->
<!--    });-->
<!--  }-->

<!--  function openModal() {-->
<!--    modal.classList.add('is-open');-->
<!--    modal.setAttribute('aria-hidden', 'false');-->
<!--    lockScroll();-->
<!--    fireConfettiBombOnce();-->
<!--  }-->

<!--  function closeModal() {-->
<!--    modal.classList.remove('is-open');-->
<!--    modal.setAttribute('aria-hidden', 'true');-->
<!--    unlockScroll();-->
<!--  }-->

<!--  function getUKParts(date = new Date()) {-->
<!--    const parts = new Intl.DateTimeFormat('en-GB', {-->
<!--      timeZone: 'Europe/London',-->
<!--      year: 'numeric',-->
<!--      month: '2-digit',-->
<!--      day: '2-digit',-->
<!--      hour: '2-digit',-->
<!--      minute: '2-digit',-->
<!--      second: '2-digit',-->
<!--      hour12: false-->
<!--    }).formatToParts(date);-->

<!--    const map = {};-->
<!--    parts.forEach(p => { if (p.type !== 'literal') map[p.type] = p.value; });-->

<!--    return {-->
<!--      year: +map.year,-->
<!--      month: +map.month,-->
<!--      day: +map.day,-->
<!--      hour: +map.hour,-->
<!--      minute: +map.minute,-->
<!--      second: +map.second,-->
<!--    };-->
<!--  }-->

<!--  function isAfterUKCutoff() {-->
<!--    const uk = getUKParts();-->
<!--    const cutoffKey = uk.year * 1e10 + 1 * 1e8 + 31 * 1e6 + 23 * 1e4 + 59 * 1e2 + 59;-->
<!--    const nowKey =-->
<!--      uk.year * 1e10 + uk.month * 1e8 + uk.day * 1e6 + uk.hour * 1e4 + uk.minute * 1e2 + uk.second;-->

<!--    return nowKey > cutoffKey;-->
<!--  }-->

<!--  window.addEventListener('load', function () {-->
<!--    if (isAfterUKCutoff()) return;-->

<!--    const key = 'promoModalSeen_JanDiscount';-->
<!--    if (!sessionStorage.getItem(key)) {-->
<!--      setTimeout(() => {-->
<!--        if (isAfterUKCutoff()) return;-->
<!--        openModal();-->
<!--        sessionStorage.setItem(key, '1');-->
<!--      }, 2000);-->
<!--    }-->
<!--  });-->

<!--  closeBtn.addEventListener('click', closeModal);-->

<!--  modal.addEventListener('click', function (e) {-->
<!--    if (e.target === modal) closeModal();-->
<!--  });-->

<!--  document.addEventListener('keydown', function (e) {-->
<!--    if (e.key === 'Escape') closeModal();-->
<!--  });-->
<!--})();-->
<!--</script>-->


</body>

</html>
