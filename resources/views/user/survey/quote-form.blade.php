<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RICS Survey Quote</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/user/custom/css/quote.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7xLp13hLBGIDOt4BIJZrJF99ItTsya0g&libraries=places&callback=initAddressAutocomplete"
        defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

</head>

<body style="overflow-x: hidden; font-family: lato !important">
    <div class="screen-center">
        <div class="form-container">
            <!-- Brand Header -->
            <div class="brand-header">
                <img src="https://flettons.group/wp-content/uploads/2025/05/Flettons-Logo-White-Transparent.png"
                    alt="Flettons Group" class="brand-logo" loading="eager" decoding="async" />
            </div>
            <h2>RICS Survey Quote</h2>
            <form id="quoteForm" method="POST" action="{{ route('user.survey.submit') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-row">
                        <div>
                            <input type="text" name="first_name" placeholder="First Name" required />
                        </div>
                        <div>
                            <input type="text" name="last_name" placeholder="Last Name" required />
                        </div>
                    </div>
                    <div class="form-row">
                        <div>
                            <input type="email" name="email_address" placeholder="Email Address" required />
                        </div>
                        <div class="telephone-field">
                            <input class="tel-input" type="tel" id="telephone_number" name="telephone_number"
                                required />
                        </div>
                    </div>
                    <div>
                        <input type="text" id="full_address" name="full_address" placeholder="Property Address"
                            required />
                        <input type="hidden" id="postcode" name="postcode" placeholder="Postcode" />
                    </div>
                    <div class="form-row">
                        <div>
                            <select name="house_or_flat" required placeholder="Property Type">
                                <option value="">Property Type</option>
                                <option>House</option>
                                <option>Flat</option>
                                <option>Maisonette</option>
                                <option>Barn Conversion</option>
                                <option>Warehouse Conversion</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div>
                            <select name="number_of_bedrooms" required placeholder="Bedrooms">
                                <option value="">Bedrooms</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <input type="number" id="market_value" name="market_value" min="100000" max="6000000"
                            step="1" placeholder="Market Value (£)" required />
                    </div>
                </div>
                <div class="switch-group">
                    <div class="switch-option">
                        <label class="switch-label" for="listed">Listed Building?</label>
                        <label class="switch">
                            <input type="checkbox" id="listed" name="listed_building" value="yes" />
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="switch-option">
                        <label class="switch-label" for="over1650">Over 1650 sqft?</label>
                        <label class="switch">
                            <input type="checkbox" id="over1650" name="over1650" value="yes" onchange="toggleSqftAreaBox()" />
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div id="sqftPriceBox" style="display: none">
                        <input type="number" id="sqft_area" name="sqft_area" placeholder="Floor Area (sqft)"
                            min="1651" max="9999" step="1" />
                    </div>
                </div>
                <!-- Hidden fields (if needed by backend) -->
                <input type="hidden" name="action" value="process_quote_form" />
                <input type="hidden" name="quote_form_nonce" value="quote_form_nonce_placeholder" />
                <div class="buttons">
                    <button type="" id="proceedBtn">GET INSTANT QUOTE</button>
                    <div class="loading-spinner" id="loading">
                        <svg viewBox="0 0 50 50">
                            <circle cx="25" cy="25" r="20"></circle>
                        </svg>
                    </div>
                </div>
            </form>
            <div class="footer">
                <div class="footer-text">
                    <input type="checkbox" id="agree_terms" name="agree_terms" required />
                    <label for="agree_terms">Your details are never shared with third parties. Powered by
                        <b>Flettons Group</b></label>
                </div>
            </div>
        </div>
    </div>

{{-- loading --}}
  <div class="overlay" style="display: none" id="overlay">
        <img src="{{ asset('assets/user/icons/Loading.png') }}" class="loading-image" alt="Loading...">
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- intl-tel-input JS + utils -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
    <!-- App JS -->
    <script src="{{ asset('assets/user/custom/js/telephone.js') }}"></script>
    <script src="{{ asset('assets/user/custom/js/quote.js') }}"></script>
    <script src="{{ asset('assets/user/custom/js/iframe.js') }}"></script>



</body>

</html>
