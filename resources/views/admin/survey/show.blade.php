{{-- resources/views/admin/survey/show.blade.php --}}
@extends('layouts.admin')

@section('style')
<style>
    label, .form-label {
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #1b202b !important;
        margin-bottom: 6px;
        margin-top: 20px;
    }
    .price-section {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 18px 18px 10px;
        margin-bottom: 18px;
        background: #fff;
    }
    .price-section__title {
        font-size: 16px;
        font-weight: 700;
        color: #1b202b;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .price-section__divider {
        height: 1px;
        background: #eef1f5;
        margin: 10px 0 16px;
    }
    .form-control {
        height: 44px;
        border-radius: 10px;
    }
    textarea.form-control {
        min-height: 80px;
        height: auto;
    }
    select.form-control {
        height: 44px;
    }
    .survey-info-row {
        display: flex;
        flex-wrap: wrap;
        gap: 24px 32px;
        align-items: center;
    }
    .survey-info-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .survey-info-item .survey-info-label {
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
    }
    .survey-info-item .survey-info-value {
        color: #1b202b;
        font-size: 14px;
        font-weight: 600;
    }
    .badge-submitted {
        background: rgba(34, 197, 94, .15);
        color: #16a34a;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
    }
    .badge-progress {
        background: rgba(234, 179, 8, .15);
        color: #ca8a04;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
    }
</style>
@endsection

@section('content')
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if (!$survey)
            <div class="alert alert-warning">Survey not found.</div>
        @else
            @php $s = $survey; @endphp

            <div class="card">
                <h5 class="card-header" style="font-size: 22px !important; font-weight: 600 !important; color: #1b202b !important;">Detail Survey</h5>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.survey.update', $s->id) }}">
                        @csrf

                        {{-- Survey info (ID, Step, Status) --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Survey info</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="survey-info-row">
                                <div class="survey-info-item">
                                    <span class="survey-info-label">ID</span>
                                    <span class="survey-info-value">{{ $s->id }}</span>
                                </div>
                                <div class="survey-info-item">
                                    <span class="survey-info-label">Step</span>
                                    <span class="survey-info-value">{{ $s->current_step }}</span>
                                </div>
                                <div class="survey-info-item">
                                    <span class="survey-info-label">Status</span>
                                    @if($s->is_submitted)
                                        <span class="survey-info-value badge-submitted">Submitted</span>
                                    @else
                                        <span class="survey-info-value badge-progress">In Progress</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Step 0: Quote form --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 0 — Quote form (initial details)</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First name</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $s->first_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last name</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $s->last_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email_address" class="form-control" value="{{ old('email_address', $s->email_address) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telephone</label>
                                    <input type="text" name="telephone_number" class="form-control" value="{{ old('telephone_number', $s->telephone_number) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Full address</label>
                                    <input type="text" name="full_address" class="form-control" value="{{ old('full_address', $s->full_address) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Postcode</label>
                                    <input type="text" name="postcode" class="form-control" value="{{ old('postcode', $s->postcode) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">House or flat</label>
                                    <input type="text" name="house_or_flat" class="form-control" value="{{ old('house_or_flat', $s->house_or_flat) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Number of bedrooms</label>
                                    <input type="number" name="number_of_bedrooms" class="form-control" value="{{ old('number_of_bedrooms', $s->number_of_bedrooms) }}" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Market value (GBP)</label>
                                    <input type="number" name="market_value" class="form-control" value="{{ old('market_value', $s->market_value) }}" min="0" step="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Listed building</label>
                                    <input type="text" name="listed_building" class="form-control" value="{{ old('listed_building', $s->listed_building) }}" placeholder="yes / no">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Over 1650 sqft</label>
                                    <input type="text" name="over1650" class="form-control" value="{{ old('over1650', $s->over1650) }}" placeholder="yes / no">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Area (sqft)</label>
                                    <input type="number" name="sqft_area" class="form-control" value="{{ old('sqft_area', $s->sqft_area) }}" min="0">
                                </div>
                            </div>
                        </div>

                        {{-- Step 1: Package selection --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 1 — Package selection</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Selected Level</label>
                                    <input type="text" name="level" class="form-control" value="{{ old('level', $s->level) }}" placeholder="1, 2, 3 or 4">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Level total</label>
                                    <input type="number" name="level_total" class="form-control" value="{{ old('level_total', $s->level_total) }}" min="0" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Level 1 price</label>
                                    <input type="number" name="level1_price" class="form-control" value="{{ old('level1_price', $s->level1_price) }}" min="0" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Level 2 price</label>
                                    <input type="number" name="level2_price" class="form-control" value="{{ old('level2_price', $s->level2_price) }}" min="0" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Level 3 price</label>
                                    <input type="number" name="level3_price" class="form-control" value="{{ old('level3_price', $s->level3_price) }}" min="0" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Level 4 price</label>
                                    <input type="number" name="level4_price" class="form-control" value="{{ old('level4_price', $s->level4_price) }}" min="0" step="0.01">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Quote summary page URL</label>
                                    <input type="url" name="quote_summary_page" class="form-control" value="{{ old('quote_summary_page', $s->quote_summary_page) }}" placeholder="https://">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Breakdown</label>
                                    <select name="breakdown" class="form-control">
                                        <option value="">—</option>
                                        <option value="1" {{ old('breakdown', $s->breakdown) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('breakdown', $s->breakdown) == 0 && $s->breakdown !== null ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Aerial</label>
                                    <select name="aerial" class="form-control">
                                        <option value="">—</option>
                                        <option value="1" {{ old('aerial', $s->aerial) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('aerial', $s->aerial) == 0 && $s->aerial !== null ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Insurance</label>
                                    <select name="insurance" class="form-control">
                                        <option value="">—</option>
                                        <option value="1" {{ old('insurance', $s->insurance) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('insurance', $s->insurance) == 0 && $s->insurance !== null ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Addons</label>
                                    <select name="addons" class="form-control">
                                        <option value="">—</option>
                                        <option value="1" {{ old('addons', $s->addons) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('addons', $s->addons) == 0 && $s->addons !== null ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3A: Client identity --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 3A — Client identity & contact</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="inf_field_Title" class="form-control" value="{{ old('inf_field_Title', $s->inf_field_Title) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">First name</label>
                                    <input type="text" name="inf_field_FirstName" class="form-control" value="{{ old('inf_field_FirstName', $s->inf_field_FirstName) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last name</label>
                                    <input type="text" name="inf_field_LastName" class="form-control" value="{{ old('inf_field_LastName', $s->inf_field_LastName) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="inf_field_Email" class="form-control" value="{{ old('inf_field_Email', $s->inf_field_Email) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="inf_field_Phone1" class="form-control" value="{{ old('inf_field_Phone1', $s->inf_field_Phone1) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Step 3B: Billing address --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 3B — Home/Billing address</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Street Address 1</label>
                                    <input type="text" name="inf_field_StreetAddress1" class="form-control" value="{{ old('inf_field_StreetAddress1', $s->inf_field_StreetAddress1) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Street Address 2</label>
                                    <input type="text" name="inf_field_StreetAddress2" class="form-control" value="{{ old('inf_field_StreetAddress2', $s->inf_field_StreetAddress2) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="inf_field_PostalCode" class="form-control" value="{{ old('inf_field_PostalCode', $s->inf_field_PostalCode) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Step 3C: Survey property address --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 3C — Survey property address</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Address2 Street1</label>
                                    <input type="text" name="inf_field_Address2Street1" class="form-control" value="{{ old('inf_field_Address2Street1', $s->inf_field_Address2Street1) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Postal Code 2</label>
                                    <input type="text" name="inf_field_PostalCode2" class="form-control" value="{{ old('inf_field_PostalCode2', $s->inf_field_PostalCode2) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Property Link</label>
                                    <input type="url" name="inf_custom_PropertyLink" class="form-control" value="{{ old('inf_custom_PropertyLink', $s->inf_custom_PropertyLink) }}" placeholder="https://">
                                </div>
                            </div>
                        </div>

                        {{-- Step 3E: Property features --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 3E — Property features & concerns</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Vacant or Occupied</label>
                                    <input type="text" name="inf_custom_VacantorOccupied" class="form-control" value="{{ old('inf_custom_VacantorOccupied', $s->inf_custom_VacantorOccupied) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Any Extensions</label>
                                    <input type="text" name="inf_custom_AnyExtensions" class="form-control" value="{{ old('inf_custom_AnyExtensions', $s->inf_custom_AnyExtensions) }}" placeholder="yes / no">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Garage</label>
                                    <input type="text" name="inf_custom_Garage" class="form-control" value="{{ old('inf_custom_Garage', $s->inf_custom_Garage) }}" placeholder="yes / no">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Garage Location</label>
                                    <input type="text" name="inf_custom_GarageLocation" class="form-control" value="{{ old('inf_custom_GarageLocation', $s->inf_custom_GarageLocation) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Garden</label>
                                    <input type="text" name="inf_custom_Garden" class="form-control" value="{{ old('inf_custom_Garden', $s->inf_custom_Garden) }}" placeholder="yes / no">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Garden Location</label>
                                    <input type="text" name="inf_custom_GardenLocation" class="form-control" value="{{ old('inf_custom_GardenLocation', $s->inf_custom_GardenLocation) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Specific Concerns</label>
                                    <textarea name="inf_custom_SpecificConcerns" class="form-control" rows="3">{{ old('inf_custom_SpecificConcerns', $s->inf_custom_SpecificConcerns) }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3F: Solicitor --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 3F — Solicitor details</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Solicitor Firm</label>
                                    <input type="text" name="inf_custom_SolicitorFirm" class="form-control" value="{{ old('inf_custom_SolicitorFirm', $s->inf_custom_SolicitorFirm) }}" placeholder="yes / no">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Firm Name</label>
                                    <input type="text" name="inf_custom_SolicitorFirmName" class="form-control" value="{{ old('inf_custom_SolicitorFirmName', $s->inf_custom_SolicitorFirmName) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Conveyancer Name</label>
                                    <input type="text" name="inf_custom_ConveyancerName" class="form-control" value="{{ old('inf_custom_ConveyancerName', $s->inf_custom_ConveyancerName) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="inf_custom_SolicitorPhoneNumber1" class="form-control" value="{{ old('inf_custom_SolicitorPhoneNumber1', $s->inf_custom_SolicitorPhoneNumber1) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="inf_custom_SolicitorsEmail" class="form-control" value="{{ old('inf_custom_SolicitorsEmail', $s->inf_custom_SolicitorsEmail) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="inf_custom_SolicitorAddress" class="form-control" value="{{ old('inf_custom_SolicitorAddress', $s->inf_custom_SolicitorAddress) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="inf_custom_SolicitorPostalCode" class="form-control" value="{{ old('inf_custom_SolicitorPostalCode', $s->inf_custom_SolicitorPostalCode) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Step 3G: Exchange --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 3G — Exchange timeline</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Exchange date known?</label>
                                    <input type="text" name="inf_custom_exchange_known" class="form-control" value="{{ old('inf_custom_exchange_known', $s->inf_custom_exchange_known) }}" placeholder="yes / no">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Exchange Date</label>
                                    <input type="text" name="inf_custom_ExchangeDate" class="form-control" value="{{ old('inf_custom_ExchangeDate', $s->inf_custom_ExchangeDate) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Step 3H: Agent --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 3H — Estate agent details</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company</label>
                                    <input type="text" name="inf_custom_AgentCompanyName" class="form-control" value="{{ old('inf_custom_AgentCompanyName', $s->inf_custom_AgentCompanyName) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Agent Name</label>
                                    <input type="text" name="inf_custom_AgentName" class="form-control" value="{{ old('inf_custom_AgentName', $s->inf_custom_AgentName) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="inf_custom_AgentPhoneNumber" class="form-control" value="{{ old('inf_custom_AgentPhoneNumber', $s->inf_custom_AgentPhoneNumber) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="inf_custom_AgentsEmail" class="form-control" value="{{ old('inf_custom_AgentsEmail', $s->inf_custom_AgentsEmail) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Address3 Street1</label>
                                    <input type="text" name="inf_field_Address3Street1" class="form-control" value="{{ old('inf_field_Address3Street1', $s->inf_field_Address3Street1) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Postal Code 3</label>
                                    <input type="text" name="inf_field_PostalCode3" class="form-control" value="{{ old('inf_field_PostalCode3', $s->inf_field_PostalCode3) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Step 3I & Tracking --}}
                        <div class="price-section">
                            <div class="price-section__title">
                                <span>Step 3I & Tracking</span>
                            </div>
                            <div class="price-section__divider"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Terms accepted</label>
                                    <input type="text" name="inf_option_IconfirmthatIhavereadandunderstandtheterms" class="form-control" value="{{ old('inf_option_IconfirmthatIhavereadandunderstandtheterms', $s->inf_option_IconfirmthatIhavereadandunderstandtheterms) }}" placeholder="yes / no">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Current step</label>
                                    <input type="number" name="current_step" class="form-control" value="{{ old('current_step', $s->current_step) }}" min="0" max="3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Is submitted</label>
                                    <select name="is_submitted" class="form-control">
                                        <option value="0" {{ old('is_submitted', $s->is_submitted) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('is_submitted', $s->is_submitted) == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact ID (Keap)</label>
                                    <input type="text" name="contact_id" class="form-control" value="{{ old('contact_id', $s->contact_id) }}" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Signature (URL)</label>
                                    <input type="text" name="inf_custom_infcustomSignature" class="form-control" value="{{ old('inf_custom_infcustomSignature', $s->inf_custom_infcustomSignature) }}" placeholder="Image URL">
                                </div>
                                @if($s->inf_custom_infcustomSignature && filter_var($s->inf_custom_infcustomSignature, FILTER_VALIDATE_URL))
                                    <div class="col-12">
                                        <label class="form-label">Signature preview</label>
                                        <div><img src="{{ $s->inf_custom_infcustomSignature }}" alt="Signature" style="max-height:80px; border-radius: 10px;"></div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label">Created at</label>
                                    <div class="form-control-plaintext text-muted" style="height: auto; margin-top: 0;">{{ $s->created_at ? \Carbon\Carbon::parse($s->created_at)->format('d M Y H:i') : '—' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Updated at</label>
                                    <div class="form-control-plaintext text-muted" style="height: auto; margin-top: 0;">{{ $s->updated_at ? \Carbon\Carbon::parse($s->updated_at)->format('d M Y H:i') : '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('admin.survey.index') }}" class="btn btn-outline-secondary" style="border-radius: 4px;">← Back to list</a>
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 4px !important;">Update Survey</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
